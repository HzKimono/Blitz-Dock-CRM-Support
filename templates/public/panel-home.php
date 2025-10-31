<?php
/**
 * Public panel home view template.
 *
 * @package BlitzDock
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<section class="blitz-dock__home"
         data-bd-view="home"
         aria-hidden="false"
         aria-labelledby="blitz-dock-title">
    <button class="blitz-dock__button blitz-dock__nav-button"
             data-bd-target="channels"
             aria-controls="bd-channels"
             aria-expanded="false">
        <?php esc_html_e( 'Channels', 'blitz-dock' ); ?>
    </button>
</section>