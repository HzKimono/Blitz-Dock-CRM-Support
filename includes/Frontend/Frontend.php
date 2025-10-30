<?php
/**
 * Public facing functionality.
 *
 * @package BlitzDock
 * @since 0.1.0
 */

namespace BlitzDock\Frontend;

use BlitzDock\Core\Assets;
use BlitzDock\Core\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Handle frontend rendering and assets.
 */
class Frontend {

    /**
     * Asset manager instance.
     *
     * @var Assets
     */
    protected Assets $assets;

    /**
     * Flag to determine if assets and template should load.
     */
    private bool $should_enqueue = false;

    /**
     * Constructor.
     *
     * @param Assets $assets Asset manager instance.
     */
    public function __construct( Assets $assets ) {
        $this->assets = $assets;
    }

    /**
     * Register hooks.
     *
     * @return void
     */
    public function register() : void {
        add_action( 'wp', array( $this, 'boot' ) );
        add_action( 'wp_footer', array( $this, 'render' ), 100 );
    }

    /**
     * Prepare enqueue logic once WordPress query variables are available.
     *
     * @return void
     */
    public function boot() : void {
        if ( ! $this->should_render() ) {
            return;
        }

        $this->should_enqueue = true;

        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ), 20 );
    }

    /**
     * Determine if public assets should render.
     *
     * @return bool
     */
    public function should_render() : bool {
        return ! is_admin()
            && ! is_feed()
            && ! wp_doing_ajax()
            && (bool) apply_filters( 'blitz_dock_public_enabled', true );
    }

    /**
     * Enqueue public assets once per page load.
     *
     * @return void
     */
    public function enqueue() : void {
        if ( ! $this->should_enqueue ) {
            return;
        }

        $this->assets->register_public();

        wp_enqueue_style( Assets::HANDLE_PUBLIC_STYLE );
        wp_enqueue_script( Assets::HANDLE_PUBLIC_SCRIPT );
    }

    /**
     * Render the frontend template.
     *
     * @return void
     */
    public function render() : void {
        if ( ! $this->should_enqueue ) {
            return;
        }

        require Plugin::PATH . 'templates/public/panel.php';
    }

    /**
     * Retrieve the icon URL for a given channel provider slug.
     *
     * @param string $slug Channel provider slug.
     *
     * @return string
     */
    public static function get_channel_icon_url( string $slug ) : string {
        $slug = sanitize_key( $slug );

        if ( '' === $slug ) {
            $slug = 'default';
        }

        static $cache = array();

        if ( ! isset( $cache[ $slug ] ) ) {
            $path           = trailingslashit( Plugin::PATH ) . "assets/icons/channels/{$slug}.png";
            $cache[ $slug ] = file_exists( $path ) ? $slug : 'default';
        }

        $resolved_slug = $cache[ $slug ];
        $url           = trailingslashit( Plugin::URL ) . "assets/icons/channels/{$resolved_slug}.png";

        return esc_url( apply_filters( 'blitz_dock_channel_icon_url', $url, $resolved_slug ) );
    }
}