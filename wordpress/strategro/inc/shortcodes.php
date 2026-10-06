<?php
/**
 * [strategro_latest_posts count="3"] — latest blog posts as cards. Lets a
 * free Elementor Shortcode widget show recent posts without needing the
 * Pro-only Posts widget.
 *
 * @package Strategro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_shortcode( 'strategro_latest_posts', 'strategro_latest_posts_shortcode' );
function strategro_latest_posts_shortcode( $atts ) {
	$atts  = shortcode_atts( array( 'count' => 3 ), $atts, 'strategro_latest_posts' );
	$query = new WP_Query(
		array(
			'posts_per_page'      => max( 1, (int) $atts['count'] ),
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
		)
	);

	if ( ! $query->have_posts() ) {
		return '<p class="sg-empty">' . esc_html__( 'New articles are on the way.', 'strategro' ) . '</p>';
	}

	ob_start();
	echo '<div class="sg-post-grid">';
	while ( $query->have_posts() ) {
		$query->the_post();
		get_template_part( 'template-parts/card', 'post' );
	}
	echo '</div>';
	wp_reset_postdata();
	return ob_get_clean();
}
