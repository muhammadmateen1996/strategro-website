<?php
/**
 * Partner offers (affiliate and referral links).
 *
 * - Managed in TickerVelocity > Partner offers. Each gets a tidy link
 *   (/go/name/) that counts clicks and can be repointed any time.
 * - Shown at the end of articles in the categories you choose, always
 *   labelled as an affiliate link and with a risk warning.
 * - Hidden from visitors in countries where you shouldn't promote them (by
 *   default the UK and the US) and, unless you choose otherwise, from
 *   visitors whose country can't be detected. The check happens in the
 *   visitor's browser via the REST API, so it still works with page caching.
 *
 * @package TickerVelocity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TV_OFFER_SLOTS = 4;

function tv_offers() {
	$offers = get_option( 'tv_offers', array() );
	$clean  = array();
	foreach ( (array) $offers as $offer ) {
		if ( ! empty( $offer['active'] ) && ! empty( $offer['slug'] ) && ! empty( $offer['url'] ) ) {
			$clean[ $offer['slug'] ] = $offer;
		}
	}
	return $clean;
}

/** Visitor's country (two letters) from the CDN or host, or '' if unknown. */
function tv_visitor_country() {
	foreach ( array( 'HTTP_CF_IPCOUNTRY', 'HTTP_CLOUDFRONT_VIEWER_COUNTRY', 'HTTP_X_VERCEL_IP_COUNTRY', 'HTTP_X_COUNTRY_CODE', 'GEOIP_COUNTRY_CODE', 'HTTP_GEOIP_COUNTRY_CODE' ) as $key ) {
		if ( ! empty( $_SERVER[ $key ] ) ) {
			$code = strtoupper( substr( sanitize_text_field( wp_unslash( $_SERVER[ $key ] ) ), 0, 2 ) );
			if ( preg_match( '/^[A-Z]{2}$/', $code ) && ! in_array( $code, array( 'XX', 'T1', 'A1', 'A2' ), true ) ) {
				return $code;
			}
		}
	}
	return '';
}

function tv_offer_allowed( $offer, $country ) {
	if ( '' === $country ) {
		return 'show' === get_option( 'tv_offers_unknown', 'hide' );
	}
	$blocked = array_filter( array_map( 'trim', explode( ',', strtoupper( (string) $offer['blocked'] ) ) ) );
	return ! in_array( $country, $blocked, true );
}

/** Offers that fit a post (by its tv_offer field or its categories). */
function tv_offers_for_post( $post_id ) {
	$offers = tv_offers();
	$choice = trim( (string) get_post_meta( $post_id, 'tv_offer', true ) );
	if ( 'none' === $choice || ! $offers ) {
		return array();
	}
	if ( $choice ) {
		return isset( $offers[ $choice ] ) ? array( $offers[ $choice ] ) : array();
	}
	$slugs = array();
	foreach ( get_the_category( $post_id ) as $term ) {
		$slugs[] = $term->slug;
		foreach ( get_ancestors( $term->term_id, 'category' ) as $parent ) {
			$slugs[] = get_term( $parent )->slug;
		}
	}
	return array_values(
		array_filter(
			$offers,
			function ( $offer ) use ( $slugs ) {
				$cats = array_filter( array_map( 'trim', explode( ',', strtolower( (string) $offer['categories'] ) ) ) );
				return (bool) array_intersect( $cats, $slugs );
			}
		)
	);
}

add_action( 'rest_api_init', 'tv_offer_routes' );
function tv_offer_routes() {
	register_rest_route(
		'tv/v1',
		'/offers',
		array(
			'methods'             => 'GET',
			'permission_callback' => '__return_true',
			'args'                => array( 'post' => array( 'sanitize_callback' => 'absint' ) ),
			'callback'            => function ( WP_REST_Request $request ) {
				$post_id = (int) $request->get_param( 'post' );
				$country = tv_visitor_country();
				$html    = '';
				if ( $post_id && 'publish' === get_post_status( $post_id ) ) {
					foreach ( tv_offers_for_post( $post_id ) as $offer ) {
						if ( tv_offer_allowed( $offer, $country ) ) {
							ob_start();
							get_template_part( 'template-parts/offer', null, array( 'offer' => $offer ) );
							$html .= ob_get_clean();
							break; // One offer per article keeps it honest.
						}
					}
				}
				$response = rest_ensure_response( array( 'html' => $html ) );
				$response->header( 'Cache-Control', 'no-store, private' );
				$response->header( 'Vary', 'CF-IPCountry' );
				return $response;
			},
		)
	);
}

