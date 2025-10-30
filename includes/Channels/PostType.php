<?php
/**
 * Channel custom post type registration.
 *
 * @package BlitzDock
 */

namespace BlitzDock\Channels;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the channel post type and its meta.
 */
class PostType {

	/**
	 * Post type slug.
	 *
	 * @since 0.2.0
	 */
	public const POST_TYPE = 'blitz_channel';

	/**
	 * Meta key for provider type.
	 *
	 * @since 0.2.0
	 */
	public const META_TYPE = 'blitz_channel_type';

	/**
	 * Meta key for provider URL.
	 *
	 * @since 0.2.0
	 */
	public const META_URL = 'blitz_channel_url';

	/**
	 * Meta key for channel status.
	 *
	 * @since 0.2.0
	 */
	public const META_STATUS = 'blitz_channel_status';

	/**
	 * Hook registrations.
	 *
	 * @since 0.2.0
	 *
	 * @return void
	 */
	public function register() : void {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'init', array( $this, 'register_meta' ) );
	}

	/**
	 * Register the post type.
	 *
	 * @since 0.2.0
	 *
	 * @return void
	 */
	public function register_post_type() : void {
		register_post_type(
			self::POST_TYPE,
			array(
				'labels'       => array(
					'name'          => __( 'Channels', 'blitz-dock' ),
					'singular_name' => __( 'Channel', 'blitz-dock' ),
				),
				'public'       => false,
				'show_ui'      => false,
				'show_in_menu' => false,
				'supports'     => array( 'title' ),
				'rewrite'      => false,
			)
		);
	}

	/**
	 * Register post meta for channels.
	 *
	 * @since 0.2.0
	 *
	 * @return void
	 */
	public function register_meta() : void {
		register_post_meta(
			self::POST_TYPE,
			self::META_TYPE,
			array(
				'type'              => 'string',
				'single'            => true,
				'sanitize_callback' => 'sanitize_key',
				'auth_callback'     => array( $this, 'auth_callback' ),
				'show_in_rest'      => false,
			)
		);

		register_post_meta(
			self::POST_TYPE,
			self::META_URL,
			array(
				'type'              => 'string',
				'single'            => true,
				'sanitize_callback' => 'esc_url_raw',
				'auth_callback'     => array( $this, 'auth_callback' ),
				'show_in_rest'      => false,
			)
		);

		register_post_meta(
			self::POST_TYPE,
			self::META_STATUS,
			array(
				'type'              => 'string',
				'single'            => true,
				'default'           => 'active',
				'sanitize_callback' => array( $this, 'sanitize_status_meta' ),
				'auth_callback'     => array( $this, 'auth_callback' ),
				'show_in_rest'      => false,
			)
		);
	}

	/**
	 * Sanitize the stored channel status.
	 *
	 * @since 0.2.0
	 *
	 * @param mixed $value Raw value.
	 * @return string Sanitized status slug.
	 */
	public function sanitize_status_meta( $value ) : string {
		$value   = sanitize_key( (string) $value );
		$allowed = array( 'active', 'disabled' );

		if ( ! in_array( $value, $allowed, true ) ) {
			return 'active';
		}

		return $value;
	}

	/**
	 * Capability check for updating channel meta.
	 *
	 * @since 0.2.0
	 *
	 * @return bool Whether the current user can manage options.
	 */
	public function auth_callback() : bool {
		return current_user_can( 'manage_options' );
	}
}