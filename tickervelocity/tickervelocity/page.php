<?php
/**
 * Pages (About, Editorial standards, legal pages, Newsletter...).
 *
 * @package TickerVelocity
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<header class="tv-archive-head tv-archive-head--page">
		<div class="tv-wrap">
			<h1 class="tv-archive-head__title"><?php the_title(); ?></h1>
			<?php if ( has_excerpt() ) : ?>
				<p class="tv-archive-head__intro"><?php echo esc_html( get_the_excerpt() ); ?></p>
			<?php endif; ?>
		</div>
	</header>
	<div class="tv-wrap tv-page">
		<div class="tv-prose"><?php the_content(); ?></div>
		<p class="tv-page__updated">
			<?php
			/* translators: %s: date */
			echo esc_html( sprintf( __( 'Last updated %s', 'tickervelocity' ), get_the_modified_date( 'j F Y' ) ) );
			?>
		</p>
	</div>
	<?php
endwhile;

get_footer();