/* ---------------------------------------------------------------------------
 * /go/name/ links
 * ------------------------------------------------------------------------ */

add_action( 'init', 'tv_offer_rewrites' );
function tv_offer_rewrites() {
	add_rewrite_rule( '^go/([a-z0-9-]+)/?$', 'index.php?tv_go=$matches[1]', 'top' );
}

add_filter( 'query_vars', fn( $vars ) => array_merge( $vars, array( 'tv_go' ) ) );

add_action( 'template_redirect', 'tv_offer_redirect' );
function tv_offer_redirect() {
	$slug = get_query_var( 'tv_go' );
	if ( ! $slug ) {
		return;
	}
	$offers = tv_offers();
	nocache_headers();
	header( 'X-Robots-Tag: noindex, nofollow' );
	if ( ! isset( $offers[ $slug ] ) || ! tv_offer_allowed( $offers[ $slug ], tv_visitor_country() ) ) {
		wp_safe_redirect( home_url( '/' ), 302 );
		exit;
	}
	$clicks          = get_option( 'tv_offer_clicks', array() );
	$clicks[ $slug ] = ( isset( $clicks[ $slug ] ) ? (int) $clicks[ $slug ] : 0 ) + 1;
	update_option( 'tv_offer_clicks', $clicks, false );
	wp_redirect( esc_url_raw( $offers[ $slug ]['url'] ), 302 ); // phpcs:ignore WordPress.Security.SafeRedirect -- external partner link set by an admin.
	exit;
}

/* ---------------------------------------------------------------------------
 * Admin: TickerVelocity > Partner offers
 * ------------------------------------------------------------------------ */

add_action( 'admin_menu', 'tv_offers_menu', 20 );
function tv_offers_menu() {
	add_submenu_page( 'tickervelocity', __( 'Partner offers', 'tickervelocity' ), __( 'Partner offers', 'tickervelocity' ), 'manage_options', 'tv-offers', 'tv_offers_page' );
}

function tv_offer_fields() {
	return array(
		'name'       => array( __( 'Name', 'tickervelocity' ), 'MEXC' ),
		'slug'       => array( __( 'Short link', 'tickervelocity' ), 'mexc' ),
		'url'        => array( __( 'Your referral link', 'tickervelocity' ), 'https://...' ),
		'headline'   => array( __( 'One-line description', 'tickervelocity' ), 'Spot and futures trading on 2,000+ tokens' ),
		'perk'       => array( __( 'Offer for new users (optional)', 'tickervelocity' ), 'Fee discount for new accounts' ),
		'button'     => array( __( 'Button text', 'tickervelocity' ), 'Visit MEXC' ),
		'categories' => array( __( 'Show on articles in these categories', 'tickervelocity' ), 'crypto' ),
		'blocked'    => array( __( 'Never show to visitors in (country codes)', 'tickervelocity' ), 'GB, US' ),
	);
}

