<?php
/**
 * Elementor integration:
 *  - registers the theme's header/footer with Elementor, so an Elementor
 *    Pro Theme Builder template can't silently replace them,
 *  - finds header/footer templates that would replace them,
 *  - adds the "Strategro Motion" panel to every element's Advanced tab,
 *  - registers the Strategro Motion widgets.
 *
 * @package Strategro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ---------------------------------------------------------------------------
 * Header / footer
 * ------------------------------------------------------------------------ */

/**
 * Claiming the header/footer locations tells Elementor Pro the theme draws
 * them itself (through strategro_elementor_location()), so Pro no longer
 * swaps the theme's whole header file for its own.
 */
add_action( 'elementor/theme/register_locations', 'strategro_register_elementor_locations' );
function strategro_register_elementor_locations( $manager ) {
	$manager->register_location( 'header' );
	$manager->register_location( 'footer' );
}

/**
 * Prints an Elementor Pro Theme Builder template for $location if the site
 * has opted in (Customizer > Strategro) and one is assigned. Returns true
 * if a template was printed.
 */
function strategro_elementor_location( $location ) {
	if ( ! strategro_mod( 'strategro_builder_header' ) || ! function_exists( 'elementor_theme_do_location' ) ) {
		return false;
	}
	return (bool) elementor_theme_do_location( $location );
}

/**
 * Templates from Elementor Pro's Theme Builder or the "Elementor Header &
 * Footer Builder" plugin that are switched on for the site. Each item:
 * array( 'title', 'type' (header|footer), 'source', 'active' (whether it
 * actually replaces the Strategro one), 'edit_url' ).
 */
function strategro_header_overrides() {
	$found = array();

	$pro = get_posts(
		array(
			'post_type'      => 'elementor_library',
			'post_status'    => 'publish',
			'posts_per_page' => 50,
			'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery
				array(
					'key'     => '_elementor_template_type',
					'value'   => array( 'header', 'footer' ),
					'compare' => 'IN',
				),
			),
		)
	);
	foreach ( $pro as $template ) {
		$conditions = get_post_meta( $template->ID, '_elementor_conditions', true );
		if ( empty( $conditions ) ) {
			continue;
		}
		$found[] = array(
			'title'    => $template->post_title,
			'type'     => get_post_meta( $template->ID, '_elementor_template_type', true ),
			'source'   => __( 'Elementor Pro Theme Builder', 'strategro' ),
			'active'   => (bool) strategro_mod( 'strategro_builder_header' ),
			'edit_url' => admin_url( 'post.php?post=' . $template->ID . '&action=elementor' ),
		);
	}

	if ( post_type_exists( 'elementor-hf' ) ) {
		$hfe = get_posts(
			array(
				'post_type'      => 'elementor-hf',
				'post_status'    => 'publish',
				'posts_per_page' => 50,
			)
		);
		foreach ( $hfe as $template ) {
			$type = get_post_meta( $template->ID, 'ehf_template_type', true );
			if ( ! in_array( $type, array( 'type_header', 'type_footer' ), true ) ) {
				continue;
			}
			$found[] = array(
				'title'    => $template->post_title,
				'type'     => 'type_header' === $type ? 'header' : 'footer',
				'source'   => __( 'Elementor Header & Footer Builder plugin', 'strategro' ),
				'active'   => true,
				'edit_url' => admin_url( 'post.php?post=' . $template->ID . '&action=edit' ),
			);
		}
	}

	return $found;
}

/* ---------------------------------------------------------------------------
 * Motion panel (Advanced tab of every widget and container)
 * ------------------------------------------------------------------------ */

foreach ( array( 'common', 'common-optimized' ) as $strategro_stack ) {
	add_action( "elementor/element/{$strategro_stack}/_section_style/after_section_end", 'strategro_widget_motion_controls' );
}
add_action( 'elementor/element/container/section_effects/after_section_end', 'strategro_container_motion_controls' );

function strategro_motion_select( $element, $name, $label, $options, $prefix = 'sg-', $description = '' ) {
	$element->add_control(
		$name,
		array(
			'label'        => $label,
			'type'         => \Elementor\Controls_Manager::SELECT,
			'options'      => $options,
			'default'      => '',
			'prefix_class' => $prefix,
			'description'  => $description,
		)
	);
}

function strategro_motion_switch( $element, $name, $label, $class, $description = '' ) {
	$element->add_control(
		$name,
		array(
			'label'        => $label,
			'type'         => \Elementor\Controls_Manager::SWITCHER,
			'return_value' => $class,
			'default'      => '',
			'prefix_class' => 'sg-',
			'description'  => $description,
		)
	);
}

