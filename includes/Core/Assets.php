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
     * @return void
     */
    public function enqueue_admin() : void {
        $screen = \get_current_screen();

        if ( ! $screen || 'toplevel_page_' . Plugin::SLUG !== $screen->id ) {
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