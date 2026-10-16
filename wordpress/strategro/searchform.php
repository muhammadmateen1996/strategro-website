<?php
/**
 * Search form. Pass array( 'sg_posts_only' => true ) to get_search_form()
 * to search articles only.
 *
 * @package Strategro
 */

$strategro_field = wp_unique_id( 'sg-search-' );
?>
<form role="search" method="get" class="sg-search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="<?php echo esc_attr( $strategro_field ); ?>"><?php esc_html_e( 'Search articles', 'strategro' ); ?></label>
	<svg class="sg-search__icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
	<input class="sg-search__input" id="<?php echo esc_attr( $strategro_field ); ?>" type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Search articles', 'strategro' ); ?>">
	<?php if ( ! empty( $args['sg_posts_only'] ) ) : ?>
		<input type="hidden" name="post_type" value="post">
	<?php endif; ?>
	<button class="sg-search__btn" type="submit"><?php esc_html_e( 'Search', 'strategro' ); ?></button>
</form>
