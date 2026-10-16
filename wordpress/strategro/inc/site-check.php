<?php
/**
 * Appearance > Strategro Site Check: what the published site actually uses
 * (Elementor Pro, Essential Addons and other add-ons), what still overrides
 * the Strategro design, and what can be removed or switched on for speed.
 *
 * @package Strategro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', 'strategro_site_check_menu' );
function strategro_site_check_menu() {
	add_theme_page( __( 'Strategro Site Check', 'strategro' ), __( 'Strategro Site Check', 'strategro' ), 'manage_options', 'strategro-site-check', 'strategro_site_check_page' );
}

/** Widgets that list posts: a published page using one is probably an old blog page. */
function strategro_post_list_widgets() {
	return array( 'posts', 'archive-posts', 'loop-grid', 'loop-carousel', 'portfolio', 'eael-post-grid', 'eael-post-timeline', 'eael-post-carousel', 'eael-post-block', 'eael-post-list', 'eael-content-timeline', 'wp-widget-recent-posts', 'wp-widget-archives' );
}

/** Which plugin (or the theme) provides each registered Elementor widget. */
function strategro_widget_sources() {
	$sources = array();
	if ( ! class_exists( '\Elementor\Plugin' ) ) {
		return $sources;
	}
	$plugins = function_exists( 'get_plugins' ) ? get_plugins() : array();
	$base    = trailingslashit( wp_normalize_path( WP_PLUGIN_DIR ) );
	foreach ( \Elementor\Plugin::$instance->widgets_manager->get_widget_types() as $name => $widget ) {
		$class = get_class( $widget );
		if ( 0 === strpos( $class, 'ElementorPro\\' ) ) {
			$sources[ $name ] = 'Elementor Pro';
		} elseif ( 0 === strpos( $class, 'Elementor\\' ) ) {
			$sources[ $name ] = 'Elementor';
		} elseif ( 0 === strpos( $class, 'Strategro_' ) ) {
			$sources[ $name ] = 'Strategro theme';
		} else {
			$file  = wp_normalize_path( (string) ( new ReflectionClass( $class ) )->getFileName() );
			$label = __( 'Another plugin', 'strategro' );
			if ( 0 === strpos( $file, $base ) ) {
				$folder = strtok( substr( $file, strlen( $base ) ), '/' );
				foreach ( $plugins as $plugin_file => $data ) {
					if ( 0 === strpos( $plugin_file, $folder . '/' ) ) {
						$label = $data['Name'];
						break;
					}
				}
			}
			$sources[ $name ] = $label;
		}
	}
	return $sources;
}

/** Walks Elementor data and calls $visit( $element ) for every element. */
function strategro_walk_elements( $elements, $visit ) {
	foreach ( (array) $elements as $element ) {
		if ( ! is_array( $element ) ) {
			continue;
		}
		$visit( $element );
		if ( ! empty( $element['elements'] ) ) {
			strategro_walk_elements( $element['elements'], $visit );
		}
	}
}

