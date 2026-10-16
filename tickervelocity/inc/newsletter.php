<?php
/**
 * Newsletter capture: the theme's forms post here, the email is kept in
 * Subscribers (so you never lose a list) and sent on to MailerLite, Beehiiv
 * or a webhook if one is chosen in the Customizer.
 *
 * @package TickerVelocity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', 'tv_register_subscribers' );
function tv_register_subscribers() {
	register_post_type(
		'tv_subscriber',
		array(
			'labels'          => array(
				'name'          => __( 'Subscribers', 'tickervelocity' ),
				'singular_name' => __( 'Subscriber', 'tickervelocity' ),
				'search_items'  => __( 'Search subscribers', 'tickervelocity' ),
				'not_found'     => __( 'No subscribers yet.', 'tickervelocity' ),
			),
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => true,
			'menu_position'   => 26,
			'menu_icon'       => 'dashicons-email-alt',
			'supports'        => array( 'title' ),
			'capability_type' => 'post',
			'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
			'map_meta_cap'    => true,
		)
	);
}

/**
 * Saves (or finds) a subscriber and forwards new ones to the webhook.
 * Returns true, or a WP_Error with a message for the visitor.
 */
function tv_subscribe( $email, $source ) {
	$email = sanitize_email( $email );
	if ( ! is_email( $email ) ) {
		return new WP_Error( 'tv_email', __( 'Please enter a valid email address.', 'tickervelocity' ) );
	}

	$ip    = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$limit = 'tv_sub_' . md5( $ip );
	$tries = (int) get_transient( $limit );
	if ( $tries >= 5 ) {
		return new WP_Error( 'tv_rate', __( 'Too many attempts. Please try again in a few minutes.', 'tickervelocity' ) );
	}
	set_transient( $limit, $tries + 1, 10 * MINUTE_IN_SECONDS );

	$existing = get_posts(
		array(
			'post_type'      => 'tv_subscriber',
			'post_status'    => 'any',
			'title'          => $email,
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);
	if ( $existing ) {
		return true; // Don't reveal who is already subscribed.
	}

	$id = wp_insert_post(
		array(
			'post_type'   => 'tv_subscriber',
			'post_status' => 'private',
			'post_title'  => $email,
		)
	);
	if ( is_wp_error( $id ) ) {
		return new WP_Error( 'tv_save', __( 'Something went wrong. Please try again.', 'tickervelocity' ) );
	}
	update_post_meta( $id, 'tv_source', sanitize_text_field( $source ) );

	tv_forward_subscriber( $email, $source );
	return true;
}

/**
 * Sends a new subscriber to the email tool chosen in the Customizer. Runs in
 * the background (non-blocking), so signups stay instant.
 */
function tv_forward_subscriber( $email, $source ) {
	$via  = tv_mod( 'tv_newsletter_via' );
	$key  = trim( (string) tv_mod( 'tv_newsletter_key' ) );
	$list = trim( (string) tv_mod( 'tv_newsletter_list' ) );
	$args = array(
		'blocking' => false,
		'timeout'  => 4,
		'headers'  => array(
			'Content-Type' => 'application/json',
			'Accept'       => 'application/json',
		),
	);

	if ( 'mailerlite' === $via && $key ) {
		$body = array( 'email' => $email );
		if ( $list ) {
			$body['groups'] = array( $list );
		}
		$args['headers']['Authorization'] = 'Bearer ' . $key;
		$args['body']                     = wp_json_encode( $body );
		wp_remote_post( 'https://connect.mailerlite.com/api/subscribers', $args );
	} elseif ( 'beehiiv' === $via && $key && $list ) {
		$args['headers']['Authorization'] = 'Bearer ' . $key;
		$args['body']                     = wp_json_encode(
			array(
				'email'              => $email,
				'reactivate_existing' => false,
				'send_welcome_email' => true,
				'utm_source'         => 'website',
				'utm_medium'         => sanitize_key( $source ),
				'referring_site'     => home_url( '/' ),
			)
		);
		wp_remote_post( 'https://api.beehiiv.com/v2/publications/' . rawurlencode( $list ) . '/subscriptions', $args );
	} elseif ( 'webhook' === $via && tv_mod( 'tv_newsletter_hook' ) ) {
		$args['body'] = wp_json_encode(
			array(
				'email'         => $email,
				'source'        => sanitize_text_field( $source ),
				'subscribed_at' => gmdate( 'c' ),
				'site'          => home_url( '/' ),
			)
		);
		wp_remote_post( tv_mod( 'tv_newsletter_hook' ), $args );
	}
}

add_action( 'rest_api_init', 'tv_newsletter_route' );
function tv_newsletter_route() {
	register_rest_route(
		'tv/v1',
		'/subscribe',
		array(
			'methods'             => 'POST',
			'permission_callback' => '__return_true',
			'callback'            => function ( WP_REST_Request $request ) {
				if ( '' !== (string) $request->get_param( 'company' ) ) {
					return array( 'ok' => true ); // Honeypot: bots fill every field.
				}
				$result = tv_subscribe( (string) $request->get_param( 'email' ), (string) $request->get_param( 'source' ) );
				if ( is_wp_error( $result ) ) {
					return new WP_REST_Response(
						array(
							'ok'      => false,
							'message' => $result->get_error_message(),
						),
						400
					);
				}
				return array( 'ok' => true );
			},
		)
	);
}

/** Same form without JavaScript. */
add_action( 'admin_post_nopriv_tv_subscribe', 'tv_subscribe_fallback' );
add_action( 'admin_post_tv_subscribe', 'tv_subscribe_fallback' );
function tv_subscribe_fallback() {
	// phpcs:disable WordPress.Security.NonceVerification -- public signup form, protected by honeypot and rate limit.
	$back = wp_get_referer() ? wp_get_referer() : home_url( '/' );
	if ( empty( $_POST['company'] ) ) {
		$result = tv_subscribe( isset( $_POST['email'] ) ? sanitize_text_field( wp_unslash( $_POST['email'] ) ) : '', isset( $_POST['source'] ) ? sanitize_text_field( wp_unslash( $_POST['source'] ) ) : '' );
		$back   = add_query_arg( 'subscribed', is_wp_error( $result ) ? '0' : '1', $back );
	}
	// phpcs:enable
	wp_safe_redirect( $back . '#newsletter' );
	exit;
}

add_shortcode( 'tv_newsletter', 'tv_newsletter_shortcode' );
function tv_newsletter_shortcode() {
	ob_start();
	get_template_part( 'template-parts/newsletter', null, array( 'variant' => 'page' ) );
	return ob_get_clean();
}

/* ---------------------------------------------------------------------------
 * Admin: list columns and CSV export
 * ------------------------------------------------------------------------ */

add_filter( 'manage_tv_subscriber_posts_columns', 'tv_subscriber_columns' );
function tv_subscriber_columns() {
	return array(
		'cb'        => '<input type="checkbox">',
		'title'     => __( 'Email', 'tickervelocity' ),
		'tv_source' => __( 'Signed up from', 'tickervelocity' ),
		'date'      => __( 'Date', 'tickervelocity' ),
	);
}

add_action( 'manage_tv_subscriber_posts_custom_column', 'tv_subscriber_column', 10, 2 );
function tv_subscriber_column( $column, $post_id ) {
	if ( 'tv_source' === $column ) {
		echo esc_html( get_post_meta( $post_id, 'tv_source', true ) );
	}
}

add_filter( 'views_edit-tv_subscriber', 'tv_subscriber_export_button' );
function tv_subscriber_export_button( $views ) {
	$url                = wp_nonce_url( admin_url( 'admin-post.php?action=tv_export_subscribers' ), 'tv_export' );
	$views['tv_export'] = '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Download CSV', 'tickervelocity' ) . '</a>';
	return $views;
}

