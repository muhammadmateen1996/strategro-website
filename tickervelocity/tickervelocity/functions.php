<?php
/**
 * TickerVelocity theme bootstrap.
 *
 * @package TickerVelocity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TV_VERSION', '1.0.0' );
define( 'TV_DIR', get_template_directory() );
define( 'TV_URI', get_template_directory_uri() );

foreach ( array( 'customizer', 'template-tags', 'meta', 'market', 'newsletter', 'offers', 'seo', 'performance' ) as $tv_file ) {
	require TV_DIR . '/inc/' . $tv_file . '.php';
}
if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
	require TV_DIR . '/inc/setup.php';
}

add_action( 'after_setup_theme', 'tv_setup' );
function tv_setup() {
	load_theme_textdomain( 'tickervelocity', TV_DIR . '/languages' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 56,
			'width'       => 240,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
	add_image_size( 'tv-card', 720, 405, true );
	add_image_size( 'tv-lead', 1280, 720, true );

	register_nav_menus(
		array(
			'primary' => __( 'Main menu (categories)', 'tickervelocity' ),
			'footer'  => __( 'Footer: company', 'tickervelocity' ),
			'legal'   => __( 'Footer: legal', 'tickervelocity' ),
		)
	);
}

add_action( 'wp_enqueue_scripts', 'tv_assets' );
function tv_assets() {
	wp_enqueue_style( 'tv-fonts', TV_URI . '/assets/fonts/fonts.css', array(), TV_VERSION );
	wp_enqueue_style( 'tickervelocity', TV_URI . '/assets/css/theme.css', array( 'tv-fonts' ), TV_VERSION );
	wp_enqueue_script( 'tickervelocity', TV_URI . '/assets/js/site.js', array(), TV_VERSION, true );
	wp_localize_script(
		'tickervelocity',
		'tvSettings',
		array(
			'rest'     => esc_url_raw( rest_url( 'tv/v1/' ) ),
			'refresh'  => 60,
			'i18n'     => array(
				'subscribed' => __( 'You\'re in. Check your inbox to confirm.', 'tickervelocity' ),
				'error'      => __( 'Something went wrong. Please try again.', 'tickervelocity' ),
				'copied'     => __( 'Link copied', 'tickervelocity' ),
			),
		)
	);
}

/** Lets the stylesheet hide motion until the script has run, without flashes. */
add_action( 'wp_head', 'tv_head_flags', 1 );
function tv_head_flags() {
	echo "<script>document.documentElement.classList.add('tv-js');</script>\n";
}

add_filter( 'excerpt_length', fn() => 32 );
add_filter( 'excerpt_more', fn() => '&hellip;' );

/** News articles don't take comments by default (moderating them is a job in itself). */
add_filter( 'comments_open', 'tv_comments_open', 10, 2 );
function tv_comments_open( $open, $post_id ) {
	return 'post' === get_post_type( $post_id ) && ! tv_mod( 'tv_comments' ) ? false : $open;
}
