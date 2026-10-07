<?php
/**
 * Strategro theme bootstrap.
 *
 * @package Strategro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'STRATEGRO_VERSION', '1.2.0' );
define( 'STRATEGRO_DIR', get_template_directory() );
define( 'STRATEGRO_URI', get_template_directory_uri() );

require STRATEGRO_DIR . '/inc/customizer.php';
require STRATEGRO_DIR . '/inc/menus.php';
require STRATEGRO_DIR . '/inc/shortcodes.php';
require STRATEGRO_DIR . '/inc/template-tags.php';
require STRATEGRO_DIR . '/inc/setup.php';
require STRATEGRO_DIR . '/inc/elementor.php';
if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
	require STRATEGRO_DIR . '/inc/installer.php';
}

add_action( 'after_setup_theme', 'strategro_setup' );
function strategro_setup() {
	load_theme_textdomain( 'strategro', STRATEGRO_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 64,
			'width'       => 240,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	register_nav_menus(
		array(
			'primary'         => __( 'Header menu', 'strategro' ),
			'footer_products' => __( 'Footer: Products', 'strategro' ),
			'footer_company'  => __( 'Footer: Company', 'strategro' ),
			'footer_legal'    => __( 'Footer: Legal', 'strategro' ),
		)
	);
}

/**
 * Flags the document before first paint so motion styles never flash:
 * sg-js means JavaScript runs; sg-motion means animate (not reduced
 * motion, and not inside the Elementor editor where everything must stay
 * visible and editable).
 */
add_action( 'wp_head', 'strategro_head_flags', 1 );
function strategro_head_flags() {
	?>
	<script>
	(function (d) {
		var c = d.documentElement.classList, q = window.location.search;
		c.add('sg-js');
		if (q.indexOf('elementor-preview') === -1 && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
			c.add('sg-motion');
			setTimeout(function () {
				if (!window.strategroMotionReady) { c.add('sg-failed'); }
			}, 4000);
		}
	})(document);
	</script>
	<?php
}

add_action( 'wp_enqueue_scripts', 'strategro_assets' );
function strategro_assets() {
	wp_enqueue_style(
		'strategro-fonts',
		'https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600&family=Inter:wght@400;500;600;700&display=swap',
		array(),
		null
	);
	wp_enqueue_style( 'strategro', STRATEGRO_URI . '/assets/css/theme.css', array( 'strategro-fonts' ), STRATEGRO_VERSION );

	wp_enqueue_script( 'gsap', STRATEGRO_URI . '/assets/vendor/gsap.min.js', array(), '3.15.0', true );
	wp_enqueue_script( 'gsap-scrolltrigger', STRATEGRO_URI . '/assets/vendor/ScrollTrigger.min.js', array( 'gsap' ), '3.15.0', true );
	wp_enqueue_script( 'strategro-motion', STRATEGRO_URI . '/assets/js/motion.js', array( 'gsap', 'gsap-scrolltrigger' ), STRATEGRO_VERSION, true );
	wp_localize_script(
		'strategro-motion',
		'strategroSettings',
		array(
			'transitions' => (bool) strategro_mod( 'strategro_page_transitions' ),
		)
	);

	$clara_key = strategro_mod( 'strategro_clara_key' );
	if ( $clara_key ) {
		wp_enqueue_script( 'strategro-clara', esc_url_raw( strategro_mod( 'strategro_clara_script' ) ), array(), null, true );
	}
}

/** Clara's embed reads its key from a data attribute on its own script tag. */
add_filter( 'script_loader_tag', 'strategro_clara_tag', 10, 2 );
function strategro_clara_tag( $tag, $handle ) {
	if ( 'strategro-clara' !== $handle ) {
		return $tag;
	}
	$key = esc_attr( strategro_mod( 'strategro_clara_key' ) );
	return str_replace( ' src=', ' data-key="' . $key . '" defer src=', $tag );
}

add_filter( 'body_class', 'strategro_body_class' );
function strategro_body_class( $classes ) {
	if ( is_singular() && strategro_is_elementor_page( get_the_ID() ) ) {
		$classes[] = 'sg-elementor-page';
	}
	return $classes;
}

function strategro_is_elementor_page( $post_id ) {
	return 'builder' === get_post_meta( $post_id, '_elementor_edit_mode', true );
}

add_filter( 'excerpt_length', fn() => 28 );
add_filter( 'excerpt_more', fn() => '&hellip;' );
