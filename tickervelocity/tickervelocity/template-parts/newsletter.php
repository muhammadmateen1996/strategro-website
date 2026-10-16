<?php
/**
 * Newsletter form. $args['variant']: band (big, dark), inline (inside an
 * article), card (sidebar), page (Newsletter page) or footer.
 *
 * @package TickerVelocity
 */

$tv_variant = isset( $args['variant'] ) ? $args['variant'] : 'band';
$tv_name    = tv_mod( 'tv_newsletter_name' );
$tv_done    = isset( $_GET['subscribed'] ) ? sanitize_key( wp_unslash( $_GET['subscribed'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
$tv_privacy = get_privacy_policy_url();
?>
<section class="tv-news tv-news--<?php echo esc_attr( $tv_variant ); ?>"<?php echo 'band' === $tv_variant || 'page' === $tv_variant ? ' id="newsletter"' : ''; ?>>
	<?php if ( 'footer' !== $tv_variant ) : ?>
		<div class="tv-news__copy">
			<p class="tv-eyebrow"><?php esc_html_e( 'Free newsletter', 'tickervelocity' ); ?></p>
			<h2 class="tv-news__title"><?php echo esc_html( $tv_name ); ?></h2>
			<p class="tv-news__pitch"><?php echo esc_html( tv_mod( 'tv_newsletter_pitch' ) ); ?></p>
			<?php if ( 'band' === $tv_variant || 'page' === $tv_variant ) : ?>
				<ul class="tv-news__points">
					<li><?php esc_html_e( 'Five stories that moved markets, in five minutes', 'tickervelocity' ); ?></li>
					<li><?php esc_html_e( 'What to watch today: data, earnings, central banks', 'tickervelocity' ); ?></li>
					<li><?php esc_html_e( 'No spam, no hype. Unsubscribe in one click', 'tickervelocity' ); ?></li>
				</ul>
			<?php endif; ?>
		</div>
	<?php endif; ?>
	<form class="tv-news__form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" data-tv-newsletter>
		<input type="hidden" name="action" value="tv_subscribe">
		<input type="hidden" name="source" value="<?php echo esc_attr( $tv_variant ); ?>">
		<label class="tv-news__trap" aria-hidden="true">Company <input type="text" name="company" tabindex="-1" autocomplete="off"></label>
		<label class="screen-reader-text" for="tv-email-<?php echo esc_attr( $tv_variant ); ?>"><?php esc_html_e( 'Email address', 'tickervelocity' ); ?></label>
		<div class="tv-news__row">
			<input id="tv-email-<?php echo esc_attr( $tv_variant ); ?>" type="email" name="email" required autocomplete="email" placeholder="<?php esc_attr_e( 'you@company.com', 'tickervelocity' ); ?>">
			<button class="tv-btn" type="submit"><?php echo 'footer' === $tv_variant ? esc_html__( 'Subscribe', 'tickervelocity' ) : esc_html__( 'Get it free', 'tickervelocity' ); ?></button>
		</div>
		<p class="tv-news__legal">
			<?php esc_html_e( 'By subscribing you agree to receive our emails.', 'tickervelocity' ); ?>
			<?php if ( $tv_privacy ) : ?>
				<a href="<?php echo esc_url( $tv_privacy ); ?>"><?php esc_html_e( 'Privacy policy', 'tickervelocity' ); ?></a>
			<?php endif; ?>
		</p>
		<p class="tv-news__status" role="status" aria-live="polite">
			<?php
			if ( '1' === $tv_done ) {
				esc_html_e( 'You\'re in. Check your inbox to confirm.', 'tickervelocity' );
			} elseif ( '0' === $tv_done ) {
				esc_html_e( 'That didn\'t work. Please check the address and try again.', 'tickervelocity' );
			}
			?>
		</p>
	</form>
</section>
