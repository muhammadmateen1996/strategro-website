<?php
/**
 * Small template helpers.
 *
 * @package Strategro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** The Strategro signal mark (three inputs converging on one system). */
function strategro_mark() {
	?>
	<svg class="sg-mark" width="30" height="30" viewBox="0 0 30 30" fill="none" aria-hidden="true">
		<circle cx="5" cy="6" r="2" fill="#c99a44"/>
		<circle cx="5" cy="15" r="2" fill="#c99a44" fill-opacity=".7"/>
		<circle cx="5" cy="24" r="2" fill="#c99a44" fill-opacity=".4"/>
		<path d="M7 6 L15 15 M7 15 H15 M7 24 L15 15" stroke="currentColor" stroke-opacity=".4" stroke-width="1.2"/>
		<rect x="15" y="10" width="10" height="10" rx="3" fill="#c99a44"/>
	</svg>
	<?php
}

/** Uploaded logo (Appearance > Customize > Site Identity) or the default mark + wordmark. */
function strategro_brand() {
	if ( has_custom_logo() ) {
		the_custom_logo();
		return;
	}
	printf( '<a class="sg-brand" href="%s" rel="home" aria-label="%s">', esc_url( home_url( '/' ) ), esc_attr( get_bloginfo( 'name' ) . ' home' ) );
	strategro_mark();
	printf( '<span class="sg-brand__name">%s</span></a>', esc_html( get_bloginfo( 'name' ) ) );
}
