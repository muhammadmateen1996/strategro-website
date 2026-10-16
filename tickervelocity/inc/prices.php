<?php
/**
 * Live prices for the cards, the market strip and the markets tables.
 *
 *  - Crypto from CoinGecko (free, no account needed; a free demo key makes
 *    it more reliable), with 7-day sparklines.
 *  - Stocks, index funds and commodity funds from Finnhub (free key).
 *  - Anything else can be sent to POST /wp-json/tv/v1/market by any tool.
 *
 * Prices are fetched by the server at most every five minutes and kept, so
 * visitor numbers never multiply API calls, and pages never wait on an API.
 *
 * @package TickerVelocity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TV_PRICE_TTL = 5 * MINUTE_IN_SECONDS;

/**
 * Parses a Customizer list: "SYMBOL:id-or-name:Name, ..." into
 * array( SYMBOL => array( 'id', 'name' ) ).
 */
function tv_parse_assets( $list, $with_id ) {
	$assets = array();
	foreach ( explode( ',', (string) $list ) as $entry ) {
		$parts = array_map( 'trim', explode( ':', $entry ) );
		if ( '' === $parts[0] ) {
			continue;
		}
		$symbol            = strtoupper( $parts[0] );
		$assets[ $symbol ] = array(
			'id'   => $with_id ? ( isset( $parts[1] ) ? sanitize_key( $parts[1] ) : '' ) : $symbol,
			'name' => $with_id ? ( isset( $parts[2] ) ? $parts[2] : '' ) : ( isset( $parts[1] ) ? $parts[1] : $symbol ),
		);
	}
	return $assets;
}

/** Every asset the site tracks: SYMBOL => array( id, name, group ). */
function tv_watchlist() {
	$list = array();
	foreach ( tv_parse_assets( tv_mod( 'tv_crypto_assets' ), true ) as $symbol => $asset ) {
		$list[ $symbol ] = $asset + array( 'group' => 'crypto' );
	}
	foreach ( array( 'stocks' => 'tv_stock_assets', 'commodities' => 'tv_commodity_assets' ) as $group => $key ) {
		foreach ( tv_parse_assets( tv_mod( $key ), false ) as $symbol => $asset ) {
			$list[ $symbol ] = $asset + array( 'group' => $group );
		}
	}
	return $list;
}

function tv_price_groups() {
	return array(
		'crypto'      => __( 'Crypto', 'tickervelocity' ),
		'stocks'      => __( 'Stocks', 'tickervelocity' ),
		'commodities' => __( 'Commodities', 'tickervelocity' ),
		'markets'     => __( 'Indices', 'tickervelocity' ),
	);
}

/** Stored prices: array( 'updated' => time, 'assets' => array( SYMBOL => data ) ). */
function tv_prices_raw() {
	$data = get_option( 'tv_prices', array() );
	return array(
		'updated' => isset( $data['updated'] ) ? (int) $data['updated'] : 0,
		'assets'  => isset( $data['assets'] ) && is_array( $data['assets'] ) ? $data['assets'] : array(),
	);
}

/** Refreshes prices if they're older than five minutes (one request at a time). */
function tv_prices_maybe_refresh() {
	$data = tv_prices_raw();
	if ( time() - $data['updated'] < TV_PRICE_TTL || get_transient( 'tv_prices_lock' ) ) {
		return;
	}
	set_transient( 'tv_prices_lock', 1, MINUTE_IN_SECONDS );
	tv_prices_refresh();
	delete_transient( 'tv_prices_lock' );
}

function tv_prices_refresh() {
	$data   = tv_prices_raw();
	$assets = $data['assets'];
	$watch  = tv_watchlist();

	// Drop assets that were removed from the lists (keep pushed ones).
	foreach ( $assets as $symbol => $asset ) {
		if ( 'push' !== ( isset( $asset['source'] ) ? $asset['source'] : '' ) && ! isset( $watch[ $symbol ] ) ) {
			unset( $assets[ $symbol ] );
		}
	}

	foreach ( tv_fetch_crypto( $watch ) as $symbol => $asset ) {
		$assets[ $symbol ] = $asset;
	}
	foreach ( tv_fetch_finnhub( $watch ) as $symbol => $asset ) {
		$previous = isset( $assets[ $symbol ]['spark'] ) ? (array) $assets[ $symbol ]['spark'] : array();
		$last     = $previous ? end( $previous ) : null;
		if ( null === $last || abs( $last - $asset['price'] ) > 1e-9 ) {
			$previous[] = $asset['price']; // Finnhub's free plan has no history: build our own intraday line.
		}
		$asset['spark']    = array_slice( $previous, -48 );
		$assets[ $symbol ] = $asset;
	}

	update_option(
		'tv_prices',
		array(
			'updated' => time(),
			'assets'  => $assets,
		),
		false
	);
}

