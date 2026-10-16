<?php
/**
 * One live price card. $args['asset'] comes from tv_prices().
 *
 * @package TickerVelocity
 */

$tv_a = $args['asset'];
?>
<article class="tv-pc tv-pc--<?php echo esc_attr( $tv_a['dir'] ); ?>" data-tv-asset="<?php echo esc_attr( $tv_a['symbol'] ); ?>" data-group="<?php echo esc_attr( $tv_a['group'] ); ?>">
	<div class="tv-pc__head">
		<span class="tv-pc__badge" data-group="<?php echo esc_attr( $tv_a['group'] ); ?>" aria-hidden="true"><?php echo esc_html( substr( preg_replace( '/[^A-Z0-9]/', '', strtoupper( $tv_a['symbol'] ) ), 0, 3 ) ); ?></span>
		<span class="tv-pc__id">
			<span class="tv-pc__sym"><?php echo esc_html( $tv_a['symbol'] ); ?></span>
			<span class="tv-pc__name"><?php echo esc_html( $tv_a['name'] ); ?></span>
		</span>
		<span class="tv-pc__chg" data-tv-chg><?php echo esc_html( $tv_a['change'] ); ?></span>
	</div>
	<p class="tv-pc__price" data-tv-px data-value="<?php echo esc_attr( $tv_a['value'] ); ?>"><?php echo esc_html( $tv_a['price'] ); ?></p>
	<svg class="tv-pc__spark" viewBox="0 0 120 36" preserveAspectRatio="none" aria-hidden="true"<?php echo $tv_a['spark'] ? '' : ' hidden'; ?>>
		<polygon data-tv-area points="<?php echo esc_attr( $tv_a['spark'] ? '0,36 ' . $tv_a['spark'] . ' 120,36' : '' ); ?>"/>
		<polyline data-tv-line points="<?php echo esc_attr( $tv_a['spark'] ); ?>"/>
	</svg>
	<div class="tv-pc__range"<?php echo ! $tv_a['spark'] && null !== $tv_a['range'] ? '' : ' hidden'; ?> aria-hidden="true">
		<span data-tv-range style="left:<?php echo esc_attr( null !== $tv_a['range'] ? max( 0, min( 100, $tv_a['range'] ) ) : 50 ); ?>%"></span>
	</div>
	<p class="tv-pc__foot">
		<span data-tv-low><?php echo $tv_a['low'] ? esc_html__( 'L', 'tickervelocity' ) . ' ' . esc_html( $tv_a['low'] ) : ''; ?></span>
		<span data-tv-span><?php echo esc_html( $tv_a['span'] ); ?></span>
		<span data-tv-high><?php echo $tv_a['high'] ? esc_html__( 'H', 'tickervelocity' ) . ' ' . esc_html( $tv_a['high'] ) : ''; ?></span>
	</p>
</article>
