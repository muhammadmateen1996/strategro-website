<?php
/**
 * One partner offer, always labelled as an affiliate link with a risk warning.
 *
 * @package TickerVelocity
 */

$tv_offer      = $args['offer'];
$tv_disclosure = get_page_by_path( 'affiliate-disclosure' );
?>
<aside class="tv-offer" aria-label="<?php esc_attr_e( 'Partner offer', 'tickervelocity' ); ?>">
	<p class="tv-offer__label">
		<span><?php esc_html_e( 'Partner offer', 'tickervelocity' ); ?></span>
		<span class="tv-offer__tag"><?php esc_html_e( 'Affiliate link', 'tickervelocity' ); ?></span>
		<?php if ( $tv_disclosure ) : ?>
			<a href="<?php echo esc_url( get_permalink( $tv_disclosure ) ); ?>"><?php esc_html_e( 'How this works', 'tickervelocity' ); ?></a>
		<?php endif; ?>
	</p>
	<div class="tv-offer__main">
		<div>
			<p class="tv-offer__name"><?php echo esc_html( $tv_offer['name'] ); ?></p>
			<?php if ( ! empty( $tv_offer['headline'] ) ) : ?>
				<p class="tv-offer__headline"><?php echo esc_html( $tv_offer['headline'] ); ?></p>
			<?php endif; ?>
			<?php if ( ! empty( $tv_offer['perk'] ) ) : ?>
				<p class="tv-offer__perk"><?php echo esc_html( $tv_offer['perk'] ); ?></p>
			<?php endif; ?>
		</div>
		<a class="tv-btn tv-btn--ghost" href="<?php echo esc_url( home_url( '/go/' . $tv_offer['slug'] . '/' ) ); ?>" target="_blank" rel="sponsored nofollow noopener"><?php echo esc_html( $tv_offer['button'] ? $tv_offer['button'] : $tv_offer['name'] ); ?> &rarr;</a>
	</div>
	<p class="tv-offer__risk"><?php echo esc_html( tv_mod( 'tv_crypto_risk' ) ); ?> <?php esc_html_e( 'We may earn a commission if you sign up. It never affects our coverage.', 'tickervelocity' ); ?></p>
</aside>
