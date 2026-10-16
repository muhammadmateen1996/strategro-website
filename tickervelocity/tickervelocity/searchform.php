<?php
/**
 * Search form.
 *
 * @package TickerVelocity
 */

$tv_field = wp_unique_id( 'tv-s-' );
?>
<form role="search" method="get" class="tv-searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="<?php echo esc_attr( $tv_field ); ?>"><?php esc_html_e( 'Search news', 'tickervelocity' ); ?></label>
	<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
	<input id="<?php echo esc_attr( $tv_field ); ?>" type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Search tickers, companies, topics…', 'tickervelocity' ); ?>">
	<button type="submit"><?php esc_html_e( 'Search', 'tickervelocity' ); ?></button>
</form>
