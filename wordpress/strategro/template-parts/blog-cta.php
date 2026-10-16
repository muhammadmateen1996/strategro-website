<?php
/**
 * Closing call to action on the Insights pages and under every article.
 *
 * @package Strategro
 */

$strategro_cta_url = strategro_mod( 'strategro_cta_url' );
$strategro_cta_url = 0 === strpos( $strategro_cta_url, '/' ) ? home_url( $strategro_cta_url ) : $strategro_cta_url;
?>
<section class="sg-blog-cta-wrap">
	<div class="sg-wrap">
		<div class="sg-blog-cta sg-band--dark sg-aurora sg-spot sg-reveal">
			<div class="sg-blog-cta__copy">
				<p class="sg-kicker"><?php esc_html_e( 'From reading to running', 'strategro' ); ?></p>
				<h2 class="sg-blog-cta__title"><?php esc_html_e( 'See what your business could', 'strategro' ); ?> <span class="sg-gradient-text"><?php esc_html_e( 'automate first.', 'strategro' ); ?></span></h2>
				<p class="sg-blog-cta__text"><?php esc_html_e( 'A free, no-obligation AI Systems Audit. We map where time and leads slip away, and show you what to fix first.', 'strategro' ); ?></p>
			</div>
			<div class="sg-blog-cta__actions">
				<a class="sg-button sg-arrow sg-magnetic" href="<?php echo esc_url( $strategro_cta_url ); ?>"><span><?php echo esc_html( strategro_mod( 'strategro_cta_label' ) ); ?></span></a>
				<a class="sg-text-link sg-text-link--light" href="<?php echo esc_url( home_url( '/products/' ) ); ?>"><?php esc_html_e( 'Explore our products', 'strategro' ); ?> &rarr;</a>
			</div>
		</div>
	</div>
</section>
