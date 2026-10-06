<?php
/**
 * Insights (posts page), category/tag archives, search and the fallback
 * for anything else.
 *
 * @package Strategro
 */

get_header();

$strategro_title = __( 'Practical thinking on AI automation.', 'strategro' );
$strategro_eyebrow = __( 'Insights', 'strategro' );
if ( is_archive() ) {
	$strategro_title   = wp_strip_all_tags( get_the_archive_title() );
	$strategro_eyebrow = __( 'Archive', 'strategro' );
} elseif ( is_search() ) {
	/* translators: %s: search query */
	$strategro_title   = sprintf( __( 'Results for "%s"', 'strategro' ), get_search_query() );
	$strategro_eyebrow = __( 'Search', 'strategro' );
}
?>
<section class="sg-band sg-band--dark sg-network">
	<div class="sg-wrap">
		<p class="sg-eyebrow"><?php echo esc_html( $strategro_eyebrow ); ?></p>
		<h1 class="sg-band__title sg-split"><?php echo esc_html( $strategro_title ); ?></h1>
		<?php if ( is_home() ) : ?>
			<p class="sg-band__lead sg-reveal"><?php esc_html_e( 'Notes on what actually works when connecting AI to real business operations, written for the people making the decision.', 'strategro' ); ?></p>
		<?php endif; ?>
	</div>
</section>

<section class="sg-wrap sg-archive">
	<?php if ( have_posts() ) : ?>
		<div class="sg-post-grid sg-stagger">
			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/card', 'post' );
			endwhile;
			?>
		</div>
		<div class="sg-pagination">
			<?php the_posts_pagination( array( 'mid_size' => 1 ) ); ?>
		</div>
	<?php else : ?>
		<p class="sg-empty"><?php esc_html_e( 'Nothing here yet.', 'strategro' ); ?></p>
	<?php endif; ?>
</section>
<?php
get_footer();
