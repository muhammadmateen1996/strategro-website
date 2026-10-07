<?php
/**
 * Site header.
 *
 * @package Strategro
 */

$strategro_cta_url = strategro_mod( 'strategro_cta_url' );
$strategro_cta_url = 0 === strpos( $strategro_cta_url, '/' ) ? home_url( $strategro_cta_url ) : $strategro_cta_url;
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="sg-skip" href="#sg-content"><?php esc_html_e( 'Skip to content', 'strategro' ); ?></a>
<div class="sg-progress" aria-hidden="true"><span></span></div>

<?php if ( strategro_mod( 'strategro_page_transitions' ) ) : ?>
	<div class="sg-loader" aria-hidden="true">
		<div class="sg-loader__inner">
			<?php strategro_mark(); ?>
			<span class="sg-loader__dots"><i></i><i></i><i></i></span>
		</div>
	</div>
<?php endif; ?>

<?php if ( ! strategro_elementor_location( 'header' ) ) : ?>
<header class="sg-header<?php echo strategro_mod( 'strategro_sticky_header' ) ? '' : ' sg-header--static'; ?>" data-sg-header>
	<div class="sg-header__inner">
		<?php strategro_brand(); ?>

		<nav class="sg-nav" aria-label="<?php esc_attr_e( 'Primary', 'strategro' ); ?>">
			<?php strategro_menu( 'primary', 'sg-nav__list' ); ?>
		</nav>

		<a class="sg-header__cta sg-magnetic" href="<?php echo esc_url( $strategro_cta_url ); ?>">
			<span><?php echo esc_html( strategro_mod( 'strategro_cta_label' ) ); ?></span>
		</a>

		<button class="sg-burger" type="button" aria-expanded="false" aria-controls="sg-mobile-nav" aria-label="<?php esc_attr_e( 'Open menu', 'strategro' ); ?>">
			<span></span><span></span>
		</button>
	</div>

	<div class="sg-mobile" id="sg-mobile-nav" hidden>
		<nav aria-label="<?php esc_attr_e( 'Mobile', 'strategro' ); ?>">
			<?php strategro_menu( 'primary', 'sg-mobile__list' ); ?>
		</nav>
		<a class="sg-mobile__cta" href="<?php echo esc_url( $strategro_cta_url ); ?>"><?php echo esc_html( strategro_mod( 'strategro_cta_label' ) ); ?></a>
	</div>
</header>
<?php endif; ?>

<main id="sg-content" class="sg-main">
