<?php
/**
 * Public facing functionality.
 *
 * @package BlitzDock
 * @since 0.1.0
 * @license GPL-2.0-or-later
 */

namespace BlitzDock\Frontend;
use BlitzDock\Core\Plugin;
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Handle frontend rendering and assets.
 *
 * @since 0.1.0
 */
class Frontend {

    /**
     * Register hooks.
     *
     * @since 0.1.0
     *
     * @return void
     */
    public function register() : void {
        \add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
        \add_action( 'wp_footer', array( $this, 'render' ), 100 );
    }

    /**
     * Determine if public assets should render.
     *
     * @since 0.1.0
     *
     * @return bool
     */
    public function should_render() : bool {
        return ! \is_admin()
            && ! \is_feed()
            && ! \wp_doing_ajax()
            && (bool) \apply_filters( 'blitz_dock_public_enabled', true );
    }

    /**
     * Enqueue public assets.
     *
     * @since 0.1.0
     *
     * @return void
     */
    public function enqueue() : void {
        if ( ! $this->should_render() ) {
            return;
        }

        $css_file = \plugin_dir_path( BLITZ_DOCK_FILE ) . 'assets/css/public.css';
        $js_file  = \plugin_dir_path( BLITZ_DOCK_FILE ) . 'assets/js/public.js';

        $css_ver = \file_exists( $css_file ) ? (string) \filemtime( $css_file ) : Plugin::VERSION;
        $js_ver  = \file_exists( $js_file ) ? (string) \filemtime( $js_file ) : Plugin::VERSION;

        \wp_enqueue_style(
            'blitz-dock-public',
            \plugin_dir_url( BLITZ_DOCK_FILE ) . 'assets/css/public.css',
            array(),
            $css_ver
        );

        \wp_enqueue_script(
            'blitz-dock-public',
            \plugin_dir_url( BLITZ_DOCK_FILE ) . 'assets/js/public.js',
            array(),
            $js_ver,
            true
        );

        \wp_script_add_data( 'blitz-dock-public', 'strategy', 'defer' );

        \wp_set_script_translations(
            'blitz-dock-public',
            'blitz-dock',
            \plugin_dir_path( BLITZ_DOCK_FILE ) . 'languages'
        );
    }

    /**
     * Render the frontend template.
     *
     * @since 0.1.0
     *
     * @return void
     */
    public function render() : void {
        if ( ! $this->should_render() ) {
            return;
        }

        require \plugin_dir_path( BLITZ_DOCK_FILE ) . 'templates/public/panel.php';
    }
}