<?php
/**
 * Key takeaways and market impact boxes at the top of an article.
 *
 * @package TickerVelocity
 */

$tv_take      = tv_takeaways();
$tv_tickers   = tv_tickers();
$tv_sentiment = strtolower( trim( (string) get_post_meta( get_the_ID(), 'tv_sentiment', true ) ) );
$tv_horizon   = trim( (string) get_post_meta( get_the_ID(), 'tv_horizon', true ) );
$tv_labels    = array(
	'bullish' => __( 'Bullish', 'tickervelocity' ),
	'bearish' => __( 'Bearish', 'tickervelocity' ),
	'mixed'   => __( 'Mixed', 'tickervelocity' ),
	'neutral' => __( 'Neutral', 'tickervelocity' ),
);
if ( ! $tv_take && ! $tv_tickers ) {
	return;
}
?>
<div class="tv-brief">
	<?php if ( $tv_take ) : ?>
		<section class="tv-brief__take" aria-labelledby="tv-take-title">
			<h2 class="tv-brief__title" id="tv-take-title"><?php esc_html_e( 'Key takeaways', 'tickervelocity' ); ?></h2>
			<ul>
				<?php foreach ( $tv_take as $tv_point ) : ?>
					<li><?php echo esc_html( $tv_point ); ?></li>
				<?php endforeach; ?>
			</ul>
		</section>
	<?php endif; ?>

	<?php if ( $tv_tickers ) : ?>
		<section class="tv-brief__impact" aria-labelledby="tv-impact-title">
			<div class="tv-brief__head">
				<h2 class="tv-brief__title" id="tv-impact-title"><?php esc_html_e( 'Market impact', 'tickervelocity' ); ?></h2>
				<?php if ( isset( $tv_labels[ $tv_sentiment ] ) ) : ?>
					<span class="tv-sentiment tv-sentiment--<?php echo esc_attr( $tv_sentiment ); ?>"><?php echo esc_html( $tv_labels[ $tv_sentiment ] ); ?></span>
				<?php endif; ?>
				<?php if ( $tv_horizon ) : ?>
					<span class="tv-brief__horizon"><?php echo esc_html( $tv_horizon ); ?></span>
				<?php endif; ?>
			</div>
			<ul class="tv-impact">
				<?php foreach ( $tv_tickers as $tv_row ) : ?>
					<li class="tv-impact__row tv-impact__row--<?php echo esc_attr( $tv_row['dir'] ); ?>">
						<span class="tv-impact__sym"><?php echo esc_html( $tv_row['symbol'] ); ?></span>
						<span class="tv-impact__dir" aria-label="<?php echo esc_attr( $tv_row['dir'] ); ?>"><?php echo 'up' === $tv_row['dir'] ? '&#9650;' : ( 'down' === $tv_row['dir'] ? '&#9660;' : '&#9644;' ); ?></span>
						<?php if ( $tv_row['note'] ) : ?>
							<span class="tv-impact__note"><?php echo esc_html( $tv_row['note'] ); ?></span>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
			<p class="tv-brief__fine"><?php esc_html_e( 'Our read of the likely direction, not a forecast or advice.', 'tickervelocity' ); ?></p>
		</section>
	<?php endif; ?>
</div>
