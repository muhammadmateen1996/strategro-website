<?php
/**
 * Speed: drops what the site doesn't use and avoids loading anything twice.
 *
 * @package Strategro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Fonts come from the theme itself: no connection to Google on every page
 * view (faster, and no visitor data sent to Google), and the two files
 * needed for English text are preloaded.
 */
add_action( 'wp_head', 'strategro_preload_fonts', 2 );
function strategro_preload_fonts() {
	foreach ( array( 'inter-latin', 'fraunces-latin' ) as $file ) {
		printf( '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n", esc_url( STRATEGRO_URI . '/assets/fonts/' . $file . '.woff2' ) );
	}
}

/**
 * Elementor also loads Fraunces and Inter (every weight, from Google) because
 * they are the site's global fonts. The theme already provides them, so
 * those copies are dropped. Any other font picked in Elementor still loads.
 */
add_filter( 'style_loader_tag', 'strategro_drop_duplicate_fonts', 10, 2 );
function strategro_drop_duplicate_fonts( $tag, $handle ) {
	if ( preg_match( '/^elementor-gf-(local-)?(fraunces|inter)$/', $handle ) && ! isset( $_GET['elementor-preview'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		return '';
	}
	return $tag;
}

/** WordPress's emoji script: every modern browser shows emoji on its own. */
add_action( 'init', 'strategro_disable_emoji' );
function strategro_disable_emoji() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	add_filter( 'emoji_svg_url', '__return_false' );
}

/**
 * Block editor styles only for the blocks a page actually contains, instead
 * of the whole block library (about 140 KB) on every page.
 */
add_filter( 'should_load_separate_core_block_assets', '__return_true' );

/** Pages built entirely with Elementor contain no blocks at all. */
add_action( 'wp_enqueue_scripts', 'strategro_drop_block_css_on_elementor_pages', 100 );
function strategro_drop_block_css_on_elementor_pages() {
	if ( is_singular() && ! is_singular( 'post' ) && strategro_is_elementor_page( get_queried_object_id() ) ) {
		foreach ( array( 'wp-block-library', 'wp-block-library-theme', 'classic-theme-styles', 'global-styles' ) as $handle ) {
			wp_dequeue_style( $handle );
		}
	}
}
