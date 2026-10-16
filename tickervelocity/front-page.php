<?php
/**
 * Homepage. Fills itself from the newest articles; nothing to edit by hand.
 *
 * @package TickerVelocity
 */

get_header();

$tv_shown = array();
$tv_take  = function ( $args ) use ( &$tv_shown ) {
	$query = new WP_Query(
		array_merge(
			array(
				'post_type'           => 'post',
				'post_status'         => 'publish',
				'ignore_sticky_posts' => true,
				'post__not_in'        => $tv_shown,
				'no_found_rows'       => true,
			),
			$args
		)
	);
	$tv_shown = array_merge( $tv_shown, wp_list_pluck( $query->posts, 'ID' ) );
	return $query;
};

$tv_brief_cat = get_category_by_slug( tv_mod( 'tv_brief_category' ) );
$tv_exclude   = $tv_brief_cat ? array( $tv_brief_cat->term_id ) : array();

// Lead: the newest "breaking" story from the last 12 hours, else the newest story.
$tv_lead = $tv_take(
	array(
		'posts_per_page'   => 1,
		'category__not_in' => $tv_exclude,
		'meta_query'       => array( // phpcs:ignore WordPress.DB.SlowDBQuery
			array(
				'key'   => 'tv_breaking',
				'value' => '1',
			),
		),
		'date_query'       => array( array( 'after' => '12 hours ago' ) ),
	)
);
if ( ! $tv_lead->have_posts() ) {
	$tv_lead = $tv_take(
		array(
			'posts_per_page'   => 1,
			'category__not_in' => $tv_exclude,
		)
	);
}
$tv_top = $tv_take(
	array(
		'posts_per_page'   => 4,
		'category__not_in' => $tv_exclude,
	)
);
?>

<?php if ( ! $tv_lead->have_posts() ) : ?>
	<section class="tv-wrap tv-empty">
		<?php tv_mark(); ?>
		<h1><?php esc_html_e( 'The first stories are on their way.', 'tickervelocity' ); ?></h1>
		<p><?php esc_html_e( 'Subscribe and get the Morning Brief from day one.', 'tickervelocity' ); ?></p>
	</section>
	<div class="tv-wrap"><?php get_template_part( 'template-parts/newsletter', null, array( 'variant' => 'band' ) ); ?></div>
	<?php
	get_footer();
	return;
endif;
?>

<section class="tv-wrap tv-top">
	<h1 class="screen-reader-text"><?php bloginfo( 'name' ); ?>: <?php echo esc_html( tv_mod( 'tv_tagline' ) ); ?></h1>
	<div class="tv-top__lead tv-reveal">
		<?php
		while ( $tv_lead->have_posts() ) :
			$tv_lead->the_post();
			get_template_part( 'template-parts/card', null, array( 'variant' => 'lead' ) );
		endwhile;
		?>
	</div>
	<div class="tv-top__list">
		<p class="tv-label"><span class="tv-dot tv-dot--live"></span><?php esc_html_e( 'Top stories', 'tickervelocity' ); ?></p>
		<?php
		while ( $tv_top->have_posts() ) :
			$tv_top->the_post();
			get_template_part( 'template-parts/card', null, array( 'variant' => 'compact' ) );
		endwhile;
		?>
		<a class="tv-more" href="<?php echo esc_url( tv_latest_url() ); ?>"><?php esc_html_e( 'All the latest', 'tickervelocity' ); ?> <span aria-hidden="true">&rarr;</span></a>
	</div>
</section>
<?php wp_reset_postdata(); ?>

<div class="tv-wrap tv-home-board">
	<?php
	get_template_part(
		'template-parts/price-board',
		null,
		array(
			'limit'   => 8,
			'columns' => 4,
			'title'   => __( 'Markets now', 'tickervelocity' ),
		)
	);
	?>
	<?php if ( tv_prices() ) : ?>
		<a class="tv-more" href="<?php echo esc_url( tv_page_url( 'markets' ) ); ?>"><?php esc_html_e( 'All live prices', 'tickervelocity' ); ?> <span aria-hidden="true">&rarr;</span></a>
	<?php endif; ?>
</div>

<?php
if ( $tv_brief_cat ) :
	$tv_brief = $tv_take(
		array(
			'posts_per_page' => 1,
			'cat'            => $tv_brief_cat->term_id,
		)
	);
	if ( $tv_brief->have_posts() ) :
		$tv_brief->the_post();
		$tv_points = array_slice( tv_post_takeaways(), 0, 5 );
		?>
		<section class="tv-wrap">
			<div class="tv-today tv-reveal">
				<div class="tv-today__main">
					<p class="tv-label"><?php echo esc_html( $tv_brief_cat->name ); ?> &middot; <?php echo tv_time_ago(); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
					<h2 class="tv-today__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<?php if ( $tv_points ) : ?>
						<ol class="tv-today__points">
							<?php foreach ( $tv_points as $tv_point ) : ?>
								<li><?php echo esc_html( $tv_point ); ?></li>
							<?php endforeach; ?>
						</ol>
					<?php else : ?>
						<p class="tv-today__excerpt"><?php echo esc_html( wp_trim_words( wp_strip_all_tags( get_the_excerpt() ), 40 ) ); ?></p>
					<?php endif; ?>
					<a class="tv-btn tv-btn--ghost-light" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read today\'s brief', 'tickervelocity' ); ?> &rarr;</a>
				</div>
				<div class="tv-today__side">
					<?php get_template_part( 'template-parts/newsletter', null, array( 'variant' => 'card' ) ); ?>
				</div>
			</div>
		</section>
		<?php
		wp_reset_postdata();
	endif;
