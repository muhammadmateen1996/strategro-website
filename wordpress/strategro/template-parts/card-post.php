<?php
/**
 * Blog post card, used on the Insights archive and by the
 * [strategro_latest_posts] shortcode.
 *
 * @package Strategro
 */

$strategro_categories = get_the_category();
?>
<article <?php post_class( 'sg-post-card sg-glow' ); ?>>
	<a class="sg-post-card__link" href="<?php the_permalink(); ?>">
		<div class="sg-post-card__media">
			<?php if ( has_post_thumbnail() ) : ?>
				<?php the_post_thumbnail( 'medium_large', array( 'loading' => 'lazy' ) ); ?>
			<?php else : ?>
				<span class="sg-post-card__mark" aria-hidden="true">S</span>
			<?php endif; ?>
		</div>
		<div class="sg-post-card__body">
			<p class="sg-post-card__meta">
				<?php if ( $strategro_categories ) : ?>
					<span><?php echo esc_html( $strategro_categories[0]->name ); ?></span>
					<span aria-hidden="true">&middot;</span>
				<?php endif; ?>
				<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'j F Y' ) ); ?></time>
			</p>
			<h3 class="sg-post-card__title"><?php the_title(); ?></h3>
			<p class="sg-post-card__excerpt"><?php echo esc_html( wp_strip_all_tags( get_the_excerpt() ) ); ?></p>
			<span class="sg-post-card__more"><?php esc_html_e( 'Read more', 'strategro' ); ?> <span aria-hidden="true">&rarr;</span></span>
		</div>
	</a>
</article>
