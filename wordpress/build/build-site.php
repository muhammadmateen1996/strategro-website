<?php
/**
 * WP-CLI entry point for the Strategro installer (the same code behind
 * Appearance > Strategro Setup). Needs Elementor active and the Strategro
 * theme active:
 *
 *   wp eval-file wordpress/build/build-site.php --user=<admin>
 *
 * @package Strategro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( "Run this with: wp eval-file build-site.php --user=<admin>\n" );
}

if ( ! function_exists( 'strategro_install_site' ) ) {
	WP_CLI::error( 'Activate the Strategro theme first.' );
}

try {
	foreach ( strategro_install_site() as $line ) {
		WP_CLI::log( '- ' . $line );
	}
} catch ( Throwable $e ) {
	WP_CLI::error( $e->getMessage() );
}
WP_CLI::success( 'Strategro site built.' );
