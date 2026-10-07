<?php
/**
 * Appearance > Customize > Strategro: the few site-wide bits that live in
 * the theme (header button, footer text, Clara chat) rather than in
 * Elementor pages.
 *
 * @package Strategro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function strategro_mod( $key ) {
	$defaults = array(
		'strategro_cta_label'        => 'Book an AI Systems Audit',
		'strategro_cta_url'          => '/contact/',
		'strategro_footer_blurb'     => 'Strategro builds AI products and automation systems for ambitious UK and UAE businesses.',
		'strategro_footer_markets'   => 'Operating in the United Kingdom & United Arab Emirates',
		'strategro_email'            => 'mateen@strategro.co.uk',
		'strategro_linkedin'         => 'https://www.linkedin.com/in/muhammadmateen/',
		'strategro_legal_name'       => 'Strategro Ltd',
		'strategro_clara_key'        => 'pk_live_xZmsc3UKTuuVEhfcH-_SwLz-iQNsPAAi',
		'strategro_clara_script'     => 'https://api.strategro.co.uk/embed/v1/clara.js',
		'strategro_page_transitions' => true,
		'strategro_sticky_header'    => true,
	);
	return get_theme_mod( $key, isset( $defaults[ $key ] ) ? $defaults[ $key ] : '' );
}

add_action( 'customize_register', 'strategro_customize_register' );
function strategro_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'strategro',
		array(
			'title'    => __( 'Strategro', 'strategro' ),
			'priority' => 30,
		)
	);

	$fields = array(
		'strategro_cta_label'      => array( __( 'Header button text', 'strategro' ), 'text', 'sanitize_text_field' ),
		'strategro_cta_url'        => array( __( 'Header button link', 'strategro' ), 'url', 'esc_url_raw' ),
		'strategro_footer_blurb'   => array( __( 'Footer description', 'strategro' ), 'textarea', 'sanitize_textarea_field' ),
		'strategro_footer_markets' => array( __( 'Footer markets line', 'strategro' ), 'text', 'sanitize_text_field' ),
		'strategro_email'          => array( __( 'Contact email', 'strategro' ), 'email', 'sanitize_email' ),
		'strategro_linkedin'       => array( __( 'LinkedIn URL', 'strategro' ), 'url', 'esc_url_raw' ),
		'strategro_legal_name'     => array( __( 'Legal company name (footer)', 'strategro' ), 'text', 'sanitize_text_field' ),
		'strategro_clara_key'      => array( __( 'Clara chat widget key (leave empty to hide the chat bubble)', 'strategro' ), 'text', 'sanitize_text_field' ),
		'strategro_clara_script'   => array( __( 'Clara chat script URL', 'strategro' ), 'url', 'esc_url_raw' ),
	);

	foreach ( $fields as $id => $field ) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => strategro_mod( $id ),
				'sanitize_callback' => $field[2],
			)
		);
		$wp_customize->add_control(
			$id,
			array(
				'label'   => $field[0],
				'type'    => $field[1],
				'section' => 'strategro',
			)
		);
	}

	$wp_customize->add_setting(
		'strategro_sticky_header',
		array(
			'default'           => true,
			'sanitize_callback' => 'rest_sanitize_boolean',
		)
	);
	$wp_customize->add_control(
		'strategro_sticky_header',
		array(
			'label'       => __( 'Sticky header', 'strategro' ),
			'description' => __( 'Keep the header at the top of the screen while scrolling.', 'strategro' ),
			'type'        => 'checkbox',
			'section'     => 'strategro',
		)
	);

	$wp_customize->add_setting(
		'strategro_page_transitions',
		array(
			'default'           => true,
			'sanitize_callback' => 'rest_sanitize_boolean',
		)
	);
	$wp_customize->add_control(
		'strategro_page_transitions',
		array(
			'label'   => __( 'Animated page transitions and loader', 'strategro' ),
			'type'    => 'checkbox',
			'section' => 'strategro',
		)
	);
}
