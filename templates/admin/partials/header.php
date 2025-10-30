<?php
/**
 * Admin header partial.
 *
 * @package BlitzDock
 * @since 0.1.0
 * @license GPL-2.0-or-later
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$current_tab_label = \BlitzDock\Admin\MenuPage::get_current_tab_label();
$root_url          = \BlitzDock\Admin\MenuPage::plugin_root_url();
?>
<header class="blitz-dock-header" role="region" aria-label="<?php echo esc_attr__( 'Page header', 'blitz-dock' ); ?>">
    <div class="blitz-dock-header__inner">
        <nav class="blitz-dock-breadcrumb" aria-label="<?php echo esc_attr__( 'Breadcrumb', 'blitz-dock' ); ?>">
            <ol class="blitz-dock-breadcrumb__list">
                <li class="blitz-dock-breadcrumb__item">
                    <a class="blitz-dock-breadcrumb__link" href="<?php echo esc_url( $root_url ); ?>"><?php echo esc_html__( 'Blitz Dock', 'blitz-dock' ); ?></a>
                </li>
                <li class="blitz-dock-breadcrumb__item blitz-dock-breadcrumb__item--current" aria-current="page">
                    <?php echo esc_html( $current_tab_label ); ?>
               </li>
            </ol>
        </nav>
        <h1 class="screen-reader-text"><?php echo esc_html( $current_tab_label ); ?></h1>
        <span class="blitz-dock-version">
            <?php
            /* translators: %s: plugin version number, e.g. 1.2 */
            printf( esc_html__( 'Version %s', 'blitz-dock' ), esc_html( \BlitzDock\Core\Plugin::VERSION ) );
            ?>
        </span>
    </div>
</header>