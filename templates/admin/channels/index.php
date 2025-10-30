<?php
/**
 * Channels admin template.
 *
 * @package BlitzDock
 */

use BlitzDock\Channels\AdminTable;
use BlitzDock\Channels\Controller;
use BlitzDock\Channels\Providers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$providers = Providers::all();
$table     = new AdminTable();
$table->prepare_items();

$statuses = array(
	'active'   => __( 'Active', 'blitz-dock' ),
	'disabled' => __( 'Disabled', 'blitz-dock' ),
);

$notice_code = '';
$notice      = null;
$notices     = Controller::notice_map();

if ( isset( $_GET['bd_notice'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Display-only notice.
	$notice_code = sanitize_key( wp_unslash( $_GET['bd_notice'] ) );

	if ( isset( $notices[ $notice_code ] ) ) {
		$notice = $notices[ $notice_code ];
	} else {
		$notice = $notices['general_error'];
	}
}

$action_url = admin_url( 'admin-post.php' );
?>
<div class="blitz-dock-channels">
	<?php if ( $notice ) : ?>
		<?php $notice_class = 'error' === $notice['type'] ? 'error' : 'success'; ?>
		<div class="notice notice-<?php echo esc_attr( $notice_class ); ?> is-dismissible">
			<p><?php echo esc_html( $notice['message'] ); ?></p>
		</div>
	<?php endif; ?>

	<form method="post" action="<?php echo esc_url( $action_url ); ?>" class="blitz-dock-channels__form">
		<input type="hidden" name="action" value="blitz_channel_add" />
		<?php wp_nonce_field( 'blitz_channel_add', 'blitz_channel_nonce' ); ?>

		<div class="blitz-dock-channels__form-row">
			<label for="blitz-channel-type" class="screen-reader-text">
				<?php esc_html_e( 'Channel provider', 'blitz-dock' ); ?>
			</label>
			<select id="blitz-channel-type" name="blitz_channel_type" class="blitz-dock-channels__select">
				<?php foreach ( $providers as $slug => $provider ) : ?>
					<option value="<?php echo esc_attr( $slug ); ?>">
						<?php echo esc_html( $provider['label'] ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>

		<div class="blitz-dock-channels__form-row">
			<label for="blitz-channel-url" class="screen-reader-text">
				<?php esc_html_e( 'Channel URL', 'blitz-dock' ); ?>
			</label>
			<input type="url" id="blitz-channel-url" name="blitz_channel_url" class="regular-text" required placeholder="<?php echo esc_attr__( 'https://wa.me/12345678901', 'blitz-dock' ); ?>" />
		</div>

		<div class="blitz-dock-channels__form-row">
			<label for="blitz-channel-status" class="screen-reader-text">
				<?php esc_html_e( 'Channel status', 'blitz-dock' ); ?>
			</label>
			<select id="blitz-channel-status" name="blitz_channel_status" class="blitz-dock-channels__select">
				<?php foreach ( $statuses as $slug => $label ) : ?>
					<option value="<?php echo esc_attr( $slug ); ?>" <?php selected( 'active', $slug ); ?>>
						<?php echo esc_html( $label ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>

		<div class="blitz-dock-channels__form-row">
			<button type="submit" class="button button-primary">
				<?php esc_html_e( 'Add Channel', 'blitz-dock' ); ?>
			</button>
		</div>
	</form>

	<div class="blitz-dock-channels__table">
		<?php $table->display(); ?>
	</div>
</div>