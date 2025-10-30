<?php
/**
 * Core plugin bootstrap.
 *
 * @package BlitzDock
 * @since 0.1.0
 */

namespace BlitzDock\Core;

use BlitzDock\Admin\MenuPage;
use BlitzDock\Admin\PluginLinks;
use BlitzDock\Channels\Controller as ChannelsController;
use BlitzDock\Channels\PostType as ChannelsPostType;
use BlitzDock\Frontend\Frontend as PublicFrontend;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Bootstrap the plugin.
 */
class Plugin {

    /**
     * Main plugin file path.
     */
    public const FILE = BLITZ_DOCK_FILE;

    /**
     * Absolute plugin directory path with trailing slash.
     */
    public const PATH = BLITZ_DOCK_PATH;

    /**
     * Base plugin URL with trailing slash.
     */
    public const URL = BLITZ_DOCK_URL;

    /**
     * Current plugin version.
     */
    public const VERSION = BLITZ_DOCK_VERSION;

    /**
     * Plugin slug used for admin pages and asset handles.
     */
    public const SLUG = 'blitz-dock';

    /**
     * Menu page handler instance.
     *
     * @var MenuPage
     */
    protected MenuPage $menu_page;

    /**
     * Asset manager instance.
     *
     * @var Assets
     */
    protected Assets $assets;

    /**
     * Plugin action links handler.
     *
     * @var PluginLinks
     */
    protected PluginLinks $plugin_links;

    /**
     * Public frontend handler.
     *
     * @var PublicFrontend
     */
    protected PublicFrontend $public;

    /**
     * Channels post type handler.
     *
     * @var ChannelsPostType
     */
    protected ChannelsPostType $channels_post_type;

    /**
     * Channels controller handler.
     *
     * @var ChannelsController
     */
    protected ChannelsController $channels_controller;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->menu_page           = new MenuPage();
        $this->assets              = new Assets();
        $this->plugin_links        = new PluginLinks();
        $this->public              = new PublicFrontend( $this->assets );
        $this->channels_post_type  = new ChannelsPostType();
        $this->channels_controller = new ChannelsController();

        $this->register_hooks();
    }

    /**
     * Register WordPress hooks.
     *
     * @return void
     */
    protected function register_hooks() : void {
        add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
        add_action( 'admin_menu', array( $this->menu_page, 'register' ) );
        add_action( 'admin_enqueue_scripts', array( $this->assets, 'enqueue_admin' ) );

        $this->plugin_links->register();
        $this->public->register();
        $this->channels_post_type->register();
        $this->channels_controller->register();
    }

    /**
     * Load plugin text domain for translations.
     *
     * @return void
     */
    public function load_textdomain() : void {
        load_plugin_textdomain( 'blitz-dock', false, dirname( plugin_basename( self::FILE ) ) . '/languages' );
    }
}