function tv_fetch_crypto( $watch ) {
	$ids = array();
	foreach ( $watch as $symbol => $asset ) {
		if ( 'crypto' === $asset['group'] && $asset['id'] ) {
			$ids[ $asset['id'] ] = $symbol;
		}
	}
	if ( ! $ids ) {
		return array();
	}
	$key = tv_mod( 'tv_coingecko_key' );
	$res = wp_remote_get(
		add_query_arg(
			array(
				'vs_currency'             => 'usd',
				'ids'                     => implode( ',', array_keys( $ids ) ),
				'sparkline'               => 'true',
				'price_change_percentage' => '24h',
			),
			'https://api.coingecko.com/api/v3/coins/markets'
		),
		array(
			'timeout' => 5,
			'headers' => $key ? array( 'x-cg-demo-api-key' => $key ) : array(),
		)
	);
	if ( is_wp_error( $res ) || 200 !== wp_remote_retrieve_response_code( $res ) ) {
		return array();
	}
	$rows = json_decode( wp_remote_retrieve_body( $res ), true );
	$out  = array();
	foreach ( is_array( $rows ) ? $rows : array() as $row ) {
		if ( empty( $row['id'] ) || ! isset( $ids[ $row['id'] ], $row['current_price'] ) ) {
			continue;
		}
		$symbol         = $ids[ $row['id'] ];
		$spark          = isset( $row['sparkline_in_7d']['price'] ) ? (array) $row['sparkline_in_7d']['price'] : array();
		$out[ $symbol ] = array(
			'symbol' => $symbol,
			'name'   => $watch[ $symbol ]['name'] ? $watch[ $symbol ]['name'] : ( isset( $row['name'] ) ? $row['name'] : $symbol ),
			'group'  => 'crypto',
			'price'  => (float) $row['current_price'],
			'change' => isset( $row['price_change_percentage_24h'] ) ? (float) $row['price_change_percentage_24h'] : 0.0,
			'high'   => isset( $row['high_24h'] ) ? (float) $row['high_24h'] : 0.0,
			'low'    => isset( $row['low_24h'] ) ? (float) $row['low_24h'] : 0.0,
			'spark'  => tv_downsample( $spark, 42 ),
			'span'   => '7d',
			'prefix' => '$',
			'source' => 'coingecko',
		);
	}
	return $out;
}

function tv_fetch_finnhub( $watch ) {
	$key = trim( (string) tv_mod( 'tv_finnhub_key' ) );
	if ( ! $key ) {
		return array();
	}
	$symbols = array();
	foreach ( $watch as $symbol => $asset ) {
		if ( 'crypto' !== $asset['group'] ) {
			$symbols[] = $symbol;
		}
	}
	$symbols = array_slice( $symbols, 0, 30 ); // Free plan: 60 calls a minute.
	$out     = array();
	foreach ( $symbols as $symbol ) {
		$res = wp_remote_get(
			add_query_arg(
				array(
					'symbol' => rawurlencode( $symbol ),
					'token'  => $key,
				),
				'https://finnhub.io/api/v1/quote'
			),
			array( 'timeout' => 4 )
		);
		if ( is_wp_error( $res ) || 200 !== wp_remote_retrieve_response_code( $res ) ) {
			continue;
		}
		$q = json_decode( wp_remote_retrieve_body( $res ), true );
		if ( ! is_array( $q ) || empty( $q['c'] ) ) {
			continue; // Unknown symbol or not on the free plan.
		}
		$out[ $symbol ] = array(
			'symbol' => $symbol,
			'name'   => $watch[ $symbol ]['name'],
			'group'  => $watch[ $symbol ]['group'],
			'price'  => (float) $q['c'],
			'change' => isset( $q['dp'] ) ? (float) $q['dp'] : 0.0,
			'high'   => isset( $q['h'] ) ? (float) $q['h'] : 0.0,
			'low'    => isset( $q['l'] ) ? (float) $q['l'] : 0.0,
			'span'   => __( 'today', 'tickervelocity' ),
			'prefix' => '$',
			'source' => 'finnhub',
		);
	}
	return $out;
}