/** Controls shared by widgets and containers. */
function strategro_motion_common_controls( $element ) {
	strategro_motion_select(
		$element,
		'sg_entrance',
		__( 'Entrance', 'strategro' ),
		array(
			''      => __( 'None', 'strategro' ),
			'reveal' => __( 'Fade up from blur', 'strategro' ),
			'split' => __( 'Words slide up (headings)', 'strategro' ),
		)
	);
	strategro_motion_select(
		$element,
		'sg_delay',
		__( 'Entrance delay', 'strategro' ),
		array(
			''  => __( 'None', 'strategro' ),
			'1' => '0.1s',
			'2' => '0.2s',
			'3' => '0.3s',
			'4' => '0.4s',
			'5' => '0.5s',
			'6' => '0.6s',
		),
		'sg-d'
	);
	strategro_motion_select(
		$element,
		'sg_scroll',
		__( 'While scrolling', 'strategro' ),
		array(
			''              => __( 'Nothing', 'strategro' ),
			'parallax'      => __( 'Parallax (drifts slower)', 'strategro' ),
			'parallax-fast' => __( 'Parallax (drifts faster)', 'strategro' ),
		)
	);
	strategro_motion_select(
		$element,
		'sg_hover',
		__( 'Cursor effect', 'strategro' ),
		array(
			''         => __( 'None', 'strategro' ),
			'tilt'     => __( '3D tilt with glare', 'strategro' ),
			'magnetic' => __( 'Magnetic (drifts to cursor)', 'strategro' ),
			'glow'     => __( 'Gold glow and lift', 'strategro' ),
		)
	);
	strategro_motion_select(
		$element,
		'sg_surface',
		__( 'Surface', 'strategro' ),
		array(
			''            => __( 'None', 'strategro' ),
			'glass'       => __( 'Frosted glass (dark)', 'strategro' ),
			'glass-light' => __( 'Frosted glass (light)', 'strategro' ),
			'card'        => __( 'White card', 'strategro' ),
			'orbit'       => __( 'Orbiting light border', 'strategro' ),
		)
	);
	strategro_motion_switch( $element, 'sg_counter', __( 'Count up numbers', 'strategro' ), 'counter', __( 'Numbers in the text count up from zero.', 'strategro' ) );
}

function strategro_motion_section_start( $element ) {
	$element->start_controls_section(
		'sg_section_motion',
		array(
			'label' => '✦ ' . __( 'Strategro Motion', 'strategro' ),
			'tab'   => \Elementor\Controls_Manager::TAB_ADVANCED,
		)
	);
	$element->add_control(
		'sg_motion_note',
		array(
			'type'            => \Elementor\Controls_Manager::RAW_HTML,
			'raw'             => esc_html__( 'Animations play on the live site, not in the editor, so everything stays easy to edit here.', 'strategro' ),
			'content_classes' => 'elementor-descriptor',
		)
	);
}

function strategro_widget_motion_controls( $element ) {
	// With Elementor's optimised markup both stacks above fire for the same widgets.
	if ( $element->get_controls( 'sg_section_motion' ) ) {
		return;
	}
	strategro_motion_section_start( $element );
	strategro_motion_common_controls( $element );
	$element->end_controls_section();
}

function strategro_container_motion_controls( $element ) {
	strategro_motion_section_start( $element );
	strategro_motion_select(
		$element,
		'sg_skin',
		__( 'Section skin', 'strategro' ),
		array(
			''      => __( 'None', 'strategro' ),
			'dark'  => __( 'Dark (ink)', 'strategro' ),
			'light' => __( 'Light (paper)', 'strategro' ),
			'paper' => __( 'Warm paper', 'strategro' ),
			'gold'  => __( 'Gold', 'strategro' ),
		),
		'sg-',
		__( 'Background colour with matching, readable text colours.', 'strategro' )
	);
	strategro_motion_common_controls( $element );
	strategro_motion_switch( $element, 'sg_stagger', __( 'Children appear one by one', 'strategro' ), 'stagger' );
	$element->add_control(
		'sg_bg_heading',
		array(
			'label'     => __( 'Animated background', 'strategro' ),
			'type'      => \Elementor\Controls_Manager::HEADING,
			'separator' => 'before',
		)
	);
	strategro_motion_switch( $element, 'sg_bg_network', __( 'Signal network', 'strategro' ), 'network', __( 'Glowing nodes and moving signals that react to the cursor.', 'strategro' ) );
	strategro_motion_switch( $element, 'sg_bg_aurora', __( 'Aurora light', 'strategro' ), 'aurora' );
	strategro_motion_switch( $element, 'sg_bg_grid', __( 'Blueprint grid', 'strategro' ), 'grid-bg' );
	strategro_motion_switch( $element, 'sg_bg_spot', __( 'Cursor spotlight', 'strategro' ), 'spot' );
	strategro_motion_switch( $element, 'sg_marquee', __( 'Scroll children sideways forever', 'strategro' ), 'marquee', __( 'Turns this container into a moving ticker.', 'strategro' ) );
	$element->end_controls_section();
}

/* ---------------------------------------------------------------------------
 * Widgets
 * ------------------------------------------------------------------------ */

add_action( 'elementor/elements/categories_registered', 'strategro_widget_category' );
function strategro_widget_category( $manager ) {
	$manager->add_category(
		'strategro',
		array(
			'title' => __( 'Strategro Motion', 'strategro' ),
			'icon'  => 'eicon-animation',
		)
	);
}

add_action( 'elementor/widgets/register', 'strategro_register_widgets' );
function strategro_register_widgets( $manager ) {
	require_once STRATEGRO_DIR . '/inc/widgets/class-widgets.php';
	foreach ( strategro_widget_classes() as $class ) {
		$manager->register( new $class() );
	}
}
