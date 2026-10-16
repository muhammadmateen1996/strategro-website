<?php
/**
 * Market strip data.
 *
 *  - Crypto prices come from CoinGecko (free) and are refreshed at most every
 *    five minutes, only when a visitor's browser asks for them, so pages
 *    never wait on CoinGecko.
 *  - Anything else (indices, stocks, FX, commodities) is pushed by n8n to
 *    POST /wp-json/tv/v1/market, so you can use whichever data source your
 *    workflow already has.
 *
 * @package TickerVelocity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Crypto symbols from the Customizer: SYMBOL => coingecko-id. */
function tv_crypto_watchlist() {
	$list = array();
	foreach ( explode( ',', tv_mod( 'tv_market_crypto' ) ) as $pair ) {
		$parts = array_map( 'trim', explode( ':', $pair ) );
		if ( 2 === count( $parts ) && $parts[0] && $parts[1] ) {
			$list[ strtoupper( $parts[0] ) ] = sanitize_key( $parts[1] );
		}
	}
	return $list;
}

/** Fetches crypto prices if the cached copy is older than five minutes. */
function tv_refresh_crypto() {
	$list = tv_crypto_watchlist();
	if ( ! $list || false !== get_transient( 'tv_crypto_fresh' ) ) {
		return;
	}
	set_transient( 'tv_crypto_fresh', 1, 5 * MINUTE_IN_SECONDS ); // Also stops retries hammering a failing API.

	$key     = tv_mod( 'tv_coingecko_key' );
	$url     = add_query_arg(
		array(
			'ids'                 => implode( ',', $list ),
			'vs_currencies'       => 'usd',
			'include_24hr_change' => 'true',
		),
		'https://api.coingecko.com/api/v3/simple/price'
	);
	$headers = $key ? array( 'x-cg-demo-api-key' => $key ) : array();
	$res     = wp_remote_get(
		$url,
		array(
			'timeout' => 4,
			'headers' => $headers,
		)
	);
	if ( is_wp_error( $res ) || 200 !== wp_remote_retrieve_response_code( $res ) ) {
		return;
	}
	$data = json_decode( wp_remote_retrieve_body( $res ), true );
	if ( ! is_array( $data ) ) {
		return;
	}
	$items = array();
	foreach ( $list as $symbol => $id ) {
		if ( isset( $data[ $id ]['usd'] ) ) {
			$items[] = array(
				'symbol' => $symbol,
				'price'  => (float) $data[ $id ]['usd'],
				'change' => isset( $data[ $id ]['usd_24h_change'] ) ? round( (float) $data[ $id ]['usd_24h_change'], 2 ) : 0,
				'prefix' => '$',
			);
		}
	}
	if ( $items ) {
		update_option(
			'tv_market_crypto',
			array(
				'updated' => time(),
				'items'   => $items,
			),
			false
		);
	}
}

/** Everything for the strip: array( 'items' => [...], 'updated' => unix time ). */
function tv_market_snapshot() {
	$pushed  = get_option( 'tv_market_pushed', array() );
	$crypto  = get_option( 'tv_market_crypto', array() );
	$items   = array_merge( isset( $pushed['items'] ) ? $pushed['items'] : array(), isset( $crypto['items'] ) ? $crypto['items'] : array() );
	$updated = max( isset( $pushed['updated'] ) ? (int) $pushed['updated'] : 0, isset( $crypto['updated'] ) ? (int) $crypto['updated'] : 0 );
	return array(
		'items'   => array_map( 'tv_format_market_item', $items ),
		'updated' => $updated,
	);
}

function tv_format_market_item( $item ) {
	$price    = (float) $item['price'];
	$decimals = $price < 1 ? 4 : ( $price < 1000 ? 2 : 0 );
	$change   = (float) $item['change'];
	return array(
		'symbol' => (string) $item['symbol'],
		'price'  => ( isset( $item['prefix'] ) ? $item['prefix'] : '' ) . number_format( $price, $decimals ),
		'change' => ( $change > 0 ? '+' : '' ) . number_format( $change, 2 ) . '%',
		'dir'    => $change > 0.005 ? 'up' : ( $change < -0.005 ? 'down' : 'flat' ),
	);
}

add_action( 'rest_api_init', 'tv_market_routes' );
function tv_market_routes() {
	register_rest_route(
		'tv/v1',
		'/market',
		array(
			array(
				'methods'             => 'GET',
				'permission_callback' => '__return_true',
				'callback'            => function () {
					tv_refresh_crypto();
					$response = rest_ensure_response( tv_market_snapshot() );
					$response->header( 'Cache-Control', 'public, max-age=60' );
					return $response;
				},
			),
			array(
				'methods'             => 'POST',
				'permission_callback' => function () {
					return current_user_can( 'edit_others_posts' );
				},
				'callback'            => 'tv_market_push',
			),
		)
	);
}

/**
 * n8n sends: { "items": [ { "symbol": "S&P 500", "price": 5321.4, "change": 0.62 }, ... ] }
 * "prefix" (for example "$") is optional. The list replaces the previous one.
 */
function tv_market_push( WP_REST_Request $request ) {
	$items = array();
	foreach ( (array) $request->get_param( 'items' ) as $item ) {
		if ( ! is_array( $item ) || empty( $item['symbol'] ) || ! isset( $item['price'] ) || ! is_numeric( $item['price'] ) ) {
			continue;
		}
		$items[] = array(
			'symbol' => sanitize_text_field( $item['symbol'] ),
			'price'  => (float) $item['price'],
			'change' => isset( $item['change'] ) && is_numeric( $item['change'] ) ? (float) $item['change'] : 0,
			'prefix' => isset( $item['prefix'] ) ? sanitize_text_field( $item['prefix'] ) : '',
		);
	}
	if ( ! $items ) {
		return new WP_Error( 'tv_no_items', __( 'Send at least one item with a symbol and a numeric price.', 'tickervelocity' ), array( 'status' => 400 ) );
	}
	update_option(
		'tv_market_pushed',
		array(
			'updated' => time(),
			'items'   => array_slice( $items, 0, 30 ),
		),
		false
	);
	return array(
		'saved'   => count( $items ),
		'updated' => time(),
	);
}
