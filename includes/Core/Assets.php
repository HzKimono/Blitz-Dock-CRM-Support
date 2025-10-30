<?php
/**
 * Asset management utilities.
 *
 * @package BlitzDock
 * @since 0.1.0
 */

namespace BlitzDock\Core;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Handle admin and public asset loading.
 */
class Assets {

    /**
     * Admin base stylesheet handle.
     */
    public const HANDLE_ADMIN_STYLE = 'blitz-dock-admin';

    /**
     * Admin channels stylesheet handle.
     */
    public const HANDLE_ADMIN_CHANNELS_STYLE = 'blitz-dock-admin-channels';

    /**
     * Admin channels script handle.
     */
    public const HANDLE_ADMIN_CHANNELS_SCRIPT = 'blitz-dock-admin-channels-toggle';

    /**
     * Public stylesheet handle.
     */
    public const HANDLE_PUBLIC_STYLE = 'blitz-dock-public';

    /**
     * Public script handle.
     */
    public const HANDLE_PUBLIC_SCRIPT = 'blitz-dock-public-script';

    /**
     * Resolve metadata for an asset respecting SCRIPT_DEBUG and cache busting.
     *
     * @param string $relative_base Relative path to the asset without suffix or extension.
     * @param string $type          Asset type. Either `css` or `js`.
     *
     * @return array{file:string,path:string,url:string,version:string}
     */
    public static function get_asset_meta( string $relative_base, string $type ) : array {
        $kind   = ( 'js' === $type ) ? 'js' : 'css';
        $debug  = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;
        $suffix = $debug ? '' : '.min';

        $relative = sprintf( '%1$s%2$s.%3$s', $relative_base, $suffix, $kind );
        $path     = ltrim( $relative, '/' );
        $file     = Plugin::PATH . $path;

        if ( ! file_exists( $file ) && $suffix ) {
            $fallback = sprintf( '%1$s.%2$s', $relative_base, $kind );
            $path     = ltrim( $fallback, '/' );
            $file     = Plugin::PATH . $path;
        }

        $url      = Plugin::URL . $path;
        $version  = file_exists( $file ) ? (string) filemtime( $file ) : Plugin::VERSION;

        return array(
            'file'    => $file,
            'path'    => $path,
            'url'     => $url,
            'version' => $version,
        );
    }

    /**
     * Enqueue admin assets on Blitz Dock screens.
     *
     * @param string $hook_suffix Optional page hook suffix.
     *
     * @return void
     */
    public function enqueue_admin( string $hook_suffix = '' ) : void {
        $allowed = 'toplevel_page_' . Plugin::SLUG;

        if ( $hook_suffix && $hook_suffix !== $allowed ) {
            return;
        }

        $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

        if ( ! $screen || $screen->id !== $allowed ) {
            return;
        }

        $style = self::get_asset_meta( 'assets/css/admin', 'css' );

        wp_register_style(
            self::HANDLE_ADMIN_STYLE,
            $style['url'],
            array(),
            $style['version']
        );

        wp_enqueue_style( self::HANDLE_ADMIN_STYLE );

        $tab = '';

        if ( isset( $_GET['tab'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only inspection.
            $tab = sanitize_key( wp_unslash( $_GET['tab'] ) );
        }

        if ( 'channels' !== $tab ) {
            return;
        }

        $channels_style = self::get_asset_meta( 'assets/css/admin-channels', 'css' );

        wp_register_style(
            self::HANDLE_ADMIN_CHANNELS_STYLE,
            $channels_style['url'],
            array( self::HANDLE_ADMIN_STYLE ),
            $channels_style['version']
        );

        wp_enqueue_style( self::HANDLE_ADMIN_CHANNELS_STYLE );

        $channels_script = self::get_asset_meta( 'assets/js/admin-channels', 'js' );

        wp_register_script(
            self::HANDLE_ADMIN_CHANNELS_SCRIPT,
            $channels_script['url'],
            array(),
            $channels_script['version'],
            true
        );

        wp_enqueue_script( self::HANDLE_ADMIN_CHANNELS_SCRIPT );
    }

    /**
     * Register the public assets.
     *
     * @return void
     */
    public function register_public() : void {
        $style = self::get_asset_meta( 'assets/css/public', 'css' );

        wp_register_style(
            self::HANDLE_PUBLIC_STYLE,
            $style['url'],
            array(),
            $style['version']
        );

        $script = self::get_asset_meta( 'assets/js/public', 'js' );

        wp_register_script(
            self::HANDLE_PUBLIC_SCRIPT,
            $script['url'],
            array(),
            $script['version'],
            true
        );

        wp_script_add_data( self::HANDLE_PUBLIC_SCRIPT, 'strategy', 'defer' );

        wp_set_script_translations(
            self::HANDLE_PUBLIC_SCRIPT,
            'blitz-dock',
            plugin_dir_path( Plugin::FILE ) . 'languages'
        );
    }
}