/** Scans everything published with Elementor. */
function strategro_site_scan() {
	$sources = strategro_widget_sources();
	$result  = array(
		'usage'         => array(), // plugin => widget => post ids.
		'pro_features'  => array(), // feature => post ids.
		'missing'       => array(), // widget => post ids (plugin not active).
		'builder_posts' => array(),
		'list_pages'    => array(),
		'scanned'       => 0,
	);

	$types = array_values( array_unique( array_merge( get_post_types( array( 'public' => true ) ), array( 'elementor_library', 'e-landing-page', 'e-floating-buttons' ) ) ) );
	$ids   = get_posts(
		array(
			'post_type'      => $types,
			'post_status'    => 'publish',
			'posts_per_page' => 800,
			'fields'         => 'ids',
			'meta_key'       => '_elementor_edit_mode', // phpcs:ignore WordPress.DB.SlowDBQuery
			'meta_value'     => 'builder', // phpcs:ignore WordPress.DB.SlowDBQuery
		)
	);

	foreach ( $ids as $id ) {
		$type = get_post_type( $id );
		if ( 'elementor_library' === $type ) {
			// Saved templates only matter while they're switched on somewhere.
			$template_type = get_post_meta( $id, '_elementor_template_type', true );
			if ( 'kit' === $template_type || ! get_post_meta( $id, '_elementor_conditions', true ) ) {
				continue;
			}
		}
		$data = json_decode( (string) get_post_meta( $id, '_elementor_data', true ), true );
		if ( ! is_array( $data ) ) {
			continue;
		}
		++$result['scanned'];
		if ( 'post' === $type ) {
			$result['builder_posts'][] = $id;
		}

		$page_settings = get_post_meta( $id, '_elementor_page_settings', true );
		if ( is_array( $page_settings ) && ! empty( $page_settings['custom_css'] ) ) {
			$result['pro_features'][ __( 'Custom CSS', 'strategro' ) ][ $id ] = $id;
		}

		strategro_walk_elements(
			$data,
			function ( $element ) use ( $id, $type, $sources, &$result ) {
				$settings = isset( $element['settings'] ) && is_array( $element['settings'] ) ? $element['settings'] : array();
				if ( ! empty( $settings['__dynamic__'] ) ) {
					$result['pro_features'][ __( 'Dynamic content', 'strategro' ) ][ $id ] = $id;
				}
				if ( ! empty( $settings['custom_css'] ) ) {
					$result['pro_features'][ __( 'Custom CSS', 'strategro' ) ][ $id ] = $id;
				}
				if ( ! empty( $settings['sticky'] ) ) {
					$result['pro_features'][ __( 'Sticky elements', 'strategro' ) ][ $id ] = $id;
				}
				foreach ( $settings as $key => $value ) {
					if ( 'yes' === $value && preg_match( '/^(background_)?motion_fx_motion_fx_(scrolling|mouse)$/', $key ) ) {
						$result['pro_features'][ __( 'Scrolling and mouse effects', 'strategro' ) ][ $id ] = $id;
					}
				}
				if ( empty( $element['widgetType'] ) ) {
					return;
				}
				$widget = $element['widgetType'];
				if ( in_array( $widget, strategro_post_list_widgets(), true ) && 'page' === $type ) {
					$result['list_pages'][ $id ] = $id;
				}
				if ( ! isset( $sources[ $widget ] ) ) {
					$result['missing'][ $widget ][ $id ] = $id;
					return;
				}
				$result['usage'][ $sources[ $widget ] ][ $widget ][ $id ] = $id;
			}
		);
	}
	return $result;
}

/** Caching and image plugins that are active, by name. */
function strategro_active_plugins_matching( $slugs ) {
	$found = array();
	if ( ! function_exists( 'get_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	foreach ( get_plugins() as $file => $data ) {
		if ( is_plugin_active( $file ) && in_array( strtok( $file, '/' ), $slugs, true ) ) {
			$found[] = $data['Name'];
		}
	}
	return $found;
}

function strategro_cache_plugins() {
	return strategro_active_plugins_matching( array( 'wp-rocket', 'litespeed-cache', 'w3-total-cache', 'wp-super-cache', 'wp-fastest-cache', 'sg-cachepress', 'breeze', 'cache-enabler', 'comet-cache', 'hummingbird-performance', 'wp-optimize', 'autoptimize', 'perfmatters', 'nitropack', 'flying-press', 'swift-performance-lite', 'jetpack-boost', 'cloudflare' ) );
}

function strategro_image_plugins() {
	return strategro_active_plugins_matching( array( 'ewww-image-optimizer', 'imagify', 'shortpixel-image-optimiser', 'wp-smushit', 'optimole-wp', 'litespeed-cache', 'webp-express', 'webp-converter-for-media', 'tiny-compress-images', 'sg-cachepress', 'wp-optimize' ) );
}

/** The heaviest images in the media library (over 400 KB). */
function strategro_heavy_images( $limit = 8 ) {
	$heavy = array();
	$ids   = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'post_status'    => 'inherit',
			'posts_per_page' => 1500,
			'fields'         => 'ids',
		)
	);
	foreach ( $ids as $id ) {
		$meta = wp_get_attachment_metadata( $id );
		$size = isset( $meta['filesize'] ) ? (int) $meta['filesize'] : 0;
		if ( ! $size ) {
			$file = get_attached_file( $id );
			$size = $file && file_exists( $file ) ? (int) filesize( $file ) : 0;
		}
		if ( $size > 400 * KB_IN_BYTES ) {
			$heavy[ $id ] = $size;
		}
	}
	arsort( $heavy );
	return array( 'count' => count( $heavy ), 'top' => array_slice( $heavy, 0, $limit, true ) );
}

