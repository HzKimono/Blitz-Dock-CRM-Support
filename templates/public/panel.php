<?php
/**
 * Public panel template.
 *
 * @package BlitzDock
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="blitz-dock blitz-dock--hidden" id="blitz-dock-root" data-bd-root aria-live="polite">
    <?php
    /**
     * Launcher bubble trigger.
     *
     * @since 0.1.0
     */
    ?>
    <button class="blitz-dock__bubble"
            type="button"
            aria-controls="blitz-dock__panel"
            aria-expanded="false"
            aria-label="<?php echo esc_attr__( 'Open support panel', 'blitz-dock' ); ?>"
            data-bd-bubble>
        <span class="blitz-dock__bubble-icon" aria-hidden="true"></span>
    </button>

    <?php
    /**
     * Modal overlay element.
     *
     * @since 0.1.0
     */
    ?>
    <div class="blitz-dock__overlay" data-bd-overlay hidden></div>

    <?php
    /**
     * Support panel dialog container.
     *
     * @since 0.1.0
     */
    ?>
  <section id="blitz-dock__panel"
            class="blitz-dock__panel"
            role="dialog"
            aria-modal="true"
            aria-labelledby="blitz-dock-title"
            aria-describedby="blitz-dock-desc"
            data-bd-panel
            hidden>
        <p id="blitz-dock-desc" class="blitz-dock-visually-hidden">
            <?php echo esc_html__( 'Customer support panel. Use Tab to navigate. Press Escape to close.', 'blitz-dock' ); ?>
        </p>
        <header class="blitz-dock__header">
            <h2 id="blitz-dock-title" class="blitz-dock__title">
                <?php echo esc_html__( 'Blitz Dock CRM Support', 'blitz-dock' ); ?>
            </h2>
            <button class="blitz-dock__close"
                    type="button"
                    data-close-label-home="<?php echo esc_attr__( 'Close panel', 'blitz-dock' ); ?>"
                    data-close-label-back="<?php echo esc_attr__( 'Back to home', 'blitz-dock' ); ?>"
                    aria-label="<?php echo esc_attr__( 'Close panel', 'blitz-dock' ); ?>"
                    data-bd-close>
                <span class="blitz-dock__close-icon" aria-hidden="true"></span>
            </button>
        </header>

        <div class="blitz-dock__subhead"
             role="region"
             aria-label="<?php echo esc_attr__( 'secondary header', 'blitz-dock' ); ?>">
            <div class="bd-subhead">
                <p class="bd-subhead__time">
                    <?php
                    $label = __( 'Local Time:', 'blitz-dock' );
                    $dt_iso = wp_date( 'Y-m-d' );
                    $dt_vis = wp_date( 'd.m.Y' );
                    printf(
                        '%s <time datetime="%s">%s</time>',
                        esc_html( $label ),
                        esc_attr( $dt_iso ),
                        esc_html( $dt_vis )
                    );
                    ?>
                </p>
                <p class="bd-subhead__note">
                    <?php echo esc_html__( 'Welcome to the Assistant Blitz Dock Panel.', 'blitz-dock' ); ?>
                </p>
            </div>
        </div>

        <div class="blitz-dock__body" tabindex="-1">
            <?php
            /**
             * Home view containing the primary actions.
             *
             * @since 0.1.0
             */
            ?>
               <?php require __DIR__ . '/panel-home.php'; ?>

            <?php
            /**
             * Channels view with available contact options.
             *
             * @since 0.1.0
             */
            ?>
        <section id="bd-channels"
                     class="blitz-dock__channels"
                     data-bd-view="channels"
                     hidden
                     aria-hidden="true"
                     aria-labelledby="blitz-dock-channels-title">
                <header class="blitz-dock__channels-header">
                    <h3 id="blitz-dock-channels-title" class="blitz-dock__channels-title">
                        <?php esc_html_e( 'Channels', 'blitz-dock' ); ?>
                    </h3>
                </header>
                <?php require __DIR__ . '/channels/list.php'; ?>
            </section>
        </div>
    </section>
</div>