function tv_downsample( $values, $points ) {
	$values = array_values( array_filter( $values, 'is_numeric' ) );
	$count  = count( $values );
	if ( $count <= $points ) {
		return array_map( 'floatval', $values );
	}
	$out = array();
	for ( $i = 0; $i < $points; $i++ ) {
		$out[] = (float) $values[ (int) round( $i * ( $count - 1 ) / ( $points - 1 ) ) ];
	}
	return $out;
}

function tv_format_price( $price, $prefix = '' ) {
	$price    = (float) $price;
	$decimals = $price < 1 ? 4 : ( $price < 1000 ? 2 : 0 );
	return $prefix . number_format( $price, $decimals );
}

/** One asset ready for display. */
function tv_price_view( $asset ) {
	$change = (float) $asset['change'];
	$spark  = isset( $asset['spark'] ) ? array_values( (array) $asset['spark'] ) : array();
	return array(
		'symbol'  => (string) $asset['symbol'],
		'name'    => (string) ( isset( $asset['name'] ) && $asset['name'] ? $asset['name'] : $asset['symbol'] ),
		'group'   => (string) ( isset( $asset['group'] ) ? $asset['group'] : 'markets' ),
		'value'   => (float) $asset['price'],
		'price'   => tv_format_price( $asset['price'], isset( $asset['prefix'] ) ? $asset['prefix'] : '' ),
		'change'  => ( $change > 0 ? '+' : '' ) . number_format( $change, 2 ) . '%',
		'dir'     => $change > 0.005 ? 'up' : ( $change < -0.005 ? 'down' : 'flat' ),
		'high'    => ! empty( $asset['high'] ) ? tv_format_price( $asset['high'], isset( $asset['prefix'] ) ? $asset['prefix'] : '' ) : '',
		'low'     => ! empty( $asset['low'] ) ? tv_format_price( $asset['low'], isset( $asset['prefix'] ) ? $asset['prefix'] : '' ) : '',
		'range'   => ! empty( $asset['high'] ) && $asset['high'] > $asset['low'] ? round( ( $asset['price'] - $asset['low'] ) / ( $asset['high'] - $asset['low'] ) * 100, 1 ) : null,
		'spark'   => count( $spark ) >= 6 ? tv_sparkline_points( $spark ) : '',
		'span'    => isset( $asset['span'] ) ? (string) $asset['span'] : '',
	);
}

/** Sparkline as SVG polyline points in a 120 x 36 box. */
function tv_sparkline_points( $values ) {
	$min   = min( $values );
	$max   = max( $values );
	$range = $max - $min ? $max - $min : 1;
	$step  = 120 / max( 1, count( $values ) - 1 );
	$pts   = array();
	foreach ( $values as $i => $v ) {
		$pts[] = round( $i * $step, 1 ) . ',' . round( 34 - ( $v - $min ) / $range * 32, 1 );
	}
	return implode( ' ', $pts );
}

/** All assets for display, in watchlist order, optionally one group. */
function tv_prices( $group = '' ) {
	$raw   = tv_prices_raw()['assets'];
	$order = array_merge( array_keys( tv_watchlist() ), array_keys( $raw ) );
	$out   = array();
	foreach ( array_unique( $order ) as $symbol ) {
		if ( isset( $raw[ $symbol ] ) && ( ! $group || ( isset( $raw[ $symbol ]['group'] ) && $raw[ $symbol ]['group'] === $group ) ) ) {
			$out[ $symbol ] = tv_price_view( $raw[ $symbol ] );
		}
	}
	return $out;
}

/** Old name kept for the strip and tables. */
function tv_market_snapshot() {
	return array(
		'items'   => array_values( tv_prices() ),
		'updated' => tv_prices_raw()['updated'],
	);
}

