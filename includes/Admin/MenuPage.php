<?php
/**
 * Admin menu page handler.
 *
 * @package BlitzDock
 * @since 0.1.0
 * @license GPL-2.0-or-later
 */

namespace BlitzDock\Admin;

use BlitzDock\Core\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Handles the Blitz Dock admin page.
 *
 * @since 0.1.0
 */
 
class MenuPage {
    
    /**
     * Hook suffix for the admin page.
     *
     * @since 0.1.0
     *
     * @var string
     */
    protected string $hook_suffix = '';

    /**
     * Get the page slug.
     *
     * @since 0.1.0
     *
     * @return string
     */
     
    public function get_slug() : string {
        return Plugin::SLUG;
    }

    /**
     * Register the top-level menu page.
     *
     * @since 0.1.0
     *
     * @return void
     */
     
    public function register() : void {
        $this->hook_suffix = \add_menu_page(
            \__( 'Blitz Dock', 'blitz-dock' ),
            \__( 'Blitz Dock', 'blitz-dock' ),
            'manage_options',
            Plugin::SLUG,
            array( $this, 'render_page' ),
            'dashicons-admin-generic'
        );
    }

   /**
     * Render the admin screen.
     *
     * @since 0.1.0
     *
     * @return void
     */
    public function render_page() : void {
        if ( ! \current_user_can( 'manage_options' ) ) {
            \wp_die( \esc_html__( 'You are not allowed to access this page.', 'blitz-dock' ) );
        }

        $active_tab = $this->get_active_tab();
        $allowed    = $this->get_allowed_tabs();

        \printf(
            '<a class="screen-reader-text blitz-dock-skip-link" href="#blitz-dock-main">%s</a>',
            \esc_html__( 'Skip to Blitz Dock content', 'blitz-dock' )
        );

        echo '<div class="wrap blitz-dock-wrap">';

        echo '<aside class="blitz-dock-sidebar" role="complementary" aria-label="' . \esc_attr__( 'Blitz Dock sidebar', 'blitz-dock' ) . '">';
        \load_template(
            Plugin::PATH . 'templates/admin/partials/sidebar.php',
            true,
            array( 'active_tab' => $active_tab )
        );
        echo '</aside>';

        echo '<main id="blitz-dock-main" class="blitz-dock-content" role="main" tabindex="-1">';
        echo '<h1>' . \esc_html__( 'Blitz Dock', 'blitz-dock' ) . '</h1>';

        $section = Plugin::PATH . 'templates/admin/sections/' . $active_tab . '.php';
        if ( \in_array( $active_tab, $allowed, true ) && \is_readable( $section ) ) {
            \load_template( $section, true, array( 'active_tab' => $active_tab ) );
        } else {
            \load_template( Plugin::PATH . 'templates/admin/sections/dashboard.php', true, array( 'active_tab' => $active_tab ) );
        }

        echo '</main>';
        echo '</div>';
    }

    /**
     * Determine the active tab from the request.
     *
     * @since 0.1.0
     *
     * @return string Active tab slug.
     */
    private function get_active_tab() : string {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only tab switch.
        $tab     = \sanitize_key( \wp_unslash( $_GET['tab'] ?? '' ) );
        $allowed = $this->get_allowed_tabs();

        if ( \in_array( $tab, $allowed, true ) ) {
            return $tab;
        }

        return 'dashboard';
    }

    /**
     * Get the list of allowed tab slugs.
     *
     * @since 0.1.0
     *
     * @return array<string> Whitelisted tab slugs.
     */
    private function get_allowed_tabs() : array {
        return \apply_filters( 'blitz_dock_allowed_tabs', array( 'dashboard', 'channels', 'analytics' ) );
    }

    /**
     * Get the page hook suffix.
     *
     * @since 0.1.0
     *
     * @return string
     */
     public function get_hook_suffix() : string {
        return $this->hook_suffix;
    }
}