<?php
/**
 * Public panel home view template.
 *
 * @package BlitzDock
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$started_iso = isset( $started_iso ) ? $started_iso : '';
$agent_name  = isset( $agent_name ) ? $agent_name : '';
?>
<section class="blitz-dock__home"
         data-bd-view="home"
         aria-hidden="false"
         aria-labelledby="blitz-dock-title">
    <section
        class="blitz-dock__conversation-start"
        id="blitz-dock-convo-start"
        aria-hidden="true"
        data-started-at="<?php echo esc_attr( $started_iso ); ?>"
        data-agent="<?php echo esc_attr( $agent_name ? $agent_name : 'Blitz Dock' ); ?>">
        <div class="blitz-dock__conversation-meta">
            <div class="blitz-dock__conversation-title">
                <time class="blitz-dock__started-time" datetime=""></time>
                <span class="blitz-dock__started-date"></span>
            </div>
            <p class="blitz-dock__conversation-desc"></p>
        </div>
    </section>

   <button class="blitz-dock__button blitz-dock__nav-button"
            data-bd-target="channels"
            aria-controls="bd-channels"
            aria-expanded="false">
        <?php esc_html_e( 'Channels', 'blitz-dock' ); ?>
    </button>
</section>