add_action( 'admin_post_tv_export_subscribers', 'tv_export_subscribers' );
function tv_export_subscribers() {
	check_admin_referer( 'tv_export' );
	if ( ! current_user_can( 'edit_others_posts' ) ) {
		wp_die( esc_html__( 'Not allowed.', 'tickervelocity' ) );
	}
	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=subscribers-' . gmdate( 'Y-m-d' ) . '.csv' );
	$out = fopen( 'php://output', 'w' ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	fputcsv( $out, array( 'email', 'source', 'subscribed_at' ) );
	foreach ( get_posts( array( 'post_type' => 'tv_subscriber', 'post_status' => 'any', 'posts_per_page' => -1 ) ) as $sub ) {
		fputcsv( $out, array( $sub->post_title, get_post_meta( $sub->ID, 'tv_source', true ), get_post_time( 'c', true, $sub ) ) );
	}
	fclose( $out ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	exit;
}

/* ---------------------------------------------------------------------------
 * Privacy: Tools > Export/Erase Personal Data include subscribers
 * ------------------------------------------------------------------------ */

add_filter( 'wp_privacy_personal_data_erasers', 'tv_register_subscriber_eraser' );
function tv_register_subscriber_eraser( $erasers ) {
	$erasers['tickervelocity-subscribers'] = array(
		'eraser_friendly_name' => __( 'Newsletter subscribers', 'tickervelocity' ),
		'callback'             => function ( $email ) {
			$ids = get_posts(
				array(
					'post_type'      => 'tv_subscriber',
					'post_status'    => 'any',
					'title'          => $email,
					'posts_per_page' => -1,
					'fields'         => 'ids',
				)
			);
			foreach ( $ids as $id ) {
				wp_delete_post( $id, true );
			}
			return array(
				'items_removed'  => (bool) $ids,
				'items_retained' => false,
				'messages'       => $ids ? array( __( 'Removed from the site\'s subscriber list. Also remove them in your email tool.', 'tickervelocity' ) ) : array(),
				'done'           => true,
			);
		},
	);
	return $erasers;
}
