<?php
/**
 * Insights (the posts page), category/tag/author/date archives, search
 * results and the fallback for anything else.
 *
 * @package Strategro
 */

get_header();

if ( strategro_elementor_location( 'archive' ) ) {
	get_footer();
	return;
}

$strategro_eyebrow = __( 'Insights', 'strategro' );
$strategro_title   = strategro_blog_headline();
$strategro_lead    = strategro_mod( 'strategro_blog_intro' );
$strategro_active  = 0;

if ( is_category() || is_tag() || is_tax() ) {
	$strategro_term    = get_queried_object();
	$strategro_active  = is_category() ? $strategro_term->term_id : 0;
	$strategro_eyebrow = is_category() ? __( 'Topic', 'strategro' ) : __( 'Tag', 'strategro' );
	$strategro_title   = esc_html( single_term_title( '', false ) );
	$strategro_lead    = wp_strip_all_tags( term_description() );
	if ( '' === trim( $strategro_lead ) ) {
		/* translators: %s: topic name */
		$strategro_lead = sprintf( __( 'Every article on %s, newest first.', 'strategro' ), single_term_title( '', false ) );
	}
} elseif ( is_author() ) {
	$strategro_eyebrow = __( 'Author', 'strategro' );
	$strategro_title   = esc_html( get_the_author_meta( 'display_name', (int) get_query_var( 'author' ) ) );
	$strategro_lead    = get_the_author_meta( 'description', (int) get_query_var( 'author' ) );
} elseif ( is_search() ) {
	$strategro_eyebrow = __( 'Search', 'strategro' );
	/* translators: %s: search terms */
	$strategro_title = sprintf( __( 'Results for &ldquo;%s&rdquo;', 'strategro' ), esc_html( get_search_query( false ) ) );
	/* translators: %d: number of results */
	$strategro_lead = sprintf( _n( '%d article found.', '%d articles found.', (int) $wp_query->found_posts, 'strategro' ), (int) $wp_query->found_posts );
} elseif ( is_archive() ) {
	$strategro_eyebrow = __( 'Archive', 'strategro' );
	$strategro_title   = esc_html( wp_strip_all_tags( get_the_archive_title() ) );
	$strategro_lead    = '';
}

$strategro_topics  = strategro_blog_topics();
$strategro_count   = (int) wp_count_posts()->publish;
$strategro_lead_on = ! is_search() && ! is_paged() && have_posts();
?>
<section class="sg-blog-hero sg-band--dark sg-network sg-spot">
	<div class="sg-wrap">
		<div class="sg-blog-hero__grid">
			<div class="sg-blog-hero__copy">
				<p class="sg-kicker sg-reveal"><?php echo esc_html( $strategro_eyebrow ); ?></p>
				<h1 class="sg-blog-hero__title sg-split"><?php echo $strategro_title; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above. ?></h1>
				<?php if ( $strategro_lead ) : ?>
					<p class="sg-blog-hero__lead sg-reveal sg-d2"><?php echo esc_html( $strategro_lead ); ?></p>
				<?php endif; ?>
			</div>

			<div class="sg-blog-hero__panel sg-reveal sg-d3">
				<div class="sg-blog-hero__stats">
					<p><strong class="sg-counter"><?php echo esc_html( $strategro_count ); ?></strong><span><?php echo esc_html( _n( 'article', 'articles', $strategro_count, 'strategro' ) ); ?></span></p>
					<p><strong class="sg-counter"><?php echo esc_html( count( $strategro_topics ) ); ?></strong><span><?php echo esc_html( _n( 'topic', 'topics', count( $strategro_topics ), 'strategro' ) ); ?></span></p>
				</div>
				<?php get_search_form( array( 'sg_posts_only' => true ) ); ?>
			</div>
		</div>

		<?php if ( $strategro_topics ) : ?>
			<nav class="sg-topics sg-reveal sg-d4" aria-label="<?php esc_attr_e( 'Topics', 'strategro' ); ?>">
				<a class="sg-topic<?php echo is_home() ? ' is-active' : ''; ?>" href="<?php echo esc_url( strategro_blog_url() ); ?>"<?php echo is_home() ? ' aria-current="page"' : ''; ?>><?php esc_html_e( 'All', 'strategro' ); ?></a>
				<?php foreach ( $strategro_topics as $strategro_topic ) : ?>
					<a class="sg-topic<?php echo $strategro_active === $strategro_topic->term_id ? ' is-active' : ''; ?>" href="<?php echo esc_url( get_category_link( $strategro_topic ) ); ?>"<?php echo $strategro_active === $strategro_topic->term_id ? ' aria-current="page"' : ''; ?>>
						<?php echo esc_html( $strategro_topic->name ); ?><span><?php echo esc_html( $strategro_topic->count ); ?></span>
					</a>
				<?php endforeach; ?>
			</nav>
		<?php endif; ?>
	</div>
</section>

<section class="sg-blog">
	<div class="sg-wrap">
		<?php if ( have_posts() ) : ?>
			<?php
			if ( $strategro_lead_on ) {
				the_post();
				get_template_part( 'template-parts/card', 'lead', array( 'badge' => is_home() ? __( 'Latest', 'strategro' ) : '' ) );
			}
			?>

			<?php if ( have_posts() ) : ?>
				<?php if ( $strategro_lead_on ) : ?>
					<div class="sg-blog__head sg-reveal">
						<h2 class="sg-blog__heading"><?php esc_html_e( 'More articles', 'strategro' ); ?></h2>
						<span class="sg-blog__rule" aria-hidden="true"></span>
					</div>
				<?php endif; ?>
				<div class="sg-post-grid sg-stagger">
					<?php
					while ( have_posts() ) :
						the_post();
						get_template_part( 'template-parts/card', 'post' );
					endwhile;
					?>
				</div>
			<?php endif; ?>

			<?php strategro_pagination(); ?>
		<?php else : ?>
			<div class="sg-blog-empty sg-reveal">
				<span class="sg-blog-empty__mark" aria-hidden="true"><?php strategro_mark(); ?></span>
				<h2><?php echo is_search() ? esc_html__( 'No articles match that search.', 'strategro' ) : esc_html__( 'New articles are on the way.', 'strategro' ); ?></h2>
				<p><?php esc_html_e( 'Try another search, or browse every article.', 'strategro' ); ?></p>
				<?php get_search_form( array( 'sg_posts_only' => true ) ); ?>
				<p><a class="sg-text-link" href="<?php echo esc_url( strategro_blog_url() ); ?>"><?php esc_html_e( 'See all insights', 'strategro' ); ?> &rarr;</a></p>
			</div>
		<?php endif; ?>
	</div>
</section>

<?php
get_template_part( 'template-parts/blog', 'cta' );
get_footer();
