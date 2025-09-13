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

$tabs = array(
    'dashboard' => __( 'Dashboard', 'blitz-dock' ),
    'channels'  => __( 'Channels', 'blitz-dock' ),
    'analytics' => __( 'Analytics', 'blitz-dock' ),
);
?>
<nav class="blitz-dock-nav" aria-label="<?php esc_attr_e( 'Blitz Dock navigation', 'blitz-dock' ); ?>">
    <ul>
        <?php foreach ( $tabs as $slug => $label ) :
            $url    = admin_url( 'admin.php?page=' . \BlitzDock\Core\Plugin::SLUG . '&tab=' . $slug );
            $active = ( $slug === $active_tab );
            ?>
             <li>
                <a href="<?php echo esc_url( $url ); ?>"<?php echo $active ? ' class="active" aria-current="page"' : ''; ?>>
                    <?php
                    $icon_svg = '';
                    if ( 'dashboard' === $slug ) {
                        $icon_path = \BlitzDock\Core\Plugin::PATH . 'assets/icons/sidebar/blitz-dock-dashboard.svg';

                        if ( is_readable( $icon_path ) ) {
                            $raw_svg = file_get_contents( $icon_path );

                            if ( false !== $raw_svg && class_exists( '\\DOMDocument' ) ) {
                                $dom       = new \DOMDocument();
                                $old_error = libxml_use_internal_errors( true );

                                if ( $dom->loadXML( $raw_svg, LIBXML_NONET ) ) {
                                    $svg = $dom->documentElement;

                                    $svg->setAttribute( 'class', 'blitz-dock-icon' );
                                    $svg->setAttribute( 'aria-hidden', 'true' );
                                    $svg->setAttribute( 'focusable', 'false' );
                                    $svg->setAttribute( 'fill', 'currentColor' );
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
                                                } elseif ( 'fill' === $name && $node !== $svg ) {
                                                    $node->removeAttribute( $name );
                                                } elseif ( 'style' === $name ) {
                                                    $clean = preg_replace( '/fill\s*:\s*[^;]+;?/i', '', $value );
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

                                    $icon_svg = $dom->saveXML( $svg );
                                }

                                libxml_clear_errors();
                                libxml_use_internal_errors( $old_error );
                            }
                        }
                    }

                    if ( $icon_svg ) {
                        echo $icon_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static trusted SVG.
                    }

                    echo esc_html( $label );
                    ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>