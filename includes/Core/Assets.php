<?php
/**
 * Asset management.
 *
 * @package BlitzDock
 * @since 0.1.0
 * @license GPL-2.0-or-later
 */
namespace BlitzDock\Core;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
  * Enqueue admin assets conditionally.
 *
 * @since 0.1.0
 */
class Assets {

    /**
     * Style handle.
     *
     * @since 0.1.0
     */
    public const STYLE_HANDLE = 'blitz-dock-admin';

    /**
   * Enqueue admin styles.
     *
     * @since 0.1.0
     *
     * @param string $hook_suffix Optional page hook suffix.
     *
     * @return void
     */
    public function enqueue_admin( string $hook_suffix = '' ) : void {
        $allowed = 'toplevel_page_' . Plugin::SLUG;
        $screen  = \function_exists( 'get_current_screen' ) ? \get_current_screen() : null;

        if ( ( $hook_suffix && $allowed !== $hook_suffix ) || ! $screen || $allowed !== $screen->id ) {
            return;
        }

        $file    = Plugin::PATH . 'assets/css/admin.css';
        $version = \file_exists( $file ) ? (string) \filemtime( $file ) : Plugin::VERSION;

        \wp_enqueue_style(
            self::STYLE_HANDLE,
            Plugin::URL . 'assets/css/admin.css',
            array(),
            $version
        );
    }
}