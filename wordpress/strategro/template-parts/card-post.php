<?php
/**
 * Blog post card, used on the Insights pages, under articles ("Keep
 * reading") and by the [strategro_latest_posts] shortcode.
 *
 * @package Strategro
 */

$strategro_term = strategro_primary_category();
?>
<article <?php post_class( 'sg-post-card sg-spot' ); ?>>
	<a class="sg-post-card__link" href="<?php the_permalink(); ?>">
		<span class="sg-post-card__media">
			<?php strategro_post_media( 'medium_large' ); ?>
			<?php if ( $strategro_term ) : ?>
				<span class="sg-post-card__cat"><?php echo esc_html( $strategro_term->name ); ?></span>
			<?php endif; ?>
		</span>
		<span class="sg-post-card__body">
			<span class="sg-post-card__meta">
				<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'j M Y' ) ); ?></time>
				<span aria-hidden="true">&middot;</span>
				<span><?php echo esc_html( strategro_reading_label() ); ?></span>
			</span>
			<span class="sg-post-card__title"><?php the_title(); ?></span>
			<span class="sg-post-card__excerpt"><?php echo esc_html( wp_trim_words( wp_strip_all_tags( get_the_excerpt() ), 26 ) ); ?></span>
			<span class="sg-post-card__more">
				<?php esc_html_e( 'Read article', 'strategro' ); ?>
				<span class="sg-post-card__arrow" aria-hidden="true">&rarr;</span>
			</span>
		</span>
	</a>
</article>
