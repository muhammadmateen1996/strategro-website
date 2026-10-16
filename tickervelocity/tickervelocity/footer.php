<?php
/**
 * Site footer.
 *
 * @package TickerVelocity
 */
?>
</main>

<footer class="tv-footer">
	<div class="tv-wrap">
		<div class="tv-footer__top">
			<div class="tv-footer__brand">
				<?php tv_brand(); ?>
				<p class="tv-footer__tagline"><?php echo esc_html( tv_mod( 'tv_tagline' ) ); ?></p>
				<?php get_template_part( 'template-parts/newsletter', null, array( 'variant' => 'footer' ) ); ?>
			</div>
			<div class="tv-footer__col">
				<p class="tv-footer__title"><?php esc_html_e( 'Sections', 'tickervelocity' ); ?></p>
				<?php tv_menu( 'primary', 'tv-footer__list' ); ?>
			</div>
			<div class="tv-footer__col">
				<p class="tv-footer__title"><?php esc_html_e( 'TickerVelocity', 'tickervelocity' ); ?></p>
				<?php tv_menu( 'footer', 'tv-footer__list' ); ?>
			</div>
			<div class="tv-footer__col">
				<p class="tv-footer__title"><?php esc_html_e( 'Legal', 'tickervelocity' ); ?></p>
				<?php tv_menu( 'legal', 'tv-footer__list' ); ?>
			</div>
		</div>

		<p class="tv-footer__disclaimer"><?php echo esc_html( tv_mod( 'tv_disclaimer' ) ); ?></p>

		<div class="tv-footer__bottom">
			<p>&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'Market data is delayed and for information only.', 'tickervelocity' ); ?> <?php esc_html_e( 'Crypto prices by', 'tickervelocity' ); ?> <a href="https://www.coingecko.com" target="_blank" rel="noopener">CoinGecko</a>.</p>
			<?php if ( tv_mod( 'tv_contact_email' ) ) : ?>
				<a href="mailto:<?php echo esc_attr( tv_mod( 'tv_contact_email' ) ); ?>"><?php echo esc_html( tv_mod( 'tv_contact_email' ) ); ?></a>
			<?php endif; ?>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
