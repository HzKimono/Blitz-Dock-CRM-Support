<?php
/**
 * Public panel template.
 *
 * @package BlitzDock
 * @since 0.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="blitz-dock blitz-dock--hidden" id="blitz-dock-root" aria-live="polite">
  <button class="blitz-dock__bubble"
          type="button"
          aria-controls="blitz-dock-panel"
          aria-expanded="false"
          aria-label="<?php echo esc_attr__( 'Open support panel', 'blitz-dock' ); ?>">
    <span class="blitz-dock__bubble-icon" aria-hidden="true"></span>
  </button>

  <div class="blitz-dock__overlay" data-blitz-dock-overlay hidden></div>

  <section id="blitz-dock-panel"
           class="blitz-dock__panel"
           role="dialog"
           aria-modal="true"
           aria-labelledby="blitz-dock-title"
           aria-describedby="blitz-dock-desc"
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
              aria-label="<?php echo esc_attr__( 'Close panel', 'blitz-dock' ); ?>">
        <span class="blitz-dock__close-icon" aria-hidden="true"></span>
      </button>
    </header>

    <div class="blitz-dock__body" tabindex="-1">
      <!-- Empty state for now -->
    </div>
  </section>
</div>