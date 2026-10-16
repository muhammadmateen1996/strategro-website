<?php
/**
 * Optional article fields: a writing tool can send them through the REST API
 * (as post "meta"), and they can be edited in the post editor's "Article
 * details" box. Everything works without them: takeaways, sources and the
 * assets in a story are also picked up from the text automatically.
 *
 * @package TickerVelocity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tv_meta_fields() {
	return array(
		'tv_takeaways'   => array( 'string', __( 'Key takeaways', 'tickervelocity' ), __( 'One per line. Three to five short, factual points.', 'tickervelocity' ) ),
		'tv_tickers'     => array( 'string', __( 'Market impact', 'tickervelocity' ), __( 'One per line: SYMBOL:up|down|flat:short reason. Example: NVDA:up:Data-centre sales beat forecasts', 'tickervelocity' ) ),
		'tv_sentiment'   => array( 'string', __( 'Overall read', 'tickervelocity' ), __( 'bullish, bearish, mixed or neutral', 'tickervelocity' ) ),
		'tv_horizon'     => array( 'string', __( 'Time frame', 'tickervelocity' ), __( 'For example: today, this week, this quarter, long term', 'tickervelocity' ) ),
		'tv_sources'     => array( 'string', __( 'Sources', 'tickervelocity' ), __( 'One per line: Title | https://link. Primary sources first (filings, central banks, company releases).', 'tickervelocity' ) ),
		'tv_reviewed_by' => array( 'string', __( 'Reviewed by', 'tickervelocity' ), __( 'Name of the editor who checked the article.', 'tickervelocity' ) ),
			'tv_ai_image'    => array( 'boolean', __( 'Featured image is AI-generated', 'tickervelocity' ), __( 'Adds an "AI-generated illustration" credit under the image.', 'tickervelocity' ) ),
		'tv_breaking'    => array( 'boolean', __( 'Breaking news', 'tickervelocity' ), __( 'Shows a "Breaking" label for 12 hours after publishing.', 'tickervelocity' ) ),
	);
}

add_action( 'init', 'tv_register_meta' );
function tv_register_meta() {
	foreach ( tv_meta_fields() as $key => $field ) {
		register_post_meta(
			'post',
			$key,
			array(
				'type'              => $field[0],
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'boolean' === $field[0] ? 'rest_sanitize_boolean' : 'sanitize_textarea_field',
				'auth_callback'     => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}
}

/* ---------------------------------------------------------------------------
 * Reading the fields
 * ------------------------------------------------------------------------ */

function tv_lines( $text ) {
	return array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) $text ) ) ) );
}

function tv_takeaways( $post_id = null ) {
	return array_map(
		function ( $line ) {
			return ltrim( $line, "-•* \t" );
		},
		tv_lines( get_post_meta( $post_id ? $post_id : get_the_ID(), 'tv_takeaways', true ) )
	);
}

/** Market impact rows: array( 'symbol', 'dir' (up|down|flat), 'note' ). */
function tv_tickers( $post_id = null ) {
	$rows = array();
	foreach ( tv_lines( get_post_meta( $post_id ? $post_id : get_the_ID(), 'tv_tickers', true ) ) as $line ) {
		$parts  = array_map( 'trim', explode( ':', $line, 3 ) );
		$symbol = strtoupper( ltrim( $parts[0], '$' ) );
		if ( '' === $symbol ) {
			continue;
		}
		$dir    = isset( $parts[1] ) ? strtolower( $parts[1] ) : 'flat';
		$rows[] = array(
			'symbol' => $symbol,
			'dir'    => in_array( $dir, array( 'up', 'down' ), true ) ? $dir : 'flat',
			'note'   => isset( $parts[2] ) ? $parts[2] : '',
		);
	}
	return $rows;
}

/** Sources: array( 'title', 'url' ). */
function tv_sources( $post_id = null ) {
	$rows = array();
	foreach ( tv_lines( get_post_meta( $post_id ? $post_id : get_the_ID(), 'tv_sources', true ) ) as $line ) {
		$parts = array_map( 'trim', explode( '|', $line, 2 ) );
		$url   = isset( $parts[1] ) ? $parts[1] : ( filter_var( $parts[0], FILTER_VALIDATE_URL ) ? $parts[0] : '' );
		$title = isset( $parts[1] ) ? $parts[0] : ( $url ? wp_parse_url( $url, PHP_URL_HOST ) : $parts[0] );
		$rows[] = array(
			'title' => $title,
			'url'   => $url,
		);
	}
	return $rows;
}

function tv_is_breaking( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	return get_post_meta( $post_id, 'tv_breaking', true ) && ( time() - get_post_time( 'U', true, $post_id ) ) < 12 * HOUR_IN_SECONDS;
}

/* ---------------------------------------------------------------------------
 * "Article details" box in the editor
 * ------------------------------------------------------------------------ */

add_action( 'add_meta_boxes_post', 'tv_add_meta_box' );
function tv_add_meta_box() {
	add_meta_box( 'tv-article-details', __( 'Article details (TickerVelocity)', 'tickervelocity' ), 'tv_meta_box', 'post', 'normal', 'high' );
}

function tv_meta_box( $post ) {
	wp_nonce_field( 'tv_meta', 'tv_meta_nonce' );
	echo '<p>' . esc_html__( 'All optional. Left empty, takeaways, sources and the assets in the story are taken from the article text.', 'tickervelocity' ) . '</p>';
	foreach ( tv_meta_fields() as $key => $field ) {
		$value = get_post_meta( $post->ID, $key, true );
		echo '<p style="margin:14px 0 4px"><label for="' . esc_attr( $key ) . '"><strong>' . esc_html( $field[1] ) . '</strong></label><br><span class="description">' . esc_html( $field[2] ) . '</span></p>';
		if ( 'boolean' === $field[0] ) {
			printf( '<label><input type="checkbox" id="%1$s" name="%1$s" value="1" %2$s> %3$s</label>', esc_attr( $key ), checked( (bool) $value, true, false ), esc_html__( 'Yes', 'tickervelocity' ) );
		} elseif ( in_array( $key, array( 'tv_takeaways', 'tv_tickers', 'tv_sources' ), true ) ) {
			printf( '<textarea class="widefat" rows="4" id="%1$s" name="%1$s">%2$s</textarea>', esc_attr( $key ), esc_textarea( $value ) );
		} else {
			printf( '<input class="widefat" type="text" id="%1$s" name="%1$s" value="%2$s">', esc_attr( $key ), esc_attr( $value ) );
		}
	}
}

add_action( 'save_post_post', 'tv_save_meta_box' );
function tv_save_meta_box( $post_id ) {
	if ( ! isset( $_POST['tv_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tv_meta_nonce'] ) ), 'tv_meta' ) || ! current_user_can( 'edit_post', $post_id ) || wp_is_post_autosave( $post_id ) ) {
		return;
	}
	foreach ( tv_meta_fields() as $key => $field ) {
		if ( 'boolean' === $field[0] ) {
			update_post_meta( $post_id, $key, ! empty( $_POST[ $key ] ) );
		} elseif ( isset( $_POST[ $key ] ) ) {
			update_post_meta( $post_id, $key, sanitize_textarea_field( wp_unslash( $_POST[ $key ] ) ) );
		}
	}
}
