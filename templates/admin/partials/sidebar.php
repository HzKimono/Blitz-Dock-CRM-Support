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

$tabs = \BlitzDock\Admin\MenuPage::get_tab_labels();

$active_tab = isset( $active_tab ) ? $active_tab : \BlitzDock\Admin\MenuPage::get_current_tab_slug();

$allowed_svg_tags = array(
    'span'   => array(
        'class' => true,
    ),
    'svg'    => array(
        'xmlns'       => true,
        'viewbox'     => true,
        'aria-hidden' => true,
        'focusable'   => true,
        'class'       => true,
        'role'        => true,
    ),
    'path'   => array(
        'd' => true,
    ),
    'polygon' => array(
        'points' => true,
    ),
    'circle' => array(
        'cx' => true,
        'cy' => true,
        'r'  => true,
    ),
);
?>
<nav class="blitz-dock-nav" aria-label="<?php echo esc_attr( __( 'Blitz Dock navigation', 'blitz-dock' ) ); ?>">
    <ul>
        <?php foreach ( $tabs as $slug => $label ) :
            $url    = add_query_arg(
                array(
                    'page' => \BlitzDock\Core\Plugin::SLUG,
                    'tab'  => $slug,
                ),
                admin_url( 'admin.php' )
            );
            $active = ( $slug === $active_tab );

            $classes = array( 'blitz-dock__nav-link' );
            if ( $active ) {
                $classes[] = 'is-active';
            }

            $icon_svg  = '';
            $icons_map = array(
                'dashboard' => 'blitz-dock-dashboard.svg',
                'channels'  => 'blitz-dock-channels.svg',
                'analytics' => 'blitz-dock-analytics.svg',
            );
            $icon_file = isset( $icons_map[ $slug ] ) ? $icons_map[ $slug ] : 'blitz-dock-dashboard.svg';
            $icon_path = \BlitzDock\Core\Plugin::PATH . 'assets/icons/sidebar/' . $icon_file;

            if ( ! is_readable( $icon_path ) ) {
                $icon_path = \BlitzDock\Core\Plugin::PATH . 'assets/icons/sidebar/blitz-dock-dashboard.svg';
            }

            if ( is_readable( $icon_path ) ) {
                $raw_svg = file_get_contents( $icon_path );

                if ( false !== $raw_svg && class_exists( '\\DOMDocument' ) ) {
                    $dom       = new \DOMDocument();
                    $old_error = libxml_use_internal_errors( true );

                    if ( $dom->loadXML( $raw_svg, LIBXML_NONET ) ) {
                        $svg = $dom->documentElement;

                        $svg->setAttribute( 'aria-hidden', 'true' );
                        $svg->setAttribute( 'focusable', 'false' );
                        $svg->removeAttribute( 'width' );
                        $svg->removeAttribute( 'height' );

                        $nodes     = $svg->getElementsByTagName( '*' );
                        $node_list = iterator_to_array( $nodes );
                        $node_list[] = $svg;

                        foreach ( $node_list as $node ) {
                            if ( $node->hasAttributes() ) {
                                foreach ( iterator_to_array( $node->attributes ) as $attr ) {
                                    $name  = $attr->nodeName;
                                    $value = $attr->nodeValue;

                                    if ( 0 === strpos( $name, 'on' ) ) {
                                        $node->removeAttribute( $name );
                                    } elseif ( in_array( $name, array( 'href', 'xlink:href' ), true ) ) {
                                        $node->removeAttribute( $name );
                                    } elseif ( in_array( $name, array( 'fill', 'stroke', 'stroke-width' ), true ) ) {
                                        $node->removeAttribute( $name );
                                    } elseif ( 'style' === $name ) {
                                        $clean = preg_replace( '/(fill|stroke|stroke-width)\s*:\s*[^;]+;?/i', '', $value );
                                        $clean = trim( $clean );

                                        if ( '' === $clean ) {
                                            $node->removeAttribute( 'style' );
                                        } else {
                                            $node->setAttribute( 'style', $clean );
                                        }
                                    }
                                }
                            }
                        }

                        $icon_svg = '<span class="blitz-dock-icon blitz-dock__icon">' . $dom->saveXML( $svg ) . '</span>';
                    }

                    libxml_clear_errors();
                    libxml_use_internal_errors( $old_error );
                }
            }
            ?>
          <li class="blitz-dock__nav-item">
                <a href="<?php echo esc_url( $url ); ?>" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"<?php if ( $active ) : ?> aria-current="<?php echo esc_attr( 'page' ); ?>"<?php endif; ?>>
                    <?php
                    if ( $icon_svg ) {
                        echo wp_kses( $icon_svg, $allowed_svg_tags );
                    }
                    ?>
                    <span class="blitz-dock__label"><?php echo esc_html( $label ); ?></span>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>