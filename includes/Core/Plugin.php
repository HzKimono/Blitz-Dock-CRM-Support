<?php
/**
 * Core plugin class.
 *
 * @package BlitzDock
 * @since 0.1.0
 * @license GPL-2.0-or-later
 */

namespace BlitzDock\Core;

use BlitzDock\Admin\MenuPage;
use BlitzDock\Admin\PluginLinks;
use BlitzDock\Public\Frontend as PublicFrontend;


if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Bootstrap the plugin.
 *
 * @since 0.1.0
 */
class Plugin {

    /**
     * Main plugin file.
     *
     * @since 0.1.0
     */
    public const FILE = BLITZ_DOCK_FILE;

    /**
     * Absolute plugin path.
     *
     * @since 0.1.0
     */
    public const PATH = BLITZ_DOCK_PATH;

    /**
     * Base plugin URL.
     *
     * @since 0.1.0
     */
    public const URL = BLITZ_DOCK_URL;

    /**
     * Current plugin version.
     *
     * @since 0.1.0
     */
     public const VERSION = BLITZ_DOCK_VERSION;

    /**
     * Plugin slug.
     *
     * @since 0.1.0
     */
    public const SLUG = 'blitz-dock';

    /**
     * Menu page handler.
     *
     * @since 0.1.0
     *
     * @var MenuPage
     */
    protected $menu_page;

    /**
     * Asset manager.
     *
     * @since 0.1.0
     *
     * @var Assets
     */
     protected $assets;

    /**
     * Plugin action links handler.
     *
     * @since 0.1.0
     *
     * @var PluginLinks Plugin action links handler.
     */
      protected $plugin_links;

    /**
     * Public frontend handler.
     *
     * @since 0.1.0
     *
     * @var PublicFrontend
     */
    protected $public;

    /**
     * Constructor.
     *
     * @since 0.1.0
     */
    public function __construct() {
       $this->menu_page    = new MenuPage();
       $this->assets       = new Assets();
       $this->plugin_links = new PluginLinks();
       $this->public       = new PublicFrontend();

       $this->register_hooks();
    }

    /**
     * Register WordPress hooks.
     *
     * @since 0.1.0
     *
     * @return void
     */
    protected function register_hooks() : void {
      \add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
      \add_action( 'admin_menu', array( $this->menu_page, 'register' ) );
      \add_action( 'admin_enqueue_scripts', array( $this->assets, 'enqueue_admin' ) );
        $this->plugin_links->register();
        $this->public->register();
    }

    /**
      * Load plugin text domain for translations.
     *
     * @since 0.1.0
     *
     * @return void
     */
    public function load_textdomain() : void {
         \load_plugin_textdomain( 'blitz-dock', false, \dirname( \plugin_basename( self::FILE ) ) . '/languages' );
    }
}
