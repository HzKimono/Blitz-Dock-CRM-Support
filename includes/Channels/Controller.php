<?php
/**
 * Channel admin controller.
 *
 * @package BlitzDock
 */

namespace BlitzDock\Channels;

use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle admin channel actions and notices.
 */
class Controller {

	/**
	 * Register WordPress hooks.
	 *
	 * @since 0.2.0
	 *
	 * @return void
	 */
	public function register() : void {
		add_action( 'admin_post_blitz_channel_add', array( $this, 'handle_add' ) );
		add_action( 'admin_post_blitz_channel_toggle', array( $this, 'handle_toggle' ) );
		add_action( 'admin_post_blitz_channel_delete', array( $this, 'handle_delete' ) );
	}

	/**
	 * Map of notice codes to messages.
	 *
	 * @since 0.2.0
	 *
	 * @return array<string, array{type:string,message:string}> Whitelisted notice messages.
	 */
	public static function notice_map() : array {
		return array(
			'added'            => array(
				'type'    => 'success',
				'message' => __( 'Channel added successfully.', 'blitz-dock' ),
			),
			'updated'          => array(
				'type'    => 'success',
				'message' => __( 'Channel status updated.', 'blitz-dock' ),
			),
			'deleted'          => array(
				'type'    => 'success',
				'message' => __( 'Channel deleted.', 'blitz-dock' ),
			),
			'invalid_url'      => array(
				'type'    => 'error',
				'message' => __( 'Invalid channel URL.', 'blitz-dock' ),
			),
			'invalid_provider' => array(
				'type'    => 'error',
				'message' => __( 'Invalid channel provider.', 'blitz-dock' ),
			),
			'invalid_status'   => array(
				'type'    => 'error',
				'message' => __( 'Invalid channel status.', 'blitz-dock' ),
			),
			'forbidden'        => array(
				'type'    => 'error',
				'message' => __( 'You are not allowed to perform this action.', 'blitz-dock' ),
			),
			'not_found'        => array(
				'type'    => 'error',
				'message' => __( 'Channel not found.', 'blitz-dock' ),
			),
			'update_failed'    => array(
				'type'    => 'error',
				'message' => __( 'Unable to update channel status.', 'blitz-dock' ),
			),
			'delete_failed'    => array(
				'type'    => 'error',
				'message' => __( 'Unable to delete the channel.', 'blitz-dock' ),
			),
			'general_error'    => array(
				'type'    => 'error',
				'message' => __( 'Something went wrong. Please try again.', 'blitz-dock' ),
			),
		);
	}

	/**
	 * Handle channel creation requests.
	 *
	 * @since 0.2.0
	 *
	 * @return void
	 */
	public function handle_add() : void {
		$this->assert_capability();

		$method = strtoupper( (string) ( $_SERVER['REQUEST_METHOD'] ?? '' ) );
		if ( 'POST' !== $method ) {
			$this->redirect_with_notice( 'general_error' );
		}

		check_admin_referer( 'blitz_channel_add', 'blitz_channel_nonce' );

		$type   = isset( $_POST['blitz_channel_type'] ) ? sanitize_key( wp_unslash( $_POST['blitz_channel_type'] ) ) : '';
		$status = isset( $_POST['blitz_channel_status'] ) ? sanitize_key( wp_unslash( $_POST['blitz_channel_status'] ) ) : 'active';

		$raw_url = isset( $_POST['blitz_channel_url'] ) ? wp_unslash( $_POST['blitz_channel_url'] ) : '';
		$url     = is_string( $raw_url ) ? trim( $raw_url ) : '';

		$result = Repository::add( $type, $url, $status );

		if ( $result instanceof WP_Error ) {
			$this->redirect_with_notice( $result->get_error_code() );
		}

		$this->redirect_with_notice( 'added' );
	}

	/**
	 * Handle channel toggle requests.
	 *
	 * @since 0.2.0
	 *
	 * @return void
	 */
	public function handle_toggle() : void {
		$this->assert_capability();

		$method = strtoupper( (string) ( $_SERVER['REQUEST_METHOD'] ?? '' ) );
		if ( 'POST' !== $method ) {
			$this->redirect_with_notice( 'general_error' );
		}

		$channel_id = isset( $_POST['channel_id'] ) ? absint( wp_unslash( $_POST['channel_id'] ) ) : 0;

		if ( 0 === $channel_id ) {
			$this->redirect_with_notice( 'not_found' );
		}

		check_admin_referer( 'blitz_channel_toggle_' . $channel_id );

		$status = isset( $_POST['blitz_channel_status'] ) ? sanitize_key( wp_unslash( $_POST['blitz_channel_status'] ) ) : '';

		if ( '' === $status ) {
			$this->redirect_with_notice( 'invalid_status' );
		}

		$result = Repository::update_status( $channel_id, $status );

		if ( $result instanceof WP_Error ) {
			$this->redirect_with_notice( $result->get_error_code() );
		}

		$this->redirect_with_notice( 'updated' );
	}

	/**
	 * Handle channel deletion requests.
	 *
	 * @since 0.2.0
	 *
	 * @return void
	 */
	public function handle_delete() : void {
		$this->assert_capability();

		$method = strtoupper( (string) ( $_SERVER['REQUEST_METHOD'] ?? '' ) );
		if ( 'POST' !== $method ) {
			$this->redirect_with_notice( 'general_error' );
		}

		$channel_id = isset( $_POST['channel_id'] ) ? absint( wp_unslash( $_POST['channel_id'] ) ) : 0;

		if ( 0 === $channel_id ) {
			$this->redirect_with_notice( 'not_found' );
		}

		check_admin_referer( 'blitz_channel_delete_' . $channel_id );

		$result = Repository::delete( $channel_id );

		if ( $result instanceof WP_Error ) {
			$this->redirect_with_notice( $result->get_error_code() );
		}

		$this->redirect_with_notice( 'deleted' );
	}

	/**
	 * Redirect back to the channels screen with a notice code.
	 *
	 * @since 0.2.0
	 *
	 * @param string $code Notice code to display.
	 * @return void
	 */
	private function redirect_with_notice( string $code ) : void {
		$code = sanitize_key( $code );

		$notices = self::notice_map();

		if ( ! isset( $notices[ $code ] ) ) {
			$code = 'general_error';
		}

		$redirect = add_query_arg(
			array(
				'page'      => 'blitz-dock',
				'tab'       => 'channels',
				'bd_notice' => $code,
			),
			admin_url( 'admin.php' )
		);

		wp_safe_redirect( $redirect );
		exit;
	}

	/**
	 * Ensure the current user can manage channels.
	 *
	 * @since 0.2.0
	 *
	 * @return void
	 */
	private function assert_capability() : void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You are not allowed to perform this action.', 'blitz-dock' ) );
		}
	}
}