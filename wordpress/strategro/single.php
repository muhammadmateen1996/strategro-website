<?php
/**
 * Single article.
 *
 * @package Strategro
 */

get_header();

if ( strategro_elementor_location( 'single' ) ) {
	get_footer();
	return;
}

while ( have_posts() ) :
	the_post();
	$strategro_term    = strategro_primary_category();
	// Posts built with Elementor show in the article design too, unless that's
	// switched off (Customize > Strategro Blog) or the post is open in Elementor.
	$strategro_builder = strategro_is_elementor_page( get_the_ID() ) && ( ! strategro_mod( 'strategro_blog_unify' ) || isset( $_GET['elementor-preview'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
	$strategro_parts   = $strategro_builder ? array( 'html' => '', 'toc' => array() ) : strategro_article_parts();
	$strategro_toc     = $strategro_parts['toc'];
	$strategro_minutes = strategro_reading_time();
	$strategro_cover   = has_post_thumbnail();
	$strategro_updated = get_the_modified_time( 'U' ) - get_the_time( 'U' ) > DAY_IN_SECONDS;
	?>
	<article <?php post_class( 'sg-post' . ( $strategro_builder ? ' sg-post--builder' : '' ) ); ?> data-sg-minutes="<?php echo esc_attr( $strategro_minutes ); ?>">
		<header class="sg-post-hero sg-band--dark sg-network<?php echo $strategro_cover ? ' sg-post-hero--cover' : ''; ?>">
			<div class="sg-wrap sg-post-hero__inner">
				<nav class="sg-crumbs sg-reveal" aria-label="<?php esc_attr_e( 'Breadcrumb', 'strategro' ); ?>">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'strategro' ); ?></a>
					<span aria-hidden="true">/</span>
					<a href="<?php echo esc_url( strategro_blog_url() ); ?>"><?php esc_html_e( 'Insights', 'strategro' ); ?></a>
					<?php if ( $strategro_term ) : ?>
						<span aria-hidden="true">/</span>
						<a href="<?php echo esc_url( get_category_link( $strategro_term ) ); ?>"><?php echo esc_html( $strategro_term->name ); ?></a>
					<?php endif; ?>
				</nav>

				<?php if ( $strategro_term ) : ?>
					<p class="sg-reveal"><a class="sg-kicker sg-kicker--link" href="<?php echo esc_url( get_category_link( $strategro_term ) ); ?>"><?php echo esc_html( $strategro_term->name ); ?></a></p>
				<?php endif; ?>

				<h1 class="sg-post-hero__title sg-split"><?php the_title(); ?></h1>

				<?php if ( has_excerpt() ) : ?>
					<p class="sg-post-hero__dek sg-reveal sg-d2"><?php echo esc_html( get_the_excerpt() ); ?></p>
				<?php endif; ?>

				<div class="sg-post-hero__meta sg-reveal sg-d3">
					<span class="sg-byline">
						<?php echo get_avatar( get_the_author_meta( 'ID' ), 44, '', '', array( 'class' => 'sg-byline__avatar' ) ); ?>
						<span>
							<span class="sg-byline__name"><?php the_author(); ?></span>
							<span class="sg-byline__date">
								<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'j F Y' ) ); ?></time>
								<?php if ( $strategro_updated ) : ?>
									&middot; <?php esc_html_e( 'Updated', 'strategro' ); ?> <time datetime="<?php echo esc_attr( get_the_modified_date( 'c' ) ); ?>"><?php echo esc_html( get_the_modified_date( 'j F Y' ) ); ?></time>
								<?php endif; ?>
							</span>
						</span>
					</span>
					<span class="sg-post-hero__time">
						<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
						<?php echo esc_html( strategro_reading_label() ); ?>
					</span>
				</div>
			</div>
		</header>

		<?php if ( $strategro_cover ) : ?>
			<figure class="sg-post-cover sg-wrap sg-reveal">
				<?php the_post_thumbnail( 'full', array( 'loading' => 'eager', 'fetchpriority' => 'high', 'sizes' => '(min-width: 1280px) 1232px, 100vw' ) ); ?>
				<?php if ( get_the_post_thumbnail_caption() ) : ?>
					<figcaption><?php the_post_thumbnail_caption(); ?></figcaption>
				<?php endif; ?>
			</figure>
		<?php endif; ?>

		<?php if ( $strategro_builder ) : ?>
			<div class="sg-post-builder"><?php the_content(); ?></div>
			<div class="sg-wrap sg-post-builder__foot">
				<?php strategro_share_buttons( 'sg-share--row' ); ?>
			</div>
		<?php else : ?>
			<div class="sg-wrap sg-post-layout">
				<aside class="sg-post-rail" aria-label="<?php esc_attr_e( 'Share', 'strategro' ); ?>">
					<p class="sg-post-rail__label"><?php esc_html_e( 'Share', 'strategro' ); ?></p>
					<?php strategro_share_buttons( 'sg-share--rail' ); ?>
				</aside>

				<div class="sg-post-main">
					<?php if ( $strategro_toc ) : ?>
						<details class="sg-toc sg-toc--inline">
							<summary><?php esc_html_e( 'On this page', 'strategro' ); ?> <span><?php echo esc_html( count( $strategro_toc ) ); ?></span></summary>
							<?php strategro_toc_list( $strategro_toc ); ?>
						</details>
					<?php endif; ?>

					<div class="sg-prose sg-prose--article">
						<?php echo $strategro_parts['html']; // phpcs:ignore WordPress.Security.EscapeOutput -- the_content output. ?>
					</div>

					<?php
					wp_link_pages(
						array(
							'before' => '<nav class="sg-page-links">' . esc_html__( 'Pages:', 'strategro' ),
							'after'  => '</nav>',
						)
					);
					?>

					<footer class="sg-post-foot">
						<?php
						$strategro_tags = get_the_tags();
						if ( $strategro_tags ) :
							?>
							<p class="sg-post-tags">
								<?php foreach ( $strategro_tags as $strategro_tag ) : ?>
									<a href="<?php echo esc_url( get_tag_link( $strategro_tag ) ); ?>">#<?php echo esc_html( $strategro_tag->name ); ?></a>
								<?php endforeach; ?>
							</p>
						<?php endif; ?>

						<div class="sg-post-foot__share">
							<p><?php esc_html_e( 'Found this useful? Pass it on.', 'strategro' ); ?></p>
							<?php strategro_share_buttons( 'sg-share--row' ); ?>
						</div>

						<div class="sg-author">
							<?php echo get_avatar( get_the_author_meta( 'ID' ), 72, '', '', array( 'class' => 'sg-author__avatar' ) ); ?>
							<div>
								<p class="sg-author__label"><?php esc_html_e( 'Written by', 'strategro' ); ?></p>
								<p class="sg-author__name"><?php the_author(); ?></p>
								<?php if ( get_the_author_meta( 'description' ) ) : ?>
									<p class="sg-author__bio"><?php echo esc_html( get_the_author_meta( 'description' ) ); ?></p>
								<?php endif; ?>
								<a class="sg-text-link" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><?php esc_html_e( 'More from this author', 'strategro' ); ?> &rarr;</a>
							</div>
						</div>
					</footer>
				</div>

				<aside class="sg-post-aside">
					<div class="sg-post-aside__sticky">
						<?php if ( $strategro_toc ) : ?>
							<nav class="sg-toc sg-toc--side" aria-label="<?php esc_attr_e( 'On this page', 'strategro' ); ?>">
								<p class="sg-toc__head">
									<span><?php esc_html_e( 'On this page', 'strategro' ); ?></span>
									<span class="sg-toc__left" data-sg-left data-unit="<?php esc_attr_e( 'min left', 'strategro' ); ?>" data-done="<?php esc_attr_e( 'Finished', 'strategro' ); ?>"><?php echo esc_html( strategro_reading_label() ); ?></span>
								</p>
								<span class="sg-toc__bar" aria-hidden="true"><span></span></span>
								<?php strategro_toc_list( $strategro_toc ); ?>
							</nav>
						<?php endif; ?>
						<div class="sg-aside-cta">
							<p class="sg-aside-cta__title"><?php esc_html_e( 'Want this working in your business?', 'strategro' ); ?></p>
							<p class="sg-aside-cta__text"><?php esc_html_e( 'Book a free AI Systems Audit and see what to automate first.', 'strategro' ); ?></p>
							<?php
							$strategro_cta_url = strategro_mod( 'strategro_cta_url' );
							$strategro_cta_url = 0 === strpos( $strategro_cta_url, '/' ) ? home_url( $strategro_cta_url ) : $strategro_cta_url;
							?>
							<a class="sg-button sg-button--sm sg-arrow" href="<?php echo esc_url( $strategro_cta_url ); ?>"><span><?php esc_html_e( 'Book a call', 'strategro' ); ?></span></a>
						</div>
					</div>
				</aside>
			</div>
		<?php endif; ?>

		<?php
		$strategro_prev = get_previous_post();
		$strategro_next = get_next_post();
		if ( $strategro_prev || $strategro_next ) :
			?>
			<nav class="sg-wrap sg-post-nav" aria-label="<?php esc_attr_e( 'More articles', 'strategro' ); ?>">
				<?php if ( $strategro_prev ) : ?>
					<a class="sg-post-nav__item sg-post-nav__item--prev sg-reveal" href="<?php echo esc_url( get_permalink( $strategro_prev ) ); ?>">
						<span class="sg-post-nav__label">&larr; <?php esc_html_e( 'Previous article', 'strategro' ); ?></span>
						<span class="sg-post-nav__title"><?php echo esc_html( get_the_title( $strategro_prev ) ); ?></span>
					</a>
				<?php endif; ?>
				<?php if ( $strategro_next ) : ?>
					<a class="sg-post-nav__item sg-post-nav__item--next sg-reveal" href="<?php echo esc_url( get_permalink( $strategro_next ) ); ?>">
						<span class="sg-post-nav__label"><?php esc_html_e( 'Next article', 'strategro' ); ?> &rarr;</span>
						<span class="sg-post-nav__title"><?php echo esc_html( get_the_title( $strategro_next ) ); ?></span>
					</a>
				<?php endif; ?>
			</nav>
		<?php endif; ?>
	</article>

	<?php
	if ( strategro_mod( 'strategro_blog_comments' ) && ( comments_open() || get_comments_number() ) ) {
		comments_template();
	}

	$strategro_related = new WP_Query(
		array(
			'post_type'           => 'post',
			'posts_per_page'      => 3,
			'post__not_in'        => array( get_the_ID() ),
			'ignore_sticky_posts' => true,
			'cat'                 => $strategro_term ? $strategro_term->term_id : 0,
		)
	);
	if ( $strategro_related->post_count < 3 && $strategro_term ) {
		$strategro_related = new WP_Query(
			array(
				'post_type'           => 'post',
				'posts_per_page'      => 3,
				'post__not_in'        => array( get_the_ID() ),
				'ignore_sticky_posts' => true,
			)
		);
	}
	if ( $strategro_related->have_posts() ) :
		?>
		<section class="sg-related">
			<div class="sg-wrap">
				<div class="sg-blog__head sg-reveal">
					<h2 class="sg-blog__heading"><?php esc_html_e( 'Keep reading', 'strategro' ); ?></h2>
					<span class="sg-blog__rule" aria-hidden="true"></span>
					<a class="sg-text-link" href="<?php echo esc_url( strategro_blog_url() ); ?>"><?php esc_html_e( 'All insights', 'strategro' ); ?> &rarr;</a>
				</div>
				<div class="sg-post-grid sg-stagger">
					<?php
					while ( $strategro_related->have_posts() ) :
						$strategro_related->the_post();
						get_template_part( 'template-parts/card', 'post' );
					endwhile;
					wp_reset_postdata();
					?>
				</div>
			</div>
		</section>
		<?php
	endif;
endwhile;

get_template_part( 'template-parts/blog', 'cta' );
get_footer();
