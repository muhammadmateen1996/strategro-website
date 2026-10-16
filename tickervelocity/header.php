<?php
/**
 * Site header: top bar, main header, search panel, market strip.
 *
 * @package TickerVelocity
 */
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
	<meta name="theme-color" content="#070a10">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="tv-skip" href="#tv-content"><?php esc_html_e( 'Skip to content', 'tickervelocity' ); ?></a>
<?php if ( is_singular( 'post' ) ) : ?>
	<div class="tv-progress" aria-hidden="true"><span></span></div>
<?php endif; ?>

<div class="tv-topbar">
	<div class="tv-wrap tv-topbar__inner">
		<p class="tv-topbar__date"><span data-tv-today><?php echo esc_html( wp_date( 'l, j F Y' ) ); ?></span></p>
		<p class="tv-topbar__session" data-tv-session hidden><span class="tv-dot"></span><span data-tv-session-label></span></p>
		<nav class="tv-topbar__links" aria-label="<?php esc_attr_e( 'Follow', 'tickervelocity' ); ?>">
			<?php if ( tv_mod( 'tv_x_url' ) ) : ?>
				<a href="<?php echo esc_url( tv_mod( 'tv_x_url' ) ); ?>" target="_blank" rel="noopener noreferrer">X</a>
			<?php endif; ?>
			<?php if ( tv_mod( 'tv_linkedin_url' ) ) : ?>
				<a href="<?php echo esc_url( tv_mod( 'tv_linkedin_url' ) ); ?>" target="_blank" rel="noopener noreferrer">LinkedIn</a>
			<?php endif; ?>
			<a href="<?php echo esc_url( tv_page_url( 'markets' ) ); ?>"><?php esc_html_e( 'Live prices', 'tickervelocity' ); ?></a>
			<a href="<?php echo esc_url( get_bloginfo( 'rss2_url' ) ); ?>">RSS</a>
		</nav>
	</div>
</div>

<header class="tv-header" data-tv-header>
	<div class="tv-wrap tv-header__inner">
		<?php tv_brand(); ?>

		<nav class="tv-nav" aria-label="<?php esc_attr_e( 'Sections', 'tickervelocity' ); ?>">
			<?php tv_menu( 'primary', 'tv-nav__list' ); ?>
		</nav>

		<div class="tv-header__actions">
			<button class="tv-icon-btn" type="button" data-tv-search-toggle aria-expanded="false" aria-controls="tv-search">
				<svg viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
				<span class="screen-reader-text"><?php esc_html_e( 'Search', 'tickervelocity' ); ?></span>
			</button>
			<a class="tv-btn tv-btn--sm tv-header__subscribe" href="#newsletter" data-tv-subscribe><?php echo esc_html( tv_mod( 'tv_subscribe_label' ) ); ?></a>
			<button class="tv-icon-btn tv-burger" type="button" data-tv-menu-toggle aria-expanded="false" aria-controls="tv-mobile">
				<span></span><span></span>
				<span class="screen-reader-text"><?php esc_html_e( 'Menu', 'tickervelocity' ); ?></span>
			</button>
		</div>
	</div>

	<div class="tv-search" id="tv-search" hidden>
		<div class="tv-wrap"><?php get_search_form(); ?></div>
	</div>

	<div class="tv-mobile" id="tv-mobile" hidden>
		<nav aria-label="<?php esc_attr_e( 'Sections', 'tickervelocity' ); ?>">
			<?php tv_menu( 'primary', 'tv-mobile__list' ); ?>
		</nav>
		<?php get_search_form(); ?>
	</div>
</header>

<?php get_template_part( 'template-parts/market-strip' ); ?>

<main id="tv-content" class="tv-main">
