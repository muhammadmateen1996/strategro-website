<?php
/**
 * Board of live price cards with group tabs.
 * $args: group (only one group), limit, tabs (bool), title.
 *
 * @package TickerVelocity
 */

$tv_group  = isset( $args['group'] ) ? $args['group'] : '';
$tv_limit  = isset( $args['limit'] ) ? (int) $args['limit'] : 0;
$tv_tabs   = ! isset( $args['tabs'] ) || $args['tabs'];
$tv_title  = isset( $args['title'] ) ? $args['title'] : __( 'Markets now', 'tickervelocity' );
$tv_cols   = isset( $args['columns'] ) ? (int) $args['columns'] : 0;
$tv_prices = tv_prices( $tv_group );
if ( $tv_limit && ! $tv_group ) {
	// A mix of every group (crypto, stocks, commodities...), not just the first list.
	$tv_by_group = array();
	foreach ( $tv_prices as $tv_symbol => $tv_asset ) {
		$tv_by_group[ $tv_asset['group'] ][ $tv_symbol ] = $tv_asset;
	}
	$tv_mixed = array();
	while ( count( $tv_mixed ) < $tv_limit && array_filter( $tv_by_group ) ) {
		foreach ( $tv_by_group as $tv_key => $tv_list ) {
			if ( $tv_list && count( $tv_mixed ) < $tv_limit ) {
				$tv_mixed[ key( $tv_list ) ] = reset( $tv_list );
				array_shift( $tv_by_group[ $tv_key ] );
			}
		}
	}
	$tv_prices = $tv_mixed;
} elseif ( $tv_limit ) {
	$tv_prices = array_slice( $tv_prices, 0, $tv_limit, true );
}
if ( ! $tv_prices ) {
	if ( current_user_can( 'manage_options' ) ) {
		echo '<p class="tv-board__setup">' . esc_html__( 'Live prices appear here once the first prices arrive (a few minutes after setup). For stocks, add a free Finnhub key in Customize > TickerVelocity > Live prices. Only you can see this note.', 'tickervelocity' ) . '</p>';
	}
	return;
}
$tv_present = array_unique( wp_list_pluck( $tv_prices, 'group' ) );
$tv_updated = tv_prices_raw()['updated'];
?>
<section class="tv-board" data-tv-board>
	<div class="tv-board__head">
		<h2 class="tv-board__title"><span class="tv-dot tv-dot--live"></span><?php echo esc_html( $tv_title ); ?></h2>
		<?php if ( $tv_tabs && ! $tv_group && count( $tv_present ) > 1 ) : ?>
			<div class="tv-tabs" role="tablist" aria-label="<?php esc_attr_e( 'Filter prices', 'tickervelocity' ); ?>">
				<button type="button" class="tv-tab is-active" data-tv-filter="" role="tab" aria-selected="true"><?php esc_html_e( 'All', 'tickervelocity' ); ?></button>
				<?php foreach ( tv_price_groups() as $tv_key => $tv_label ) : ?>
					<?php if ( in_array( $tv_key, $tv_present, true ) ) : ?>
						<button type="button" class="tv-tab" data-tv-filter="<?php echo esc_attr( $tv_key ); ?>" role="tab" aria-selected="false"><?php echo esc_html( $tv_label ); ?></button>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
		<span class="tv-board__time"><?php esc_html_e( 'Updated', 'tickervelocity' ); ?> <span data-tv-strip-time><?php echo $tv_updated ? esc_html( wp_date( 'H:i', $tv_updated ) ) : ''; ?></span></span>
	</div>
	<div class="tv-cards<?php echo $tv_cols ? ' tv-cards--' . esc_attr( $tv_cols ) : ''; ?>">
		<?php
		foreach ( $tv_prices as $tv_asset ) {
			get_template_part( 'template-parts/price-card', null, array( 'asset' => $tv_asset ) );
		}
		?>
	</div>
	<p class="tv-board__fine"><?php esc_html_e( 'Prices may be delayed and are for information only. Crypto data by CoinGecko; stocks and funds by Finnhub.', 'tickervelocity' ); ?></p>
</section>
