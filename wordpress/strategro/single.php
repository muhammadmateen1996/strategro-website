<?php
/**
 * Single blog post.
 *
 * @package Strategro
 */

get_header();

while ( have_posts() ) :
	the_post();
	$strategro_categories = get_the_category();
	?>
	<article <?php post_class( 'sg-article' ); ?>>
		<header class="sg-band sg-band--dark">
			<div class="sg-wrap sg-wrap--narrow">
				<p class="sg-band__crumbs"><a href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ?: home_url( '/' ) ); ?>"><?php esc_html_e( 'Insights', 'strategro' ); ?></a></p>
				<p class="sg-eyebrow">
					<?php if ( $strategro_categories ) : ?>
						<?php echo esc_html( $strategro_categories[0]->name ); ?> &middot;
					<?php endif; ?>
					<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'j F Y' ) ); ?></time>
				</p>
				<h1 class="sg-band__title sg-split"><?php the_title(); ?></h1>
				<p class="sg-band__meta"><?php echo esc_html( sprintf( __( 'By %s', 'strategro' ), get_the_author() ) ); ?></p>
			</div>
		</header>

		<div class="sg-wrap sg-wrap--narrow sg-article__body">
			<?php if ( has_post_thumbnail() ) : ?>
				<figure class="sg-article__media sg-reveal"><?php the_post_thumbnail( 'large' ); ?></figure>
			<?php endif; ?>
			<div class="sg-prose sg-reveal"><?php the_content(); ?></div>

			<aside class="sg-article__cta sg-glass-light sg-reveal">
				<div>
					<h2><?php esc_html_e( 'See what your business could automate first.', 'strategro' ); ?></h2>
					<p><?php esc_html_e( 'A free, no-obligation AI Systems Audit.', 'strategro' ); ?></p>
				</div>
				<a class="sg-button sg-magnetic" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Book a call', 'strategro' ); ?></a>
			</aside>
		</div>
	</article>
	<?php
endwhile;

get_footer();