/* ---------------------------------------------------------------------------
 * Assets mentioned in an article
 * ------------------------------------------------------------------------ */

/** Names and symbols to look for, per asset. */
function tv_asset_aliases( $symbol, $asset ) {
	$aliases = array( $symbol );
	$name    = preg_replace( '/\s*\(.*?\)\s*/', '', (string) $asset['name'] );
	if ( $name && strtoupper( $name ) !== $symbol ) {
		$aliases[] = $name;
	}
	$extra = array(
		'BTC'  => array( 'Bitcoin' ),
		'ETH'  => array( 'Ethereum', 'Ether' ),
		'SOL'  => array( 'Solana' ),
		'XRP'  => array( 'Ripple' ),
		'DOGE' => array( 'Dogecoin' ),
		'ADA'  => array( 'Cardano' ),
		'SPY'  => array( 'S&P 500', 'S&amp;P 500' ),
		'QQQ'  => array( 'Nasdaq' ),
		'DIA'  => array( 'Dow Jones' ),
		'GLD'  => array( 'gold' ),
		'SLV'  => array( 'silver' ),
		'USO'  => array( 'oil', 'crude', 'Brent', 'WTI' ),
		'UNG'  => array( 'natural gas' ),
	);
	$all    = isset( $extra[ $symbol ] ) ? array_merge( $aliases, $extra[ $symbol ] ) : $aliases;
	$unique = array();
	foreach ( $all as $alias ) {
		$unique[ strtolower( $alias ) ] = $alias; // Count each name once.
	}
	return array_values( $unique );
}

/** Symbols of tracked assets mentioned in a post, most mentioned first. */
function tv_assets_in_post( $post = null, $limit = 4 ) {
	$post   = get_post( $post );
	$manual = wp_list_pluck( tv_tickers( $post->ID ), 'symbol' );
	$prices = tv_prices();
	$scores = array();
	foreach ( $manual as $i => $symbol ) {
		if ( isset( $prices[ $symbol ] ) ) {
			$scores[ $symbol ] = 1000 - $i;
		}
	}
	$title = ' ' . wp_strip_all_tags( get_the_title( $post ) ) . ' ';
	$body  = ' ' . wp_strip_all_tags( strip_shortcodes( $post->post_content ) ) . ' ';
	foreach ( tv_watchlist() as $symbol => $asset ) {
		if ( ! isset( $prices[ $symbol ] ) || isset( $scores[ $symbol ] ) ) {
			continue;
		}
		$score = 0;
		foreach ( tv_asset_aliases( $symbol, $asset ) as $alias ) {
			// Tickers must match in capitals ("SOL", not "sol"); names in any case.
			$flags = strtoupper( $alias ) === $alias ? 'u' : 'iu';
			$re    = '/(?<![\p{L}\p{N}$])\$?' . preg_quote( $alias, '/' ) . '(?![\p{L}\p{N}])/' . $flags;
			$score += 3 * preg_match_all( $re, $title ) + min( 5, preg_match_all( $re, $body ) );
		}
		if ( $score >= 2 ) {
			$scores[ $symbol ] = $score;
		}
	}
	arsort( $scores );
	return array_slice( array_keys( $scores ), 0, $limit );
}

/* ---------------------------------------------------------------------------
 * REST and scheduling
 * ------------------------------------------------------------------------ */

add_action( 'rest_api_init', 'tv_price_routes' );
function tv_price_routes() {
	register_rest_route(
		'tv/v1',
		'/prices',
		array(
			'methods'             => 'GET',
			'permission_callback' => '__return_true',
			'callback'            => function () {
				tv_prices_maybe_refresh();
				$response = rest_ensure_response(
					array(
						'updated' => tv_prices_raw()['updated'],
						'assets'  => array_values( tv_prices() ),
					)
				);
				$response->header( 'Cache-Control', 'public, max-age=60' );
				return $response;
			},
		)
	);
	register_rest_route(
		'tv/v1',
		'/market',
		array(
			'methods'             => 'POST',
			'permission_callback' => function () {
				return current_user_can( 'edit_others_posts' );
			},
			'callback'            => 'tv_market_push',
		)
	);
}

