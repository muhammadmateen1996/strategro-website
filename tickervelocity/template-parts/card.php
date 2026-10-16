<?php
/**
 * Story card. $args['variant']: lead, feature, standard, compact or row.
 *
 * @package TickerVelocity
 */

$tv_variant = isset( $args['variant'] ) ? $args['variant'] : 'standard';
$tv_size    = 'lead' === $tv_variant ? 'tv-lead' : 'tv-card';
$tv_excerpt = in_array( $tv_variant, array( 'lead', 'feature', 'row' ), true );
?>
<article <?php post_class( 'tv-card tv-card--' . $tv_variant ); ?>>
	<?php if ( 'compact' !== $tv_variant || has_post_thumbnail() ) : ?>
		<a class="tv-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
			<?php tv_post_media( $tv_size ); ?>
		</a>
	<?php endif; ?>
	<div class="tv-card__body">
		<p class="tv-card__meta">
			<?php if ( tv_is_breaking() ) : ?>
				<span class="tv-badge tv-badge--breaking"><?php esc_html_e( 'Breaking', 'tickervelocity' ); ?></span>
			<?php endif; ?>
			<?php tv_category_chip(); ?>
			<?php echo tv_time_ago(); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in the helper. ?>
		</p>
		<?php if ( 'lead' === $tv_variant ) : ?>
			<h2 class="tv-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<?php else : ?>
			<h3 class="tv-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<?php endif; ?>
		<?php if ( $tv_excerpt ) : ?>
			<p class="tv-card__excerpt"><?php echo esc_html( wp_trim_words( wp_strip_all_tags( get_the_excerpt() ), 'lead' === $tv_variant ? 38 : 24 ) ); ?></p>
		<?php endif; ?>
		<?php
		if ( 'lead' === $tv_variant ) :
			$tv_take = array_slice( tv_post_takeaways(), 0, 3 );
			if ( $tv_take ) :
				?>
				<ul class="tv-card__takeaways">
					<?php foreach ( $tv_take as $tv_point ) : ?>
						<li><?php echo esc_html( $tv_point ); ?></li>
					<?php endforeach; ?>
				</ul>
				<?php
			endif;
		endif;
		?>
	</div>
</article>
