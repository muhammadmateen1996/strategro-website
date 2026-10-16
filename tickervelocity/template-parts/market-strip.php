<?php
/**
 * Scrolling market strip under the header. Printed with the last known
 * prices, then refreshed every minute in the browser.
 *
 * @package TickerVelocity
 */

$tv_market = tv_market_snapshot();
?>
<div class="tv-strip" data-tv-strip<?php echo $tv_market['items'] ? '' : ' hidden'; ?>>
	<div class="tv-strip__label">
		<span class="tv-dot tv-dot--live"></span><?php esc_html_e( 'Markets', 'tickervelocity' ); ?>
		<span class="tv-strip__delay" data-tv-strip-time><?php echo $tv_market['updated'] ? esc_html( wp_date( 'H:i', $tv_market['updated'] ) ) : ''; ?></span>
	</div>
	<div class="tv-strip__viewport">
		<div class="tv-strip__track" data-tv-strip-track>
			<?php for ( $tv_copy = 0; $tv_copy < 2; $tv_copy++ ) : ?>
				<ul class="tv-strip__group"<?php echo $tv_copy ? ' aria-hidden="true"' : ''; ?>>
					<?php foreach ( $tv_market['items'] as $tv_item ) : ?>
						<li class="tv-quote tv-quote--<?php echo esc_attr( $tv_item['dir'] ); ?>">
							<span class="tv-quote__sym"><?php echo esc_html( $tv_item['symbol'] ); ?></span>
							<span class="tv-quote__px"><?php echo esc_html( $tv_item['price'] ); ?></span>
							<span class="tv-quote__chg"><?php echo esc_html( $tv_item['change'] ); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endfor; ?>
		</div>
	</div>
</div>
