<?php
/**
 * Admin sidebar template.
 *
 * @package BlitzDock
 * @since 0.3.0
 * @license GPL-2.0-or-later
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$tabs = \BlitzDock\Admin\MenuPage::get_tabs_map();

$active_tab = isset( $active_tab ) ? $active_tab : \BlitzDock\Admin\MenuPage::get_current_tab_slug();

$base_url = menu_page_url( \BlitzDock\Core\Plugin::SLUG, false );

if ( empty( $base_url ) ) {
    $base_url = add_query_arg(
        array(
            'page' => \BlitzDock\Core\Plugin::SLUG,
        ),
        admin_url( 'admin.php' )
    );
}
?>
<nav class="blitz-dock-nav" aria-label="<?php echo esc_attr( __( 'Blitz Dock navigation', 'blitz-dock' ) ); ?>">
    <ul>
        <?php foreach ( $tabs as $slug => $config ) :
            $capability = isset( $config['cap'] ) ? $config['cap'] : 'manage_options';

            if ( ! current_user_can( $capability ) ) {
                continue;
            }

            $label = isset( $config['label'] ) ? $config['label'] : '';
            $url   = add_query_arg( 'tab', $slug, $base_url );
            $active = ( $slug === $active_tab );

            $classes = array( 'blitz-dock__nav-link' );
            if ( $active ) {
                $classes[] = 'is-active';
            }

            $icon_file = isset( $config['icon'] ) ? $config['icon'] : 'blitz-dock-dashboard.svg';
            $icon_url  = plugins_url( 'assets/icons/sidebar/' . $icon_file, BLITZ_DOCK_FILE );
            ?>
          <li class="blitz-dock__nav-item">
                <a href="<?php echo esc_url( $url ); ?>" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"<?php if ( $active ) : ?> aria-current="<?php echo esc_attr( 'page' ); ?>"<?php endif; ?>>
                    <span class="blitz-dock-icon blitz-dock__icon">
                        <img src="<?php echo esc_url( $icon_url ); ?>" width="18" height="18" alt="" aria-hidden="true" loading="lazy" decoding="async" />
                    </span>
                    <span class="blitz-dock__label"><?php echo esc_html( $label ); ?></span>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>