<?php
/**
 * Lists of articles: Latest, categories, tags, authors, search and dates.
 *
 * @package TickerVelocity
 */

get_header();

$tv_obj     = get_queried_object();
$tv_eyebrow = '';
$tv_title   = __( 'Latest news', 'tickervelocity' );
$tv_intro   = __( 'Every story as it is published, newest first.', 'tickervelocity' );
$tv_pills   = array();
$tv_author  = 0;

if ( is_category() ) {
	$tv_anc     = get_ancestors( $tv_obj->term_id, 'category' );
	$tv_top     = $tv_anc ? get_term( (int) end( $tv_anc ), 'category' ) : $tv_obj;
	$tv_eyebrow = $tv_obj->parent ? $tv_top->name : __( 'Section', 'tickervelocity' );
	$tv_title   = single_cat_title( '', false );
	$tv_intro   = wp_strip_all_tags( category_description() );
	$tv_pills   = get_categories(
		array(
			'parent'     => $tv_top->term_id,
			'hide_empty' => true,
		)
	);
	if ( $tv_pills ) {
		array_unshift( $tv_pills, $tv_top );
	}
} elseif ( is_tag() ) {
	$tv_eyebrow = __( 'Topic', 'tickervelocity' );
	$tv_title   = single_tag_title( '', false );
	$tv_intro   = wp_strip_all_tags( tag_description() );
} elseif ( is_author() ) {
	$tv_author  = (int) get_query_var( 'author' );
	$tv_eyebrow = get_the_author_meta( 'tv_role', $tv_author ) ? get_the_author_meta( 'tv_role', $tv_author ) : __( 'Author', 'tickervelocity' );
	$tv_title   = get_the_author_meta( 'display_name', $tv_author );
	$tv_intro   = get_the_author_meta( 'description', $tv_author );
} elseif ( is_search() ) {
	$tv_eyebrow = __( 'Search', 'tickervelocity' );
	/* translators: %s: search terms */
	$tv_title = sprintf( __( 'Results for “%s”', 'tickervelocity' ), get_search_query( false ) );
	/* translators: %d: number of results */
	$tv_intro = sprintf( _n( '%d article', '%d articles', (int) $wp_query->found_posts, 'tickervelocity' ), (int) $wp_query->found_posts );
} elseif ( is_archive() ) {
	$tv_eyebrow = __( 'Archive', 'tickervelocity' );
	$tv_title   = wp_strip_all_tags( get_the_archive_title() );
	$tv_intro   = '';
}
?>
<header class="tv-archive-head<?php echo $tv_author ? ' tv-archive-head--author' : ''; ?>"<?php echo is_category() ? ' data-cat="' . esc_attr( $tv_top->slug ) . '"' : ''; ?>>
	<div class="tv-wrap">
		<?php if ( $tv_author ) : ?>
			<?php echo get_avatar( $tv_author, 96, '', '', array( 'class' => 'tv-archive-head__avatar' ) ); ?>
		<?php endif; ?>
		<div>
			<?php if ( $tv_eyebrow ) : ?>
				<p class="tv-eyebrow"><?php echo esc_html( $tv_eyebrow ); ?></p>
			<?php endif; ?>
			<h1 class="tv-archive-head__title"><?php echo esc_html( $tv_title ); ?></h1>
			<?php if ( $tv_intro ) : ?>
				<p class="tv-archive-head__intro"><?php echo esc_html( $tv_intro ); ?></p>
			<?php endif; ?>
			<?php if ( $tv_author ) : ?>
				<p class="tv-archive-head__links">
					<?php if ( get_the_author_meta( 'tv_expertise', $tv_author ) ) : ?>
						<span><?php esc_html_e( 'Covers:', 'tickervelocity' ); ?> <?php echo esc_html( get_the_author_meta( 'tv_expertise', $tv_author ) ); ?></span>
					<?php endif; ?>
					<?php foreach ( array( 'tv_x' => 'X', 'tv_linkedin' => 'LinkedIn' ) as $tv_key => $tv_label ) : ?>
						<?php if ( get_the_author_meta( $tv_key, $tv_author ) ) : ?>
							<a href="<?php echo esc_url( get_the_author_meta( $tv_key, $tv_author ) ); ?>" rel="me noopener" target="_blank"><?php echo esc_html( $tv_label ); ?></a>
						<?php endif; ?>
					<?php endforeach; ?>
				</p>
			<?php endif; ?>
			<?php if ( is_search() ) : ?>
				<div class="tv-archive-head__search"><?php get_search_form(); ?></div>
			<?php endif; ?>
		</div>
	</div>
	<?php if ( $tv_pills ) : ?>
		<nav class="tv-wrap tv-pills" aria-label="<?php esc_attr_e( 'Sub-sections', 'tickervelocity' ); ?>">
			<?php foreach ( $tv_pills as $tv_i => $tv_pill ) : ?>
				<a class="tv-pill<?php echo $tv_pill->term_id === $tv_obj->term_id ? ' is-active' : ''; ?>" href="<?php echo esc_url( get_category_link( $tv_pill ) ); ?>"><?php echo 0 === $tv_i ? esc_html__( 'All', 'tickervelocity' ) : esc_html( $tv_pill->name ); ?></a>
			<?php endforeach; ?>
		</nav>
	<?php endif; ?>
</header>

<div class="tv-wrap tv-archive">
	<div class="tv-archive__main">
		<?php if ( have_posts() ) : ?>
			<?php
			if ( ! is_paged() && ! is_search() ) {
				the_post();
				get_template_part( 'template-parts/card', null, array( 'variant' => 'feature' ) );
			}
			?>
			<div class="tv-river">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/card', null, array( 'variant' => 'row' ) );
				endwhile;
				?>
			</div>
			<?php tv_pagination(); ?>
		<?php else : ?>
			<div class="tv-empty tv-empty--inline">
				<h2><?php esc_html_e( 'Nothing here yet.', 'tickervelocity' ); ?></h2>
				<p><?php esc_html_e( 'Try another search, or see the latest news.', 'tickervelocity' ); ?></p>
				<?php get_search_form(); ?>
			</div>
		<?php endif; ?>
	</div>
	<aside class="tv-archive__side">
		<div class="tv-sticky">
			<?php get_template_part( 'template-parts/newsletter', null, array( 'variant' => 'card' ) ); ?>
			<?php get_template_part( 'template-parts/markets-table' ); ?>
		</div>
	</aside>
</div>
<?php
get_footer();
