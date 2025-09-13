<?php
/**
 * Plugin action links.
 *
 * @package BlitzDock
 * @since 0.1.0
 * @license GPL-2.0-or-later
 */

namespace BlitzDock\Admin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Adds a Settings link on the Plugins screen.
 *
 * @since 0.1.0
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
        \add_filter( 'plugin_action_links_' . \plugin_basename( BLITZ_DOCK_FILE ), array( $this, 'add_links' ) );
    }

    /**
     * Prepend a Settings link.
     *
     * @since 0.1.0
     *
     * @param array $links Existing plugin action links.
     * @return array Modified links.
     */
    public function add_links( array $links ) : array {
        $url      = \admin_url( 'admin.php?page=blitz-dock' );
        $settings = '<a href="' . \esc_url( $url ) . '">' . \esc_html__( 'Settings', 'blitz-dock' ) . '</a>';
        \array_unshift( $links, $settings );
        return $links;
    }
}