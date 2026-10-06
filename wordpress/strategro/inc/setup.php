<?php
/**
 * Appearance > Strategro Setup: builds every Strategro page, post, menu
 * and Elementor brand setting with one click, so no Elementor import tool
 * (or Elementor Pro) is needed.
 *
 * @package Strategro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', 'strategro_setup_menu' );
function strategro_setup_menu() {
	add_theme_page( __( 'Strategro Setup', 'strategro' ), __( 'Strategro Setup', 'strategro' ), 'manage_options', 'strategro-setup', 'strategro_setup_page' );
}

add_action( 'admin_notices', 'strategro_setup_notice' );
function strategro_setup_notice() {
	$screen = get_current_screen();
	if ( ! current_user_can( 'manage_options' ) || get_option( 'strategro_installed_version' ) || ( $screen && 'appearance_page_strategro-setup' === $screen->id ) ) {
		return;
	}
	printf(
		'<div class="notice notice-info"><p><strong>%s</strong> %s</p><p><a class="button button-primary" href="%s">%s</a></p></div>',
		esc_html__( 'The Strategro theme is active.', 'strategro' ),
		esc_html__( 'One more step: build the Strategro pages.', 'strategro' ),
		esc_url( admin_url( 'themes.php?page=strategro-setup' ) ),
		esc_html__( 'Open Strategro Setup', 'strategro' )
	);
}

function strategro_setup_page() {
	$result = get_transient( 'strategro_setup_result' );
	delete_transient( 'strategro_setup_result' );
	$elementor = did_action( 'elementor/loaded' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Strategro Setup', 'strategro' ); ?></h1>

		<?php if ( $result && ! empty( $result['error'] ) ) : ?>
			<div class="notice notice-error"><p><strong><?php esc_html_e( 'Setup could not finish:', 'strategro' ); ?></strong> <?php echo esc_html( $result['error'] ); ?></p></div>
		<?php elseif ( $result ) : ?>
			<div class="notice notice-success">
				<p><strong><?php esc_html_e( 'Done. Your Strategro site is built.', 'strategro' ); ?></strong>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank"><?php esc_html_e( 'View the site', 'strategro' ); ?> &rarr;</a></p>
				<ul style="list-style:disc;margin-left:20px">
					<?php foreach ( $result['log'] as $line ) : ?>
						<li><?php echo esc_html( $line ); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

		<div class="card" style="max-width:720px">
			<h2><?php esc_html_e( 'Build the Strategro pages', 'strategro' ); ?></h2>
			<p><?php esc_html_e( 'This creates everything in one go, using only free Elementor widgets (Elementor Pro is not needed):', 'strategro' ); ?></p>
			<ul style="list-style:disc;margin-left:20px">
				<li><?php esc_html_e( 'Pages: Home, Products, Custom Builds, About, Insights, Contact, Privacy Policy, Terms. All editable with Elementor.', 'strategro' ); ?></li>
				<li><?php esc_html_e( 'Three starter blog posts (skipped if you already have posts at the same address).', 'strategro' ); ?></li>
				<li><?php esc_html_e( 'Header and footer menus, named "Strategro ..." so your existing menus are untouched.', 'strategro' ); ?></li>
				<li><?php esc_html_e( 'Elementor global colours and fonts set to the Strategro brand.', 'strategro' ); ?></li>
				<li><?php esc_html_e( 'Home becomes the homepage and Insights the blog page.', 'strategro' ); ?></li>
			</ul>
			<p><strong><?php esc_html_e( 'Nothing is deleted.', 'strategro' ); ?></strong>
			<?php esc_html_e( 'If you already have a page at one of those addresses (for example /about/), it is kept as a draft renamed "Previous: ..." at /about-old/, so you can copy anything across.', 'strategro' ); ?></p>
			<p><?php esc_html_e( 'Running it again later rebuilds the Strategro pages to their original design, so do that only before you start editing them.', 'strategro' ); ?></p>

			<?php if ( ! $elementor ) : ?>
				<p class="notice notice-warning" style="padding:10px"><?php esc_html_e( 'Activate the Elementor plugin first (Plugins > Installed Plugins).', 'strategro' ); ?></p>
			<?php else : ?>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<input type="hidden" name="action" value="strategro_install">
					<?php wp_nonce_field( 'strategro_install' ); ?>
					<?php
					submit_button(
						get_option( 'strategro_installed_version' ) ? __( 'Rebuild the Strategro pages', 'strategro' ) : __( 'Build my Strategro site', 'strategro' ),
						'primary large',
						'submit',
						false
					);
					?>
				</form>
			<?php endif; ?>
		</div>
	</div>
	<?php
}

add_action( 'admin_post_strategro_install', 'strategro_install_request' );
function strategro_install_request() {
	check_admin_referer( 'strategro_install' );
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to change site settings.', 'strategro' ) );
	}

	try {
		$result = array( 'log' => strategro_install_site() );
	} catch ( Throwable $e ) {
		$result = array( 'error' => $e->getMessage() );
	}

	set_transient( 'strategro_setup_result', $result, 300 );
	wp_safe_redirect( admin_url( 'themes.php?page=strategro-setup' ) );
	exit;
}