function tv_offers_page() {
	if ( isset( $_POST['tv_offers_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tv_offers_nonce'] ) ), 'tv_offers' ) && current_user_can( 'manage_options' ) ) {
		$offers = array();
		$posted = isset( $_POST['tv_offers'] ) && is_array( $_POST['tv_offers'] ) ? wp_unslash( $_POST['tv_offers'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- sanitised per field below.
		for ( $i = 0; $i < TV_OFFER_SLOTS; $i++ ) {
			$row = isset( $posted[ $i ] ) ? $posted[ $i ] : array();
			$offer = array( 'active' => ! empty( $row['active'] ) );
			foreach ( array_keys( tv_offer_fields() ) as $key ) {
				$value         = isset( $row[ $key ] ) ? (string) $row[ $key ] : '';
				$offer[ $key ] = 'url' === $key ? esc_url_raw( $value ) : ( 'slug' === $key ? sanitize_title( $value ) : sanitize_text_field( $value ) );
			}
			$offers[] = $offer;
		}
		update_option( 'tv_offers', $offers, false );
		update_option( 'tv_offers_unknown', isset( $_POST['tv_offers_unknown'] ) && 'show' === $_POST['tv_offers_unknown'] ? 'show' : 'hide', false );
		flush_rewrite_rules( false );
		echo '<div class="notice notice-success"><p>' . esc_html__( 'Offers saved.', 'tickervelocity' ) . '</p></div>';
	}

	$offers  = get_option( 'tv_offers', array() );
	$clicks  = get_option( 'tv_offer_clicks', array() );
	$country = tv_visitor_country();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Partner offers', 'tickervelocity' ); ?></h1>
		<div class="notice notice-warning inline" style="max-width:900px">
			<p><strong><?php esc_html_e( 'Before you add an exchange or broker link, check the rules where your readers are.', 'tickervelocity' ); ?></strong>
			<?php esc_html_e( 'In the UK, promoting cryptoassets to consumers is a regulated financial promotion: unless an FCA-authorised firm approves it, a referral link to an exchange that is not authorised can be a criminal offence. Many exchanges also refuse US customers. That is why offers are hidden from GB and US visitors by default. The UAE, the EU and others have their own rules. This is not legal advice: confirm with the partner programme and, ideally, a lawyer.', 'tickervelocity' ); ?></p>
			<p>
			<?php
			echo $country
				/* translators: %s: country code */
				? esc_html( sprintf( __( 'Country detection works: you are being seen as %s.', 'tickervelocity' ), $country ) )
				: esc_html__( 'Country detection is not set up, so offers stay hidden unless you change the setting below. The simplest fix is Cloudflare (free plan) in front of the site with "IP Geolocation" switched on.', 'tickervelocity' );
			?>
			</p>
		</div>

		<form method="post">
			<?php wp_nonce_field( 'tv_offers', 'tv_offers_nonce' ); ?>
			<?php for ( $i = 0; $i < TV_OFFER_SLOTS; $i++ ) : ?>
				<?php $offer = isset( $offers[ $i ] ) ? $offers[ $i ] : array(); ?>
				<div class="card" style="max-width:900px">
					<h2 style="display:flex;gap:12px;align-items:center">
						<?php
						/* translators: %d: offer number */
						echo esc_html( sprintf( __( 'Offer %d', 'tickervelocity' ), $i + 1 ) );
						?>
						<label style="font-size:13px;font-weight:400"><input type="checkbox" name="tv_offers[<?php echo esc_attr( $i ); ?>][active]" value="1" <?php checked( ! empty( $offer['active'] ) ); ?>> <?php esc_html_e( 'Active', 'tickervelocity' ); ?></label>
						<?php if ( ! empty( $offer['slug'] ) ) : ?>
							<span style="font-size:13px;font-weight:400;color:#646970">
								<code><?php echo esc_html( home_url( '/go/' . $offer['slug'] . '/' ) ); ?></code>
								<?php
								/* translators: %d: clicks */
								echo esc_html( sprintf( _n( '%d click', '%d clicks', isset( $clicks[ $offer['slug'] ] ) ? (int) $clicks[ $offer['slug'] ] : 0, 'tickervelocity' ), isset( $clicks[ $offer['slug'] ] ) ? (int) $clicks[ $offer['slug'] ] : 0 ) );
								?>
							</span>
						<?php endif; ?>
					</h2>
					<table class="form-table" role="presentation">
						<?php foreach ( tv_offer_fields() as $key => $field ) : ?>
							<tr>
								<th scope="row"><label for="tv-offer-<?php echo esc_attr( $i . '-' . $key ); ?>"><?php echo esc_html( $field[0] ); ?></label></th>
								<td><input class="regular-text" id="tv-offer-<?php echo esc_attr( $i . '-' . $key ); ?>" type="<?php echo 'url' === $key ? 'url' : 'text'; ?>" name="tv_offers[<?php echo esc_attr( $i ); ?>][<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( isset( $offer[ $key ] ) ? $offer[ $key ] : ( 'blocked' === $key ? 'GB, US' : '' ) ); ?>" placeholder="<?php echo esc_attr( $field[1] ); ?>"></td>
							</tr>
						<?php endforeach; ?>
					</table>
				</div>
			<?php endfor; ?>

			<h2><?php esc_html_e( 'Visitors whose country is unknown', 'tickervelocity' ); ?></h2>
			<p>
				<label><input type="radio" name="tv_offers_unknown" value="hide" <?php checked( 'show' !== get_option( 'tv_offers_unknown', 'hide' ) ); ?>> <?php esc_html_e( 'Hide offers (safer)', 'tickervelocity' ); ?></label><br>
				<label><input type="radio" name="tv_offers_unknown" value="show" <?php checked( 'show' === get_option( 'tv_offers_unknown', 'hide' ) ); ?>> <?php esc_html_e( 'Show offers', 'tickervelocity' ); ?></label>
			</p>
			<?php submit_button( __( 'Save offers', 'tickervelocity' ) ); ?>
		</form>
	</div>
	<?php
}
