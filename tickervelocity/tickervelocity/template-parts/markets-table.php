<?php
/**
 * Markets snapshot table (sidebars). Refreshed in the browser with the strip.
 *
 * @package TickerVelocity
 */

$tv_market = tv_market_snapshot();
if ( ! $tv_market['items'] ) {
	return;
}
?>
<section class="tv-panel tv-markets" data-tv-markets>
	<div class="tv-panel__head">
		<h2 class="tv-panel__title"><?php esc_html_e( 'Markets', 'tickervelocity' ); ?></h2>
		<span class="tv-panel__note" data-tv-strip-time><?php echo $tv_market['updated'] ? esc_html( wp_date( 'H:i', $tv_market['updated'] ) ) : ''; ?></span>
	</div>
	<table>
		<tbody data-tv-markets-body>
			<?php foreach ( array_slice( $tv_market['items'], 0, 10 ) as $tv_item ) : ?>
				<tr class="tv-quote--<?php echo esc_attr( $tv_item['dir'] ); ?>">
					<th scope="row"><?php echo esc_html( $tv_item['symbol'] ); ?></th>
					<td><?php echo esc_html( $tv_item['price'] ); ?></td>
					<td class="tv-markets__chg"><?php echo esc_html( $tv_item['change'] ); ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<p class="tv-panel__fine"><?php esc_html_e( 'Delayed data, for information only.', 'tickervelocity' ); ?></p>
</section>