endif;
?>

<?php
foreach ( tv_home_categories() as $tv_cat ) :
	$tv_row = $tv_take(
		array(
			'posts_per_page' => 4,
			'cat'            => $tv_cat->term_id,
		)
	);
	if ( ! $tv_row->have_posts() ) {
		continue;
	}
	?>
	<section class="tv-wrap tv-section" data-cat="<?php echo esc_attr( $tv_cat->slug ); ?>">
		<div class="tv-section__head">
			<h2 class="tv-section__title"><a href="<?php echo esc_url( get_category_link( $tv_cat ) ); ?>"><?php echo esc_html( $tv_cat->name ); ?></a></h2>
			<span class="tv-section__rule" aria-hidden="true"></span>
			<a class="tv-more" href="<?php echo esc_url( get_category_link( $tv_cat ) ); ?>">
				<?php
				/* translators: %s: category name */
				echo esc_html( sprintf( __( 'More %s', 'tickervelocity' ), $tv_cat->name ) );
				?>
				<span aria-hidden="true">&rarr;</span>
			</a>
		</div>
		<div class="tv-section__grid tv-section__grid--n<?php echo esc_attr( $tv_row->post_count ); ?> tv-stagger">
			<?php
			$tv_i = 0;
			while ( $tv_row->have_posts() ) :
				$tv_row->the_post();
				get_template_part( 'template-parts/card', null, array( 'variant' => 0 === $tv_i++ ? 'feature' : 'standard' ) );
			endwhile;
			?>
		</div>
	</section>
	<?php
	wp_reset_postdata();
endforeach;
?>

<div class="tv-wrap"><?php get_template_part( 'template-parts/newsletter', null, array( 'variant' => 'band' ) ); ?></div>

<?php
$tv_latest = $tv_take( array( 'posts_per_page' => 8 ) );
if ( $tv_latest->have_posts() ) :
	?>
	<section class="tv-wrap tv-latest">
		<div class="tv-latest__main">
			<div class="tv-section__head">
				<h2 class="tv-section__title"><?php esc_html_e( 'Latest', 'tickervelocity' ); ?></h2>
				<span class="tv-section__rule" aria-hidden="true"></span>
			</div>
			<div class="tv-river">
				<?php
				while ( $tv_latest->have_posts() ) :
					$tv_latest->the_post();
					get_template_part( 'template-parts/card', null, array( 'variant' => 'row' ) );
				endwhile;
				wp_reset_postdata();
				?>
			</div>
			<a class="tv-btn tv-btn--outline" href="<?php echo esc_url( tv_latest_url() ); ?>"><?php esc_html_e( 'More news', 'tickervelocity' ); ?> &rarr;</a>
		</div>
		<aside class="tv-latest__side">
			<?php get_template_part( 'template-parts/markets-table' ); ?>
		</aside>
	</section>
<?php endif; ?>

<section class="tv-trust">
	<div class="tv-wrap">
		<div class="tv-trust__head">
			<p class="tv-eyebrow"><?php esc_html_e( 'How we report', 'tickervelocity' ); ?></p>
			<h2 class="tv-trust__title"><?php esc_html_e( 'Fast, but never careless.', 'tickervelocity' ); ?></h2>
		</div>
		<ul class="tv-trust__grid tv-stagger">
			<li><strong><?php esc_html_e( 'Sourced', 'tickervelocity' ); ?></strong><span><?php esc_html_e( 'Every story links to its primary sources: filings, central banks, company releases.', 'tickervelocity' ); ?></span></li>
			<li><strong><?php esc_html_e( 'Reviewed', 'tickervelocity' ); ?></strong><span><?php esc_html_e( 'A named editor checks every article before it goes live.', 'tickervelocity' ); ?></span></li>
			<li><strong><?php esc_html_e( 'Independent', 'tickervelocity' ); ?></strong><span><?php esc_html_e( 'Partner links are always labelled and never shape what we cover.', 'tickervelocity' ); ?></span></li>
			<li><strong><?php esc_html_e( 'Corrected in the open', 'tickervelocity' ); ?></strong><span><?php esc_html_e( 'If we get something wrong, we fix it and say so on the article.', 'tickervelocity' ); ?></span></li>
		</ul>
		<a class="tv-more tv-more--light" href="<?php echo esc_url( tv_page_url( 'editorial-standards' ) ); ?>"><?php esc_html_e( 'Read our editorial standards', 'tickervelocity' ); ?> <span aria-hidden="true">&rarr;</span></a>
	</div>
</section>

<?php
get_footer();
