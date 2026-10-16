<?php
/**
 * Comments (off by default; switch on in Customize > TickerVelocity > Homepage).
 *
 * @package TickerVelocity
 */

if ( post_password_required() ) {
	return;
}
?>
<section id="comments" class="tv-comments-list">
	<?php if ( have_comments() ) : ?>
		<h2 class="tv-section__title"><?php echo esc_html( sprintf( /* translators: %s: count */ _n( '%s comment', '%s comments', get_comments_number(), 'tickervelocity' ), number_format_i18n( get_comments_number() ) ) ); ?></h2>
		<ol><?php wp_list_comments( array( 'avatar_size' => 40, 'style' => 'ol' ) ); ?></ol>
		<?php the_comments_navigation(); ?>
	<?php endif; ?>
	<?php comment_form( array( 'class_submit' => 'tv-btn' ) ); ?>
</section>