/** Links to the posts in $ids ("About, Contact and 3 more"). */
function strategro_post_links( $ids, $max = 4 ) {
	$links = array();
	foreach ( array_slice( array_values( $ids ), 0, $max ) as $id ) {
		$title   = get_the_title( $id ) ? get_the_title( $id ) : __( '(no title)', 'strategro' );
		$url     = 'elementor_library' === get_post_type( $id ) ? admin_url( 'post.php?post=' . $id . '&action=elementor' ) : get_edit_post_link( $id );
		$links[] = '<a href="' . esc_url( $url ) . '">' . esc_html( $title ) . '</a>';
	}
	$more = count( $ids ) - $max;
	if ( $more > 0 ) {
		/* translators: %d: number of further pages */
		$links[] = esc_html( sprintf( __( 'and %d more', 'strategro' ), $more ) );
	}
	return implode( ', ', $links );
}

function strategro_check_row( $status, $title, $body ) {
	$icons = array(
		'good' => array( '&#10003;', '#00a32a' ),
		'act'  => array( '!', '#d63638' ),
		'tip'  => array( '&#9679;', '#dba617' ),
	);
	$icon  = $icons[ $status ];
	printf(
		'<tr><td style="width:28px;vertical-align:top"><span style="display:inline-grid;place-items:center;width:22px;height:22px;border-radius:50%%;background:%2$s;color:#fff;font-weight:700;font-size:12px">%1$s</span></td><td><strong>%3$s</strong><div style="margin-top:4px">%4$s</div></td></tr>',
		$icon[0], // phpcs:ignore WordPress.Security.EscapeOutput -- fixed entity.
		esc_attr( $icon[1] ),
		esc_html( $title ),
		wp_kses( $body, strategro_check_allowed_html() )
	);
}

/** Post HTML plus the small form used by the "Apply" button. */
function strategro_check_allowed_html() {
	$allowed           = wp_kses_allowed_html( 'post' );
	$allowed['form']   = array(
		'method' => true,
		'action' => true,
		'style'  => true,
	);
	$allowed['input']  = array(
		'type'  => true,
		'name'  => true,
		'value' => true,
	);
	$allowed['button'] = array( 'class' => true );
	return $allowed;
}

