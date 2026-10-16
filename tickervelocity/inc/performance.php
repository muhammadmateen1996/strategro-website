<?php
/**
 * Speed: a news site lives on Core Web Vitals. No jQuery, no page builder,
 * fonts from the site itself, and nothing loaded that a page doesn't use.
 *
 * @package TickerVelocity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_head', 'tv_preload_fonts', 2 );
function tv_preload_fonts() {
	foreach ( array( 'inter-latin', 'newsreader-latin' ) as $file ) {
		printf( '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n", esc_url( TV_URI . '/assets/fonts/' . $file . '.woff2' ) );
	}
}

/** The lead image on the homepage and articles is the largest thing on screen: fetch it first. */
add_filter( 'wp_get_attachment_image_attributes', 'tv_priority_image', 10, 3 );
function tv_priority_image( $attr, $attachment, $size ) {
	if ( 'tv-lead' === $size && ! is_admin() ) {
		$attr['loading']       = 'eager';
		$attr['fetchpriority'] = 'high';
	}
	return $attr;
}

add_action( 'init', 'tv_disable_emoji' );
function tv_disable_emoji() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	add_filter( 'emoji_svg_url', '__return_false' );
}

/** Block styles only for the blocks a page uses. */
add_filter( 'should_load_separate_core_block_assets', '__return_true' );

/** Bits WordPress adds to every page that a news site doesn't need. */
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
