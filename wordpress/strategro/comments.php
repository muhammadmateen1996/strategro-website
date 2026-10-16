<?php
/**
 * Comments under an article (shown when switched on in Customize >
 * Strategro Blog and the post allows comments).
 *
 * @package Strategro
 */

if ( post_password_required() ) {
	return;
}
?>
<section id="comments" class="sg-comments">
	<div class="sg-wrap sg-comments__inner">
		<?php if ( have_comments() ) : ?>
			<h2 class="sg-comments__title">
				<?php
				/* translators: %s: number of comments */
				echo esc_html( sprintf( _n( '%s comment', '%s comments', get_comments_number(), 'strategro' ), number_format_i18n( get_comments_number() ) ) );
				?>
			</h2>
			<ol class="sg-comments__list">
				<?php
				wp_list_comments(
					array(
						'style'       => 'ol',
						'short_ping'  => true,
						'avatar_size' => 44,
					)
				);
				?>
			</ol>
			<?php the_comments_navigation(); ?>
		<?php endif; ?>

		<?php
		comment_form(
			array(
				'class_form'         => 'sg-comment-form',
				'title_reply'        => __( 'Join the conversation', 'strategro' ),
				'title_reply_before' => '<h2 class="sg-comments__title">',
				'title_reply_after'  => '</h2>',
				'class_submit'       => 'sg-button',
			)
		);
		?>
	</div>
</section>
