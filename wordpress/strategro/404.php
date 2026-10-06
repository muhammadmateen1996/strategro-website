<?php
/**
 * Not found.
 *
 * @package Strategro
 */

get_header();
?>
<section class="sg-band sg-band--dark sg-band--full sg-network">
	<div class="sg-wrap">
		<p class="sg-eyebrow">404</p>
		<h1 class="sg-band__title sg-split"><?php esc_html_e( 'This signal got lost.', 'strategro' ); ?></h1>
		<p class="sg-band__lead"><?php esc_html_e( 'The page you were looking for has moved or no longer exists.', 'strategro' ); ?></p>
		<p><a class="sg-button sg-magnetic" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to the homepage', 'strategro' ); ?></a></p>
	</div>
</section>
<?php
get_footer();
