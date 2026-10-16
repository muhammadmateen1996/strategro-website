<?php
/**
 * Not found.
 *
 * @package TickerVelocity
 */

get_header();
?>
<section class="tv-wrap tv-empty">
	<?php tv_mark(); ?>
	<p class="tv-eyebrow">404</p>
	<h1><?php esc_html_e( 'That page has been delisted.', 'tickervelocity' ); ?></h1>
	<p><?php esc_html_e( 'It may have moved or never existed. Search, or see what is moving markets now.', 'tickervelocity' ); ?></p>
	<?php get_search_form(); ?>
	<p><a class="tv-btn" href="<?php echo esc_url( tv_latest_url() ); ?>"><?php esc_html_e( 'Latest news', 'tickervelocity' ); ?> &rarr;</a></p>
</section>
<?php
get_footer();
