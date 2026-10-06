<?php
/**
 * Pages. Elementor-built pages render edge to edge (Elementor owns the
 * layout); plain pages such as Privacy Policy get a title band and a
 * readable text column.
 *
 * @package Strategro
 */

get_header();

while ( have_posts() ) :
	the_post();

	if ( strategro_is_elementor_page( get_the_ID() ) ) :
		the_content();
	else :
		?>
		<section class="sg-band sg-band--dark">
			<div class="sg-wrap">
				<h1 class="sg-band__title sg-split"><?php the_title(); ?></h1>
				<p class="sg-band__meta"><?php echo esc_html( sprintf( __( 'Last updated %s', 'strategro' ), get_the_modified_date( 'j F Y' ) ) ); ?></p>
			</div>
		</section>
		<section class="sg-wrap sg-prose-wrap">
			<div class="sg-prose sg-reveal"><?php the_content(); ?></div>
		</section>
		<?php
	endif;
endwhile;

get_footer();
