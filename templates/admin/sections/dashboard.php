<?php
/**
 * Dashboard section template.
 *
 * @package BlitzDock
 * @since 0.1.0
 * @license GPL-2.0-or-later
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

?>
<?php include __DIR__ . '/../partials/header.php'; ?>
<?php include __DIR__ . '/../partials/container-start.php'; ?>
<p><?php esc_html_e( 'Content will appear here.', 'blitz-dock' ); ?></p>
<?php include __DIR__ . '/../partials/container-end.php'; ?>