/**
 * Optional: any tool can send its own prices (for example real index
 * levels): { "items": [ { "symbol": "S&P 500", "price": 5321.4, "change": 0.62,
 * "group": "markets" } ] }. Pushed items stay until pushed again.
 */
function tv_market_push( WP_REST_Request $request ) {
	$data  = tv_prices_raw();
	$count = 0;
	foreach ( $data['assets'] as $symbol => $asset ) {
		if ( isset( $asset['source'] ) && 'push' === $asset['source'] ) {
			unset( $data['assets'][ $symbol ] );
		}
	}
	foreach ( (array) $request->get_param( 'items' ) as $item ) {
		if ( ! is_array( $item ) || empty( $item['symbol'] ) || ! isset( $item['price'] ) || ! is_numeric( $item['price'] ) ) {
			continue;
		}
		$symbol                    = sanitize_text_field( $item['symbol'] );
		$data['assets'][ $symbol ] = array(
			'symbol' => $symbol,
			'name'   => isset( $item['name'] ) ? sanitize_text_field( $item['name'] ) : $symbol,
			'group'  => isset( $item['group'] ) && array_key_exists( $item['group'], tv_price_groups() ) ? $item['group'] : 'markets',
			'price'  => (float) $item['price'],
			'change' => isset( $item['change'] ) && is_numeric( $item['change'] ) ? (float) $item['change'] : 0.0,
			'prefix' => isset( $item['prefix'] ) ? sanitize_text_field( $item['prefix'] ) : '',
			'source' => 'push',
		);
		++$count;
	}
	if ( ! $count ) {
		return new WP_Error( 'tv_no_items', __( 'Send at least one item with a symbol and a numeric price.', 'tickervelocity' ), array( 'status' => 400 ) );
	}
	update_option( 'tv_prices', $data, false );
	return array( 'saved' => $count );
}

/** Keeps prices fresh even when nobody has a page open. */
add_filter(
	'cron_schedules',
	function ( $schedules ) {
		$schedules['tv_five_minutes'] = array(
			'interval' => TV_PRICE_TTL,
			'display'  => 'Every five minutes',
		);
		return $schedules;
	}
);

add_action( 'init', 'tv_schedule_prices' );
function tv_schedule_prices() {
	if ( ! wp_next_scheduled( 'tv_prices_cron' ) ) {
		wp_schedule_event( time() + 60, 'tv_five_minutes', 'tv_prices_cron' );
	}
}
add_action( 'tv_prices_cron', 'tv_prices_maybe_refresh' );

/** Settings changed: fetch again straight away. */
add_action(
	'customize_save_after',
	function () {
		delete_transient( 'tv_prices_lock' );
		tv_prices_refresh();
	}
);

/* ---------------------------------------------------------------------------
 * Shortcodes for use inside articles and pages
 * ------------------------------------------------------------------------ */

/** [tv_price symbol="BTC"]: one live price card. */
add_shortcode(
	'tv_price',
	function ( $atts ) {
		$atts   = shortcode_atts( array( 'symbol' => '' ), $atts, 'tv_price' );
		$prices = tv_prices();
		$symbol = strtoupper( trim( $atts['symbol'] ) );
		if ( ! isset( $prices[ $symbol ] ) ) {
			return '';
		}
		ob_start();
		echo '<div class="tv-cards tv-cards--inline">';
		get_template_part( 'template-parts/price-card', null, array( 'asset' => $prices[ $symbol ] ) );
		echo '</div>';
		return ob_get_clean();
	}
);

/** [tv_prices group="crypto" limit="8" tabs="yes"]: a board of live price cards. */
add_shortcode(
	'tv_prices',
	function ( $atts ) {
		$atts = shortcode_atts(
			array(
				'group' => '',
				'limit' => 0,
				'tabs'  => 'yes',
			),
			$atts,
			'tv_prices'
		);
		ob_start();
		get_template_part(
			'template-parts/price-board',
			null,
			array(
				'group' => sanitize_key( $atts['group'] ),
				'limit' => (int) $atts['limit'],
				'tabs'  => 'yes' === $atts['tabs'],
			)
		);
		return ob_get_clean();
	}
);
