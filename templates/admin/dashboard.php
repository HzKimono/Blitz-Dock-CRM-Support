<?php
/**
 * Admin dashboard template.
 *
 * @package BlitzDock
 * @since 0.1.0
 * @license GPL-2.0-or-later
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$base = menu_page_url( \BlitzDock\Core\Plugin::SLUG, false );
?>
<a class="screen-reader-text blitz-dock-skip-link" href="#blitz-dock-main"><?php esc_html_e( 'Skip to Blitz Dock content', 'blitz-dock' ); ?></a>
<div class="wrap blitz-dock-wrap">
    <div class="blitz-dock-app">
        <aside class="blitz-dock-sidebar" role="complementary" aria-label="<?php esc_attr_e( 'Blitz Dock sidebar', 'blitz-dock' ); ?>">
            <nav class="blitz-dock-nav" aria-label="<?php esc_attr_e( 'Blitz Dock navigation', 'blitz-dock' ); ?>">
                <ul>
                    <li>
                        <a href="<?php echo esc_url( add_query_arg( 'tab', 'dashboard', $base ) ); ?>" class="<?php echo esc_attr( 'dashboard' === $active_tab ? 'current' : '' ); ?>"<?php echo 'dashboard' === $active_tab ? ' aria-current="page"' : ''; ?>><?php esc_html_e( 'Dashboard', 'blitz-dock' ); ?></a>
                    </li>
                    <li>
                        <a href="<?php echo esc_url( add_query_arg( 'tab', 'channels', $base ) ); ?>" class="<?php echo esc_attr( 'channels' === $active_tab ? 'current' : '' ); ?>"<?php echo 'channels' === $active_tab ? ' aria-current="page"' : ''; ?>><?php esc_html_e( 'Channels', 'blitz-dock' ); ?></a>
                    </li>
                    <li>
                        <a href="<?php echo esc_url( add_query_arg( 'tab', 'analytics', $base ) ); ?>" class="<?php echo esc_attr( 'analytics' === $active_tab ? 'current' : '' ); ?>"<?php echo 'analytics' === $active_tab ? ' aria-current="page"' : ''; ?>><?php esc_html_e( 'Analytics', 'blitz-dock' ); ?></a>
                    </li>
                </ul>
            </nav>
        </aside>
        <main id="blitz-dock-main" class="blitz-dock-content" role="main" tabindex="-1">
            <h1><?php echo esc_html__( 'Blitz Dock', 'blitz-dock' ); ?></h1>
            <?php
            $section = \BlitzDock\Core\Plugin::PATH . 'templates/admin/sections/' . $active_tab . '.php';
            if ( is_readable( $section ) ) {
                include $section;
            } else {
                include \BlitzDock\Core\Plugin::PATH . 'templates/admin/sections/dashboard.php';
            }
            ?>
        </main>
    </div>
</div>