function strategro_site_check_page() {
	if ( ! function_exists( 'get_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	$elementor = class_exists( '\Elementor\Plugin' );
	$scan      = $elementor ? strategro_site_scan() : null;
	$pro       = is_plugin_active( 'elementor-pro/elementor-pro.php' );
	$cache     = strategro_cache_plugins();
	$notice    = get_transient( 'strategro_speed_applied' );
	delete_transient( 'strategro_speed_applied' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Strategro Site Check', 'strategro' ); ?></h1>
		<p><?php esc_html_e( 'What your published site actually uses, what still replaces the Strategro design, and what you can remove to make it faster. Nothing here changes your site unless you press a button.', 'strategro' ); ?></p>
		<?php if ( $notice ) : ?>
			<div class="notice notice-success"><p><?php esc_html_e( 'Elementor speed settings applied and its CSS rebuilt.', 'strategro' ); ?></p></div>
		<?php endif; ?>

		<h2><?php esc_html_e( '1. Still seeing old pages?', 'strategro' ); ?></h2>
		<table class="widefat striped" style="max-width:980px"><tbody>
		<?php
		/* translators: %s: theme version */
		strategro_check_row( 'good', sprintf( __( 'Strategro theme %s is active', 'strategro' ), STRATEGRO_VERSION ), __( 'If this number is lower than the version you uploaded, upload the theme again and choose "Replace active with uploaded".', 'strategro' ) );

		if ( $cache ) {
			/* translators: %s: plugin names */
			strategro_check_row( 'act', __( 'Clear your cache after every theme update', 'strategro' ), sprintf( __( 'Your site uses %s, which keeps copies of pages. Until you clear it (look for "Purge all" or "Clear cache" in its menu or the admin bar), visitors and you can keep seeing the old design.', 'strategro' ), '<strong>' . esc_html( implode( ', ', $cache ) ) . '</strong>' ) );
		} else {
			strategro_check_row( 'tip', __( 'Clear your host\'s cache after updating', 'strategro' ), __( 'No caching plugin is active, but many hosts cache pages themselves (and Cloudflare can too). If you still see old pages, clear the cache in your hosting panel, then reload with Ctrl+F5 (Cmd+Shift+R on a Mac).', 'strategro' ) );
		}

		$overrides = function_exists( 'strategro_header_overrides' ) ? strategro_header_overrides() : array();
		if ( $overrides ) {
			$names = array();
			foreach ( $overrides as $template ) {
				$names[] = '<a href="' . esc_url( $template['edit_url'] ) . '">' . esc_html( $template['title'] ? $template['title'] : __( '(no title)', 'strategro' ) ) . '</a> (' . esc_html( $template['type'] ) . ', ' . ( $template['active'] ? '<strong>' . esc_html__( 'showing', 'strategro' ) . '</strong>' : esc_html__( 'hidden by the theme', 'strategro' ) ) . ')';
			}
			strategro_check_row( 'tip', __( 'Old Theme Builder templates are still switched on', 'strategro' ), __( 'The theme hides these, so the Strategro design shows. Delete them for good from Templates > Saved Templates:', 'strategro' ) . ' ' . implode( '; ', $names ) );
		} else {
			strategro_check_row( 'good', __( 'No old header, footer or blog templates', 'strategro' ), __( 'Nothing replaces the Strategro header, footer, Insights list or articles.', 'strategro' ) );
		}

		if ( $scan && $scan['list_pages'] ) {
			strategro_check_row( 'act', __( 'Old blog pages are still published', 'strategro' ), __( 'These pages list posts with an older design, so links or menus that point to them show the old blog:', 'strategro' ) . ' ' . strategro_post_links( $scan['list_pages'], 6 ) . '. ' . __( 'Move them to Draft (Quick Edit > Status). Their old addresses /blog/, /blogs/ and /news/ then lead to Insights automatically, and the real blog is your Insights page.', 'strategro' ) );
		}

		if ( $scan && $scan['builder_posts'] ) {
			$count = count( $scan['builder_posts'] );
			if ( strategro_mod( 'strategro_blog_unify' ) ) {
				/* translators: %d: number of posts */
				strategro_check_row( 'good', sprintf( _n( '%d post was built with Elementor', '%d posts were built with Elementor', $count, 'strategro' ), $count ), __( 'They are shown in the Strategro article design, using their text, headings, images and links:', 'strategro' ) . ' ' . strategro_post_links( $scan['builder_posts'] ) );
			} else {
				/* translators: %d: number of posts */
				strategro_check_row( 'tip', sprintf( _n( '%d post keeps its old Elementor layout', '%d posts keep their old Elementor layout', $count, 'strategro' ), $count ), __( 'To show them in the Strategro article design, tick "Show posts built with Elementor in the article design" in Customize > Strategro Blog.', 'strategro' ) . ' ' . strategro_post_links( $scan['builder_posts'] ) );
			}
		}
		?>
		</tbody></table>

		<?php if ( $scan ) : ?>
			<h2><?php esc_html_e( '2. Plugins: what your published pages use', 'strategro' ); ?></h2>
			<p>
			<?php
			/* translators: %d: number of pages, posts and templates */
			echo esc_html( sprintf( __( 'Checked %d published pages, posts and active templates built with Elementor. Drafts and the trash are ignored.', 'strategro' ), $scan['scanned'] ) );
			?>
			</p>
			<table class="widefat striped" style="max-width:980px"><tbody>
			<?php
			strategro_check_row( 'good', 'Elementor', __( 'Needed: every Strategro page is built with it.', 'strategro' ) );

			$widget_sources = strategro_widget_sources();
			$addons         = array();
			if ( $pro ) {
				$addons['Elementor Pro'] = true;
			}
			foreach ( array_keys( $scan['usage'] ) as $source ) {
				if ( ! in_array( $source, array( 'Elementor', 'Strategro theme' ), true ) ) {
					$addons[ $source ] = true;
				}
			}
			foreach ( $widget_sources as $source ) {
				if ( ! in_array( $source, array( 'Elementor', 'Strategro theme' ), true ) ) {
					$addons[ $source ] = true;
				}
			}

			foreach ( array_keys( $addons ) as $source ) {
				$widgets  = isset( $scan['usage'][ $source ] ) ? $scan['usage'][ $source ] : array();
				$features = 'Elementor Pro' === $source ? $scan['pro_features'] : array();
				if ( ! $widgets && ! $features ) {
					$body = __( 'Nothing on your published pages uses it, so it only adds weight. Deactivate it (Plugins > Installed Plugins), check your pages, and delete it if all is well. You can always install it again.', 'strategro' );
					if ( 'Elementor Pro' === $source ) {
						$body .= ' ' . __( 'This also switches off the old Theme Builder templates for good. Without a working licence Elementor Pro gets no security updates, another reason to remove it.', 'strategro' );
					}
					strategro_check_row( 'act', sprintf( /* translators: %s: plugin name */ __( '%s: not used', 'strategro' ), $source ), $body );
					continue;
				}
				$lines = array();
				foreach ( $widgets as $widget => $ids ) {
					$lines[] = '<code>' . esc_html( $widget ) . '</code> ' . esc_html__( 'on', 'strategro' ) . ' ' . strategro_post_links( $ids, 3 );
				}
				foreach ( $features as $feature => $ids ) {
					$lines[] = esc_html( $feature ) . ' ' . esc_html__( 'on', 'strategro' ) . ' ' . strategro_post_links( $ids, 3 );
				}
				$body = __( 'Used here:', 'strategro' ) . '<br>' . implode( '<br>', $lines ) . '<br>' . __( 'If these are old pages you no longer need, move them to Draft and run this check again. When nothing is listed, the plugin can go.', 'strategro' );
				if ( false !== stripos( $source, 'Essential Addons' ) ) {
					$body .= ' ' . __( 'Meanwhile, in Essential Addons > Elements, switch off every element not listed here so its files are never loaded.', 'strategro' );
				}
				strategro_check_row( 'tip', sprintf( /* translators: %s: plugin name */ __( '%s: in use', 'strategro' ), $source ), $body );
			}

			if ( $scan['missing'] ) {
				$lines = array();
				foreach ( $scan['missing'] as $widget => $ids ) {
					$lines[] = '<code>' . esc_html( $widget ) . '</code> ' . esc_html__( 'on', 'strategro' ) . ' ' . strategro_post_links( $ids, 3 );
				}
				strategro_check_row( 'act', __( 'Widgets from a plugin that is no longer active', 'strategro' ), __( 'These show as empty boxes on the live site. Remove them from the page, or reactivate the plugin:', 'strategro' ) . '<br>' . implode( '<br>', $lines ) );
			}
			?>
			</tbody></table>
		<?php endif; ?>

		<h2><?php esc_html_e( '3. Speed', 'strategro' ); ?></h2>
		<table class="widefat striped" style="max-width:980px"><tbody>
		<?php
		strategro_check_row( 'good', __( 'Already done by the theme', 'strategro' ), __( 'Fonts served from your own site (no Google connection) and preloaded; Elementor\'s duplicate copies of the same fonts removed; WordPress\'s emoji script removed; block editor styles only where a page uses blocks; article script only on articles.', 'strategro' ) );

		if ( $elementor ) {
			$icons_inline = \Elementor\Plugin::$instance->experiments->is_feature_active( 'e_font_icon_svg' );
			$swap         = 'swap' === get_option( 'elementor_font_display', 'auto' );
			if ( $icons_inline && $swap ) {
				strategro_check_row( 'good', __( 'Elementor speed settings are on', 'strategro' ), __( 'Icons are drawn inline (no Font Awesome download) and text shows while fonts load.', 'strategro' ) );
			} else {
				$form = '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" style="margin-top:8px"><input type="hidden" name="action" value="strategro_speed">' . wp_nonce_field( 'strategro_speed', '_wpnonce', true, false ) . '<button class="button button-primary">' . esc_html__( 'Apply Elementor speed settings', 'strategro' ) . '</button></form>';
				strategro_check_row( 'act', __( 'Elementor speed settings are off', 'strategro' ), __( 'Elementor is loading the whole Font Awesome icon font (around 150 KB) for a handful of icons, and/or hiding text until fonts load. This button switches on "Inline Font Icons" and "Font display: swap", then rebuilds Elementor\'s CSS.', 'strategro' ) . $form );
			}
		}

		if ( ! $cache ) {
			$host = isset( $_SERVER['SERVER_SOFTWARE'] ) && false !== stripos( sanitize_text_field( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) ), 'litespeed' );
			strategro_check_row( 'act', __( 'Add page caching', 'strategro' ), $host ? __( 'Your server runs LiteSpeed: install the free "LiteSpeed Cache" plugin and switch on page caching. It is the single biggest speed gain.', 'strategro' ) : __( 'Install one caching plugin (your host may recommend one; otherwise the free "WP Super Cache" is simple). Page caching is the single biggest speed gain.', 'strategro' ) );
		}

		$images = strategro_heavy_images();
		$optim  = strategro_image_plugins();
		if ( $images['count'] ) {
			$list = array();
			foreach ( $images['top'] as $id => $size ) {
				$list[] = '<a href="' . esc_url( get_edit_post_link( $id ) ) . '">' . esc_html( get_the_title( $id ) ? get_the_title( $id ) : basename( (string) get_attached_file( $id ) ) ) . '</a> (' . esc_html( size_format( $size ) ) . ')';
			}
			/* translators: %d: number of images */
			$body = sprintf( _n( '%d image is over 400 KB:', '%d images are over 400 KB:', $images['count'], 'strategro' ), $images['count'] ) . ' ' . implode( ', ', $list ) . '. ';
			$body .= $optim ? sprintf( /* translators: %s: plugin names */ __( 'Run %s\'s bulk optimisation on them.', 'strategro' ), esc_html( implode( ', ', $optim ) ) ) : __( 'Install an image optimiser (for example the free "Converter for Media" or "EWWW Image Optimizer") to shrink them and serve WebP, or re-upload them at 1600-2000 px wide.', 'strategro' );
			strategro_check_row( 'tip', __( 'Large images', 'strategro' ), $body );
		} else {
			strategro_check_row( 'good', __( 'No oversized images', 'strategro' ), __( 'Every image in the media library is under 400 KB.', 'strategro' ) );
		}
		?>
		</tbody></table>

		<h2><?php esc_html_e( '4. Leftovers you can tidy up', 'strategro' ); ?></h2>
		<p><?php esc_html_e( 'These don\'t slow visitors down, but unused plugins and themes are a security risk and clutter the admin.', 'strategro' ); ?></p>
		<table class="widefat striped" style="max-width:980px"><tbody>
		<?php
		$inactive_plugins = array();
		foreach ( get_plugins() as $file => $data ) {
			if ( ! is_plugin_active( $file ) ) {
				$inactive_plugins[] = $data['Name'];
			}
		}
		if ( $inactive_plugins ) {
			strategro_check_row( 'tip', __( 'Inactive plugins', 'strategro' ), esc_html( implode( ', ', $inactive_plugins ) ) . '. <a href="' . esc_url( admin_url( 'plugins.php?plugin_status=inactive' ) ) . '">' . esc_html__( 'Delete the ones you don\'t need', 'strategro' ) . '</a>.' );
		}

		$themes = array();
		foreach ( wp_get_themes() as $slug => $theme ) {
			if ( get_stylesheet() !== $slug && get_template() !== $slug ) {
				$themes[] = $theme->get( 'Name' );
			}
		}
		if ( count( $themes ) > 1 ) {
			strategro_check_row( 'tip', __( 'Unused themes', 'strategro' ), esc_html( implode( ', ', $themes ) ) . '. ' . __( 'Keep one default WordPress theme (such as Twenty Twenty-Five) as a fallback and delete the rest, including the theme from your old design.', 'strategro' ) . ' <a href="' . esc_url( admin_url( 'themes.php' ) ) . '">' . esc_html__( 'Themes', 'strategro' ) . '</a>' );
		}

		$old_pages = get_posts(
			array(
				'post_type'      => 'page',
				'post_status'    => 'draft',
				'posts_per_page' => 50,
				's'              => 'Previous:',
				'fields'         => 'ids',
			)
		);
		if ( $old_pages ) {
			strategro_check_row( 'tip', __( 'Old pages kept by the Strategro installer', 'strategro' ), __( 'Drafts named "Previous: ...". Copy anything you still need, then trash them:', 'strategro' ) . ' ' . strategro_post_links( $old_pages, 6 ) );
		}

		$saved = get_posts(
			array(
				'post_type'      => 'elementor_library',
				'post_status'    => 'any',
				'posts_per_page' => 300,
				'fields'         => 'ids',
				'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery
					array(
						'key'     => '_elementor_template_type',
						'value'   => 'kit',
						'compare' => '!=',
					),
				),
			)
		);
		$unused = 0;
		foreach ( $saved as $id ) {
			if ( ! get_post_meta( $id, '_elementor_conditions', true ) ) {
				++$unused;
			}
		}
		if ( $unused ) {
			/* translators: %d: number of templates */
			strategro_check_row( 'tip', __( 'Saved Elementor templates', 'strategro' ), sprintf( _n( '%d saved template is not switched on anywhere.', '%d saved templates are not switched on anywhere.', $unused, 'strategro' ), $unused ) . ' ' . __( 'Harmless, but if they belong to the old design you can delete them.', 'strategro' ) . ' <a href="' . esc_url( admin_url( 'edit.php?post_type=elementor_library&tabs_group=library' ) ) . '">' . esc_html__( 'Saved Templates', 'strategro' ) . '</a>' );
		}

		global $wpdb;
		$revisions = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'revision'" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		if ( $revisions > 300 ) {
			/* translators: %s: number of revisions */
			strategro_check_row( 'tip', __( 'Old revisions', 'strategro' ), sprintf( __( '%s saved revisions of pages and posts. A clean-up plugin such as WP-Optimize can remove old ones and tidy the database.', 'strategro' ), number_format_i18n( $revisions ) ) );
		}
		if ( ! $inactive_plugins && count( $themes ) <= 1 && ! $old_pages && ! $unused && $revisions <= 300 ) {
			strategro_check_row( 'good', __( 'Nothing to tidy up', 'strategro' ), __( 'No unused plugins, themes, old pages or templates.', 'strategro' ) );
		}
		?>
		</tbody></table>
		<p><a class="button" href="<?php echo esc_url( admin_url( 'themes.php?page=strategro-site-check' ) ); ?>"><?php esc_html_e( 'Run the check again', 'strategro' ); ?></a></p>
	</div>
	<?php
}

add_action( 'admin_post_strategro_speed', 'strategro_apply_speed_settings' );
function strategro_apply_speed_settings() {
	check_admin_referer( 'strategro_speed' );
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to change site settings.', 'strategro' ) );
	}
	update_option( 'elementor_experiment-e_font_icon_svg', 'active' );
	update_option( 'elementor_font_display', 'swap' );
	if ( class_exists( '\Elementor\Plugin' ) ) {
		\Elementor\Plugin::$instance->files_manager->clear_cache();
	}
	set_transient( 'strategro_speed_applied', 1, 60 );
	wp_safe_redirect( admin_url( 'themes.php?page=strategro-site-check' ) );
	exit;
}
