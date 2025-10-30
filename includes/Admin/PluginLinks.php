<?php
/**
 * Plugin action links for Blitz Dock.
 *
 * @package BlitzDock
 */

namespace BlitzDock\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds a Settings link on the Plugins screen.
 */
class PluginLinks {

	/**
	 * Register hooks.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function register() : void {
		add_filter( 'plugin_action_links_' . plugin_basename( BLITZ_DOCK_FILE ), array( $this, 'add_links' ) );
	}

	/**
	 * Prepend a Settings link.
	 *
	 * @since 0.1.0
	 *
	 * @param array<int, string> $links Existing plugin links.
	 * @return array<int, string> Modified links.
	 */
	public function add_links( array $links ) : array {
		$url      = admin_url( 'admin.php?page=blitz-dock' );
		$settings = '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Settings', 'blitz-dock' ) . '</a>';

		array_unshift( $links, $settings );

		return $links;
	}
}