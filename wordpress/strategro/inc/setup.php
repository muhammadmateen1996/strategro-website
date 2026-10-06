<?php
/**
 * One-click "Finish setup" after importing the Strategro Elementor kit.
 *
 * Elementor's kit import brings in pages, posts and menus but leaves a few
 * WordPress settings untouched. This shows a notice to administrators with
 * a button that:
 *   - sets the "Insights" page as the blog (posts) page,
 *   - sets "Home" as the homepage if no homepage is chosen yet,
 *   - connects the imported menus to the theme's header and footer slots
 *     (only slots that are still empty),
 *   - if the import created "privacy-policy-2" because WordPress's own
 *     unpublished draft held the "privacy-policy" address, removes that
 *     never-published draft and gives the real page its proper address.
 *     A published privacy page is never touched.
 *
 * @package Strategro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function strategro_setup_needed() {
	if ( get_option( 'strategro_setup_done' ) ) {
		return false;
	}
	return (bool) get_page_by_path( 'insights' ) || (bool) wp_get_nav_menu_object( 'Header' );
}

add_action( 'admin_notices', 'strategro_setup_notice' );
function strategro_setup_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( isset( $_GET['strategro-setup'] ) && 'done' === $_GET['strategro-setup'] ) { // phpcs:ignore WordPress.Security.NonceVerification
		$log = get_transient( 'strategro_setup_log' );
		echo '<div class="notice notice-success is-dismissible"><p><strong>' . esc_html__( 'Strategro setup finished.', 'strategro' ) . '</strong></p>';
		if ( $log ) {
			echo '<ul style="list-style:disc;margin-left:20px">';
			foreach ( $log as $line ) {
				echo '<li>' . esc_html( $line ) . '</li>';
			}
			echo '</ul>';
		}
		echo '</div>';
		return;
	}

	if ( ! strategro_setup_needed() ) {
		return;
	}

	$url = wp_nonce_url( admin_url( 'admin-post.php?action=strategro_finish_setup' ), 'strategro_finish_setup' );
	?>
	<div class="notice notice-info">
		<p><strong><?php esc_html_e( 'Strategro is almost ready.', 'strategro' ); ?></strong>
		<?php esc_html_e( 'Finish setup to make Insights your blog page, connect the imported menus to the header and footer, and give the Privacy Policy page its proper address.', 'strategro' ); ?></p>
		<p>
			<a class="button button-primary" href="<?php echo esc_url( $url ); ?>"><?php esc_html_e( 'Finish setup', 'strategro' ); ?></a>
			<a class="button" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=strategro_skip_setup' ), 'strategro_skip_setup' ) ); ?>"><?php esc_html_e( 'Dismiss', 'strategro' ); ?></a>
		</p>
	</div>
	<?php
}

add_action( 'admin_post_strategro_skip_setup', 'strategro_skip_setup' );
function strategro_skip_setup() {
	check_admin_referer( 'strategro_skip_setup' );
	if ( current_user_can( 'manage_options' ) ) {
		update_option( 'strategro_setup_done', 1 );
	}
	wp_safe_redirect( admin_url() );
	exit;
}

add_action( 'admin_post_strategro_finish_setup', 'strategro_finish_setup_request' );
function strategro_finish_setup_request() {
	check_admin_referer( 'strategro_finish_setup' );
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to change site settings.', 'strategro' ) );
	}
	set_transient( 'strategro_setup_log', strategro_finish_setup(), 300 );
	wp_safe_redirect( admin_url( '?strategro-setup=done' ) );
	exit;
}

/**
 * Applies the recommended settings and returns a list of what changed.
 */
function strategro_finish_setup() {
	$log = array();

	$insights = get_page_by_path( 'insights' );
	if ( $insights && (int) get_option( 'page_for_posts' ) !== $insights->ID ) {
		update_option( 'page_for_posts', $insights->ID );
		$log[] = __( 'Insights is now the blog page.', 'strategro' );
	}

	$home = get_page_by_path( 'home' );
	if ( $home && ( 'page' !== get_option( 'show_on_front' ) || ! get_option( 'page_on_front' ) ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $home->ID );
		$log[] = __( 'Home is now the homepage.', 'strategro' );
	}

	$slots     = array(
		'primary'         => 'Header',
		'footer_products' => 'Footer Products',
		'footer_company'  => 'Footer Company',
		'footer_legal'    => 'Footer Legal',
	);
	$locations = get_theme_mod( 'nav_menu_locations', array() );
	foreach ( $slots as $location => $menu_name ) {
		$menu = wp_get_nav_menu_object( $menu_name );
		if ( $menu && empty( $locations[ $location ] ) ) {
			$locations[ $location ] = $menu->term_id;
			/* translators: %s: menu name */
			$log[] = sprintf( __( 'Connected the "%s" menu.', 'strategro' ), $menu_name );
		}
	}
	set_theme_mod( 'nav_menu_locations', $locations );

	$imported = get_page_by_path( 'privacy-policy-2' );
	$default  = get_page_by_path( 'privacy-policy' );
	if ( $imported && 'publish' === $imported->post_status ) {
		if ( $default && 'draft' === $default->post_status ) {
			wp_delete_post( $default->ID, true );
			$default = null;
			$log[]   = __( 'Removed WordPress\'s unpublished sample privacy draft.', 'strategro' );
		}
		if ( ! $default ) {
			wp_update_post(
				array(
					'ID'        => $imported->ID,
					'post_name' => 'privacy-policy',
				)
			);
			update_option( 'wp_page_for_privacy_policy', $imported->ID );
			$log[] = __( 'Privacy Policy now lives at /privacy-policy/.', 'strategro' );
		} else {
			$log[] = __( 'You already have a published Privacy Policy, so the imported copy was left at /privacy-policy-2/ for you to compare.', 'strategro' );
		}
	}

	// Elementor's kit import can point the Legal links at the wrong page (or
	// drop one), so rebuild them from the real pages, linked by page rather
	// than by address so they keep working if a page is renamed.
	$legal_menu = wp_get_nav_menu_object( 'Footer Legal' );
	if ( $legal_menu ) {
		foreach ( (array) wp_get_nav_menu_items( $legal_menu->term_id ) as $item ) {
			wp_delete_post( $item->ID, true );
		}
		$privacy_id = (int) get_option( 'wp_page_for_privacy_policy' );
		$privacy    = $privacy_id ? get_post( $privacy_id ) : null;
		$privacy    = ( $privacy && 'publish' === $privacy->post_status ) ? $privacy : get_page_by_path( 'privacy-policy' );
		$pages      = array(
			__( 'Privacy Policy', 'strategro' )   => $privacy,
			__( 'Terms of Service', 'strategro' ) => get_page_by_path( 'terms' ),
		);
		foreach ( $pages as $title => $page ) {
			if ( $page && 'publish' === $page->post_status ) {
				wp_update_nav_menu_item(
					$legal_menu->term_id,
					0,
					array(
						'menu-item-title'     => $title,
						'menu-item-object'    => 'page',
						'menu-item-object-id' => $page->ID,
						'menu-item-type'      => 'post_type',
						'menu-item-status'    => 'publish',
					)
				);
			}
		}
		$log[] = __( 'Linked the Legal footer menu to your Privacy Policy and Terms pages.', 'strategro' );
	}

	if ( ! $log ) {
		$log[] = __( 'Everything was already set up.', 'strategro' );
	}

	flush_rewrite_rules();
	update_option( 'strategro_setup_done', 1 );
	return $log;
}
