<?php
/**
 * Plugin bootstrap for Blitz Dock.
 *
 * @package BlitzDock
 *
 * @wordpress-plugin
 * Plugin Name:       Blitz Dock
 * Plugin URI:        https://github.com/blitz-dock/blitz-dock
 * Description:       Adds the Blitz Dock admin interface and public support panel.
 * Version:           0.2.2
 * Requires at least: 6.4
 * Requires PHP:      7.4
 * Tested up to:      6.6
 * Author:            Blitz Dock Contributors
 * Author URI:        https://github.com/blitz-dock
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       blitz-dock
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BLITZ_DOCK_FILE', __FILE__ );
define( 'BLITZ_DOCK_PATH', plugin_dir_path( BLITZ_DOCK_FILE ) );
define( 'BLITZ_DOCK_URL', plugin_dir_url( BLITZ_DOCK_FILE ) );

if ( ! defined( 'BLITZ_DOCK_VERSION' ) ) {
	$plugin_data = get_file_data( __FILE__, array( 'Version' => 'Version' ) );
    $version     = isset( $plugin_data['Version'] ) && '' !== $plugin_data['Version'] ? $plugin_data['Version'] : '0.2.2';

	define( 'BLITZ_DOCK_VERSION', $version );
}

spl_autoload_register(
	static function ( $class ) {
		if ( 0 !== strpos( $class, 'BlitzDock\\' ) ) {
			return;
		}

		$relative = substr( $class, strlen( 'BlitzDock\\' ) );
		$relative = str_replace( '\\', DIRECTORY_SEPARATOR, $relative );
		$file     = BLITZ_DOCK_PATH . 'includes/' . $relative . '.php';

		if ( is_readable( $file ) ) {
			require $file;
		}
	}
);

new \BlitzDock\Core\Plugin();