<?php
/**
 * Site footer.
 *
 * @package Strategro
 */

$strategro_email    = strategro_mod( 'strategro_email' );
$strategro_linkedin = strategro_mod( 'strategro_linkedin' );
?>
</main>

<?php if ( ! strategro_elementor_location( 'footer' ) ) : ?>
<footer class="sg-footer">
	<div class="sg-footer__glow" aria-hidden="true"></div>
	<div class="sg-footer__inner">
		<div class="sg-footer__grid">
			<div class="sg-footer__brand">
				<?php strategro_brand(); ?>
				<p class="sg-footer__blurb"><?php echo esc_html( strategro_mod( 'strategro_footer_blurb' ) ); ?></p>
				<p class="sg-footer__markets"><?php echo esc_html( strategro_mod( 'strategro_footer_markets' ) ); ?></p>
			</div>

			<div class="sg-footer__col">
				<p class="sg-footer__title"><?php esc_html_e( 'Products', 'strategro' ); ?></p>
				<?php strategro_menu( 'footer_products', 'sg-footer__list' ); ?>
			</div>
			<div class="sg-footer__col">
				<p class="sg-footer__title"><?php esc_html_e( 'Company', 'strategro' ); ?></p>
				<?php strategro_menu( 'footer_company', 'sg-footer__list' ); ?>
			</div>
			<div class="sg-footer__col">
				<p class="sg-footer__title"><?php esc_html_e( 'Legal', 'strategro' ); ?></p>
				<?php strategro_menu( 'footer_legal', 'sg-footer__list' ); ?>
			</div>
		</div>

		<div class="sg-footer__bottom">
			<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( strategro_mod( 'strategro_legal_name' ) ); ?>. <?php esc_html_e( 'All rights reserved.', 'strategro' ); ?></p>
			<div class="sg-footer__contact">
				<?php if ( $strategro_linkedin ) : ?>
					<a href="<?php echo esc_url( $strategro_linkedin ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Strategro on LinkedIn', 'strategro' ); ?>">
						<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true"><path d="M20.45 20.45h-3.56v-5.57c0-1.33-.02-3.04-1.85-3.04-1.85 0-2.14 1.45-2.14 2.94v5.67H9.34V9h3.42v1.56h.05c.48-.9 1.64-1.85 3.38-1.85 3.6 0 4.27 2.37 4.27 5.45v6.29zM5.34 7.43a2.07 2.07 0 1 1 0-4.13 2.07 2.07 0 0 1 0 4.13zM7.12 20.45H3.56V9h3.56v11.45z"/></svg>
					</a>
				<?php endif; ?>
				<?php if ( $strategro_email ) : ?>
					<a href="mailto:<?php echo esc_attr( $strategro_email ); ?>"><?php echo esc_html( $strategro_email ); ?></a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</footer>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
