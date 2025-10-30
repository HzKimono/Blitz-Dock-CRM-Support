<?php
/**
 * Public channels list.
 *
 * @package BlitzDock
 */

use BlitzDock\Channels\Providers;
use BlitzDock\Channels\Repository;
use BlitzDock\Frontend\Frontend;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$channels = Repository::get_active();

if ( empty( $channels ) ) {
	echo '<p class="screen-reader-text blitz-dock-channels__empty">' . esc_html__( 'No channels available.', 'blitz-dock' ) . '</p>';
	return;
}
?>
<ul class="blitz-dock-channels">
        <?php foreach ( $channels as $channel ) :
        $label = Providers::label( $channel['type'] );
        $url   = isset( $channel['url'] ) ? (string) $channel['url'] : '';

        if ( '' === $url ) {
                continue;
        }

        $escaped_url = esc_url( $url );

        if ( '' === $escaped_url ) {
                continue;
        }

        $slug = sanitize_key( $channel['type'] ?? '' );

        if ( '' === $slug ) {
                $slug = 'default';
        }

        $icon_url = Frontend::get_channel_icon_url( $slug );
        ?>
        <li class="blitz-dock-channels__item" data-provider="<?php echo esc_attr( $slug ); ?>">
                <a class="blitz-dock-channels__link" href="<?php echo esc_url( $escaped_url ); ?>" target="_blank" rel="noopener noreferrer">
                        <img class="blitz-dock-channels__icon" src="<?php echo $icon_url; ?>" alt="" width="24" height="24" decoding="async" loading="lazy" fetchpriority="low">
                        <span class="blitz-dock-channels__label"><?php echo esc_html( $label ); ?></span>
                </a>
        </li>
        <?php endforeach; ?>
</ul>