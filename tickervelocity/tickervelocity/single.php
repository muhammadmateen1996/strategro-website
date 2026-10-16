<?php
/**
 * Article.
 *
 * @package TickerVelocity
 */

get_header();

while ( have_posts() ) :
	the_post();
	$tv_top      = tv_top_category();
	$tv_term     = tv_primary_category();
	$tv_parts    = tv_article_parts();
	$tv_reviewer = trim( (string) get_post_meta( get_the_ID(), 'tv_reviewed_by', true ) );
	$tv_updated  = get_the_modified_time( 'U' ) - get_the_time( 'U' ) > 30 * MINUTE_IN_SECONDS;
	$tv_sources  = tv_sources();
	$tv_offers   = tv_offers_for_post( get_the_ID() );
	$tv_format   = get_option( 'timezone_string' ) ? 'j M Y, H:i T' : 'j M Y, H:i';
	?>
	<article <?php post_class( 'tv-article' ); ?> data-cat="<?php echo esc_attr( $tv_top ? $tv_top->slug : '' ); ?>" data-tv-minutes="<?php echo esc_attr( tv_reading_time() ); ?>">
		<header class="tv-wrap tv-article__head">
			<nav class="tv-crumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'tickervelocity' ); ?>">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'tickervelocity' ); ?></a>
				<?php if ( $tv_top ) : ?>
					<span aria-hidden="true">/</span><a href="<?php echo esc_url( get_category_link( $tv_top ) ); ?>"><?php echo esc_html( $tv_top->name ); ?></a>
				<?php endif; ?>
				<?php if ( $tv_term && $tv_top && $tv_term->term_id !== $tv_top->term_id ) : ?>
					<span aria-hidden="true">/</span><a href="<?php echo esc_url( get_category_link( $tv_term ) ); ?>"><?php echo esc_html( $tv_term->name ); ?></a>
				<?php endif; ?>
			</nav>

			<?php if ( tv_is_breaking() ) : ?>
				<p class="tv-article__flags"><span class="tv-badge tv-badge--breaking"><?php esc_html_e( 'Breaking', 'tickervelocity' ); ?></span></p>
			<?php endif; ?>

			<h1 class="tv-article__title"><?php the_title(); ?></h1>
			<?php if ( has_excerpt() ) : ?>
				<p class="tv-article__dek"><?php echo esc_html( get_the_excerpt() ); ?></p>
			<?php endif; ?>

			<div class="tv-article__byline">
				<?php echo get_avatar( get_the_author_meta( 'ID' ), 40, '', '', array( 'class' => 'tv-avatar' ) ); ?>
				<div class="tv-article__who">
					<p>
						<?php esc_html_e( 'By', 'tickervelocity' ); ?> <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><?php the_author(); ?></a>
						<?php if ( $tv_reviewer ) : ?>
							<span class="tv-article__sep" aria-hidden="true">&middot;</span> <?php esc_html_e( 'Reviewed by', 'tickervelocity' ); ?> <?php echo esc_html( $tv_reviewer ); ?>
						<?php endif; ?>
					</p>
					<p class="tv-article__when">
						<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( $tv_format ) ); ?></time>
						<?php if ( $tv_updated ) : ?>
							<span class="tv-article__sep" aria-hidden="true">&middot;</span> <?php esc_html_e( 'Updated', 'tickervelocity' ); ?> <time datetime="<?php echo esc_attr( get_the_modified_date( 'c' ) ); ?>"><?php echo esc_html( get_the_modified_date( $tv_format ) ); ?></time>
						<?php endif; ?>
						<span class="tv-article__sep" aria-hidden="true">&middot;</span>
						<?php
						/* translators: %d: minutes */
						echo esc_html( sprintf( __( '%d min read', 'tickervelocity' ), tv_reading_time() ) );
						?>
					</p>
				</div>
				<?php tv_share_buttons(); ?>
			</div>
		</header>

		<?php if ( has_post_thumbnail() ) : ?>
			<figure class="tv-wrap tv-article__media">
				<?php the_post_thumbnail( 'tv-lead', array( 'sizes' => '(min-width: 1180px) 1132px, 100vw' ) ); ?>
				<?php if ( get_the_post_thumbnail_caption() || get_post_meta( get_the_ID(), 'tv_ai_image', true ) ) : ?>
					<figcaption>
						<?php the_post_thumbnail_caption(); ?>
						<?php if ( get_post_meta( get_the_ID(), 'tv_ai_image', true ) ) : ?>
							<span class="tv-article__credit"><?php esc_html_e( 'AI-generated illustration', 'tickervelocity' ); ?></span>
						<?php endif; ?>
					</figcaption>
				<?php endif; ?>
			</figure>
		<?php endif; ?>

		<div class="tv-wrap tv-article__layout">
			<div class="tv-article__main">
				<?php get_template_part( 'template-parts/impact' ); ?>

				<?php if ( $tv_parts['toc'] ) : ?>
					<details class="tv-toc tv-toc--inline">
						<summary><?php esc_html_e( 'In this article', 'tickervelocity' ); ?></summary>
						<?php tv_toc_list( $tv_parts['toc'] ); ?>
					</details>
				<?php endif; ?>

				<div class="tv-prose">
					<?php echo $tv_parts['html']; // phpcs:ignore WordPress.Security.EscapeOutput -- the_content output. ?>
				</div>

				<?php if ( $tv_offers ) : ?>
					<div class="tv-offer-slot" data-tv-offers="<?php echo esc_attr( get_the_ID() ); ?>"></div>
				<?php endif; ?>

				<?php if ( $tv_sources ) : ?>
					<section class="tv-sources">
						<h2 class="tv-sources__title"><?php esc_html_e( 'Sources', 'tickervelocity' ); ?></h2>
						<ol>
							<?php foreach ( $tv_sources as $tv_source ) : ?>
								<li>
									<?php if ( $tv_source['url'] ) : ?>
										<a href="<?php echo esc_url( $tv_source['url'] ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $tv_source['title'] ); ?></a>
										<span class="tv-sources__host"><?php echo esc_html( preg_replace( '/^www\./', '', (string) wp_parse_url( $tv_source['url'], PHP_URL_HOST ) ) ); ?></span>
									<?php else : ?>
										<?php echo esc_html( $tv_source['title'] ); ?>
									<?php endif; ?>
								</li>
							<?php endforeach; ?>
						</ol>
					</section>
				<?php endif; ?>

				<?php
				$tv_tags = get_the_tags();
				if ( $tv_tags ) :
					?>
					<p class="tv-tags">
						<?php foreach ( $tv_tags as $tv_tag ) : ?>
							<a href="<?php echo esc_url( get_tag_link( $tv_tag ) ); ?>"><?php echo esc_html( $tv_tag->name ); ?></a>
						<?php endforeach; ?>
					</p>
				<?php endif; ?>

				<aside class="tv-disclaimer">
					<p><strong><?php esc_html_e( 'Not financial advice.', 'tickervelocity' ); ?></strong> <?php echo esc_html( tv_mod( 'tv_disclaimer' ) ); ?></p>
					<p><?php echo esc_html( tv_mod( 'tv_ai_note' ) ); ?>
					<?php if ( tv_mod( 'tv_contact_email' ) ) : ?>
						<?php esc_html_e( 'Spotted an error?', 'tickervelocity' ); ?> <a href="mailto:<?php echo esc_attr( tv_mod( 'tv_contact_email' ) ); ?>?subject=<?php echo rawurlencode( 'Correction: ' . wp_strip_all_tags( get_the_title() ) ); ?>"><?php esc_html_e( 'Tell us', 'tickervelocity' ); ?></a>.
					<?php endif; ?>
					</p>
				</aside>

				<section class="tv-author">
					<?php echo get_avatar( get_the_author_meta( 'ID' ), 64, '', '', array( 'class' => 'tv-avatar' ) ); ?>
					<div>
						<p class="tv-author__name"><a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><?php the_author(); ?></a>
						<?php if ( get_the_author_meta( 'tv_role' ) ) : ?>
							<span><?php echo esc_html( get_the_author_meta( 'tv_role' ) ); ?></span>
						<?php endif; ?>
						</p>
						<?php if ( get_the_author_meta( 'description' ) ) : ?>
							<p class="tv-author__bio"><?php echo esc_html( get_the_author_meta( 'description' ) ); ?></p>
						<?php endif; ?>
					</div>
				</section>
			</div>

			<aside class="tv-article__side">
				<div class="tv-sticky">
					<?php if ( $tv_parts['toc'] ) : ?>
						<nav class="tv-toc tv-toc--side" aria-label="<?php esc_attr_e( 'In this article', 'tickervelocity' ); ?>">
							<p class="tv-toc__head"><?php esc_html_e( 'In this article', 'tickervelocity' ); ?></p>
							<?php tv_toc_list( $tv_parts['toc'] ); ?>
						</nav>
					<?php endif; ?>
					<?php get_template_part( 'template-parts/newsletter', null, array( 'variant' => 'card' ) ); ?>
				</div>
			</aside>
		</div>
	</article>

	<?php
	if ( comments_open() || get_comments_number() ) {
		echo '<div class="tv-wrap tv-comments">';
		comments_template();
		echo '</div>';
	}

	$tv_related = new WP_Query(
		array(
			'post_type'           => 'post',
			'posts_per_page'      => 4,
			'post__not_in'        => array( get_the_ID() ),
			'cat'                 => $tv_term ? $tv_term->term_id : 0,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	);
	if ( $tv_related->have_posts() ) :
		?>
		<section class="tv-wrap tv-section tv-related">
			<div class="tv-section__head">
				<h2 class="tv-section__title">
					<?php
					/* translators: %s: category */
					echo esc_html( $tv_term ? sprintf( __( 'More in %s', 'tickervelocity' ), $tv_term->name ) : __( 'More news', 'tickervelocity' ) );
					?>
				</h2>
				<span class="tv-section__rule" aria-hidden="true"></span>
			</div>
			<div class="tv-section__grid tv-section__grid--even tv-stagger">
				<?php
				while ( $tv_related->have_posts() ) :
					$tv_related->the_post();
					get_template_part( 'template-parts/card', null, array( 'variant' => 'standard' ) );
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		</section>
		<?php
	endif;
endwhile;

get_footer();
