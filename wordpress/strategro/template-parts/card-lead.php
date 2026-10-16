<?php
/**
 * The large card that opens each page of the Insights listing.
 *
 * @package Strategro
 */

$strategro_term  = strategro_primary_category();
$strategro_badge = isset( $args['badge'] ) ? $args['badge'] : '';
?>
<article <?php post_class( 'sg-lead-card sg-reveal' ); ?>>
	<a class="sg-lead-card__link" href="<?php the_permalink(); ?>">
		<span class="sg-lead-card__media">
			<?php strategro_post_media( 'large' ); ?>
			<?php if ( $strategro_badge ) : ?>
				<span class="sg-lead-card__badge"><?php echo esc_html( $strategro_badge ); ?></span>
			<?php endif; ?>
		</span>
		<span class="sg-lead-card__body">
			<span class="sg-lead-card__meta">
				<?php if ( $strategro_term ) : ?>
					<span class="sg-lead-card__cat"><?php echo esc_html( $strategro_term->name ); ?></span>
				<?php endif; ?>
				<span><?php echo esc_html( strategro_reading_label() ); ?></span>
			</span>
			<span class="sg-lead-card__title"><?php the_title(); ?></span>
			<span class="sg-lead-card__excerpt"><?php echo esc_html( wp_trim_words( wp_strip_all_tags( get_the_excerpt() ), 40 ) ); ?></span>
			<span class="sg-lead-card__foot">
				<span class="sg-byline">
					<?php echo get_avatar( get_the_author_meta( 'ID' ), 40, '', '', array( 'class' => 'sg-byline__avatar' ) ); ?>
					<span>
						<span class="sg-byline__name"><?php the_author(); ?></span>
						<time class="sg-byline__date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'j F Y' ) ); ?></time>
					</span>
				</span>
				<span class="sg-lead-card__go"><?php esc_html_e( 'Read the article', 'strategro' ); ?> <span aria-hidden="true">&rarr;</span></span>
			</span>
		</span>
	</a>
</article>
