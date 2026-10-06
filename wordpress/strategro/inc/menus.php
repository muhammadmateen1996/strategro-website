<?php
/**
 * Menu rendering with built-in defaults. Each location shows the WordPress
 * menu assigned to it in Appearance > Menus; until one is assigned, the
 * defaults below are shown so a fresh install already has working
 * navigation.
 *
 * @package Strategro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function strategro_default_links( $location ) {
	$links = array(
		'primary'         => array(
			array( 'Products', '/products/' ),
			array( 'Custom Builds', '/custom-builds/' ),
			array( 'About', '/about/' ),
			array( 'Insights', '/insights/' ),
			array( 'Contact', '/contact/' ),
		),
		'footer_products' => array(
			array( 'Clara', 'https://clara.strategro.co.uk', true ),
			array( 'Lodway', 'https://lodway.com', true ),
			array( 'Strategro SEO', 'https://seo.strategro.co.uk', true ),
			array( 'Strategro Proposals', 'https://proposal.strategro.co.uk', true ),
			array( 'ShopOps', 'https://shopops.strategro.co.uk', true ),
		),
		'footer_company'  => array(
			array( 'About Strategro', '/about/' ),
			array( 'Custom Builds', '/custom-builds/' ),
			array( 'Insights', '/insights/' ),
			array( 'Contact', '/contact/' ),
		),
		'footer_legal'    => array(
			array( 'Privacy Policy', '/privacy-policy/' ),
			array( 'Terms of Service', '/terms/' ),
		),
	);
	return isset( $links[ $location ] ) ? $links[ $location ] : array();
}

/**
 * Prints a <ul> of links for a menu location, using the assigned menu or
 * the defaults.
 */
function strategro_menu( $location, $class ) {
	if ( has_nav_menu( $location ) ) {
		wp_nav_menu(
			array(
				'theme_location' => $location,
				'container'      => false,
				'menu_class'     => $class,
				'depth'          => 2,
				'fallback_cb'    => false,
			)
		);
		return;
	}

	echo '<ul class="' . esc_attr( $class ) . '">';
	foreach ( strategro_default_links( $location ) as $link ) {
		$external = ! empty( $link[2] );
		$url      = $external ? $link[1] : home_url( $link[1] );
		$current  = ! $external && untrailingslashit( $url ) === untrailingslashit( home_url( add_query_arg( array() ) ) );
		printf(
			'<li class="menu-item%s"><a href="%s"%s>%s</a></li>',
			$current ? ' current-menu-item' : '',
			esc_url( $url ),
			$external ? ' target="_blank" rel="noopener noreferrer"' : '',
			esc_html( $link[0] )
		);
	}
	echo '</ul>';
}

/** External links in assigned menus open in a new tab automatically. */
add_filter( 'nav_menu_link_attributes', 'strategro_external_menu_links', 10, 2 );
function strategro_external_menu_links( $atts, $item ) {
	$host = wp_parse_url( home_url(), PHP_URL_HOST );
	$link = wp_parse_url( $item->url, PHP_URL_HOST );
	if ( $link && $link !== $host ) {
		$atts['target'] = '_blank';
		$atts['rel']    = 'noopener noreferrer';
	}
	return $atts;
}
