<?php
/**
 * Channels section template wrapper.
 *
 * @package BlitzDock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

include __DIR__ . '/../partials/header.php';
include __DIR__ . '/../partials/container-start.php';

load_template(
	\BlitzDock\Core\Plugin::PATH . 'templates/admin/channels/index.php',
	true
);

include __DIR__ . '/../partials/container-end.php';