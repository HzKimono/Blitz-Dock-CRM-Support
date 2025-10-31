<?php
/**
 * Admin menu page handler.
 *
 * @package BlitzDock
 */

namespace BlitzDock\Admin;

use BlitzDock\Core\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles the Blitz Dock admin screen.
 */
class MenuPage {

	/**
	 * Default admin tab slug.
	 *
	 * @since 0.5.0
	 */
    protected const DEFAULT_TAB = 'dashboard';

	/**
	 * Retrieve the registered admin tabs configuration.
	 *
	 * @since 0.5.0
	 *
	 * @return array<string, array<string, string>> Tab configuration map.
	 */
	public static function get_tabs_map() : array {
		return array(
			'dashboard' => array(
				'label' => __( 'Dashboard', 'blitz-dock' ),
				'icon'  => 'blitz-dock-dashboard.svg',
				'cap'   => 'manage_options',
			),
			'channels'  => array(
				'label' => __( 'Channels', 'blitz-dock' ),
				'icon'  => 'blitz-dock-channels.svg',
				'cap'   => 'manage_options',
			),
			'analytics' => array(
				'label' => __( 'Analytics', 'blitz-dock' ),
				'icon'  => 'blitz-dock-analytics.svg',
				'cap'   => 'manage_options',
			),
			'faq'       => array(
				'label' => __( 'Frequently Asked Questions', 'blitz-dock' ),
				'icon'  => 'blitz-dock-faq.svg',
				'cap'   => 'manage_options',
			),
		);
	}


	/**
	 * Stored hook suffix for the admin page.
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	protected string $hook_suffix = '';

	/**
	 * Retrieve the current tab slug.
	 *
	 * @since 0.1.0
	 *
	 * @return string Tab slug.
	 */
	public static function get_current_tab_slug() : string {
		$allowed_tabs = self::get_accessible_tab_slugs();
		$default      = in_array( self::DEFAULT_TAB, $allowed_tabs, true ) ? self::DEFAULT_TAB : ( $allowed_tabs[0] ?? self::DEFAULT_TAB );
		$tab          = $default;

		if ( isset( $_GET['tab'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only context.
			$tab = sanitize_key( wp_unslash( $_GET['tab'] ) );
		}

		if ( in_array( $tab, $allowed_tabs, true ) ) {
			return $tab;
		}

		return $default;
	}

	/**
	 * Retrieve the available tab labels.
	 *
	 * @since 0.1.0
	 *
	 * @return array<string, string> Tab => label map.
	 */
	public static function get_tab_labels() : array {
		$labels = array();

		foreach ( self::get_tabs_map() as $slug => $config ) {
			if ( isset( $config['label'] ) ) {
				$labels[ $slug ] = $config['label'];
		}
		}

		return $labels;
	}

	/**
	 * Retrieve the current tab label.
	 *
	 * @since 0.1.0
	 *
	 * @return string Tab label.
	 */
	public static function get_current_tab_label() : string {
		$labels  = self::get_tab_labels();
		$current = self::get_current_tab_slug();

		if ( isset( $labels[ $current ] ) ) {
			return $labels[ $current ];
		}

		return $labels['dashboard'];
	}

	/**
	 * Retrieve the plugin root URL.
	 *
	 * @since 0.1.0
	 *
	 * @return string URL to the Blitz Dock admin page.
	 */
	public static function plugin_root_url() : string {
		return menu_page_url( Plugin::SLUG, false );
	}

	/**
	 * Get the menu slug.
	 *
	 * @since 0.1.0
	 *
	 * @return string Menu slug.
	 */
	public function get_slug() : string {
		return Plugin::SLUG;
	}

	/**
	 * Register the top-level admin menu page.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function register() : void {
		$this->hook_suffix = add_menu_page(
			__( 'Blitz Dock', 'blitz-dock' ),
			__( 'Blitz Dock', 'blitz-dock' ),
			'manage_options',
			Plugin::SLUG,
			array( $this, 'render_page' ),
			'dashicons-admin-generic'
		);
	}

	/**
	 * Render the admin page contents.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function render_page() : void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You are not allowed to access this page.', 'blitz-dock' ) );
		}

		$active_tab = self::get_current_tab_slug();
		$allowed    = $this->get_allowed_tabs();

		printf(
			'<a class="screen-reader-text blitz-dock-skip-link" href="#blitz-dock-main">%s</a>',
			esc_html__( 'Skip to Blitz Dock content', 'blitz-dock' )
		);

		echo '<div class="wrap blitz-dock-wrap">';

		echo '<aside class="blitz-dock-sidebar" role="complementary" aria-label="' . esc_attr__( 'Blitz Dock sidebar', 'blitz-dock' ) . '">';
		load_template(
			Plugin::PATH . 'templates/admin/partials/sidebar.php',
			true,
			array( 'active_tab' => $active_tab )
		);
		echo '</aside>';

		echo '<main id="blitz-dock-main" class="blitz-dock-content" role="main" tabindex="-1">';

		$section = Plugin::PATH . 'templates/admin/sections/' . $active_tab . '.php';

		if ( 'faq' === $active_tab ) {
			$section = Plugin::PATH . 'templates/admin/faq.php';
		}

		if ( in_array( $active_tab, $allowed, true ) && is_readable( $section ) ) {
			load_template( $section, true, array( 'active_tab' => $active_tab ) );
		} else {
			load_template( Plugin::PATH . 'templates/admin/sections/dashboard.php', true, array( 'active_tab' => $active_tab ) );
		}

		echo '</main>';
		echo '</div>';
	}

	/**
	 * Retrieve the list of allowed tab slugs.
	 *
	 * @since 0.1.0
	 *
	 * @return array<int, string> Allowed tabs.
	 */
	private function get_allowed_tabs() : array {
		return self::get_accessible_tab_slugs();
	}

	/**
	 * Retrieve accessible tab slugs for the current user.
	 *
	 * @since 0.5.0
	 *
	 * @return array<int, string> Accessible tab slugs.
	 */
	protected static function get_accessible_tab_slugs() : array {
		$allowed = array();

		foreach ( self::get_tabs_map() as $slug => $config ) {
			$capability = isset( $config['cap'] ) ? $config['cap'] : 'manage_options';

			if ( current_user_can( $capability ) ) {
				$allowed[] = $slug;
			}
		}

		if ( empty( $allowed ) ) {
			$allowed[] = self::DEFAULT_TAB;
		}

		return array_values( array_unique( $allowed ) );
	}


	/**
	 * Retrieve the registered hook suffix.
	 *
	 * @since 0.1.0
	 *
	 * @return string Hook suffix.
	 */
	public function get_hook_suffix() : string {
		return $this->hook_suffix;
	}
}