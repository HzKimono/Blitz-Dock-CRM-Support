<?php
/**
 * Bootstrap file for the Blitz Dock plugin.
 *
 * @package BlitzDock
 * @since 0.1.0
 * @license GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Blitz Dock
 * Description:       Provides the Blitz Dock admin interface.
 * Version:           0.1.1
 * Requires at least: 6.4
 * Requires PHP:      7.4
 * Tested up to:      6.6
 * Author:            Blitz Dock Contributors
 * License:           GPLv2 or later
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

/**
 * Plugin version constant.
 *
 * @since 0.1.0
 */
if ( ! defined( 'BLITZ_DOCK_VERSION' ) ) {
    $data = get_file_data( __FILE__, array( 'Version' => 'Version' ), 'plugin' );
    define( 'BLITZ_DOCK_VERSION', isset( $data['Version'] ) && $data['Version'] !== '' ? $data['Version'] : '0.1.1' );
}

// Register a simple PSR-4 autoloader for BlitzDock\ namespaced classes.
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

// Bootstrap the plugin.
new \BlitzDock\Core\Plugin();