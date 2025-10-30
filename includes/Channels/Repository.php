<?php
/**
 * Channel repository for CRUD and queries.
 *
 * @package BlitzDock
 */

namespace BlitzDock\Channels;

use WP_Error;
use WP_Post;
use WP_Query;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Storage layer for channel data.
 */
class Repository {

	/**
	 * Allowed status values.
	 *
	 * @since 0.2.0
	 */
	private const STATUSES = array( 'active', 'disabled' );

	/**
	 * Transient key for active channel cache.
	 *
	 * @since 0.2.0
	 */
	private const ACTIVE_TRANSIENT = 'blitz_channels_active';

	/**
	 * Create a new channel entry.
	 *
	 * @since 0.2.0
	 *
	 * @param string $type   Channel provider slug.
	 * @param string $url    Channel URL.
	 * @param string $status Channel status.
	 * @return int|WP_Error Post ID on success, WP_Error otherwise.
	 */
	public static function add( string $type, string $url, string $status = 'active' ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return new WP_Error( 'forbidden', __( 'You are not allowed to manage channels.', 'blitz-dock' ) );
		}

		$type    = sanitize_key( $type );
		$status  = sanitize_key( $status );
		$raw_url = self::prepare_url( $url );

		if ( '' === $type || ! Providers::exists( $type ) ) {
			return new WP_Error( 'invalid_provider', __( 'Invalid channel provider.', 'blitz-dock' ) );
		}

		if ( ! self::is_valid_status( $status ) ) {
			return new WP_Error( 'invalid_status', __( 'Invalid channel status.', 'blitz-dock' ) );
		}

		if ( '' === $raw_url || ! self::validate_url( $type, $raw_url ) ) {
			return new WP_Error( 'invalid_url', __( 'Invalid channel URL.', 'blitz-dock' ) );
		}

		$stored_url = esc_url_raw( $raw_url );

		if ( '' === $stored_url || ! self::validate_url( $type, $stored_url ) ) {
			return new WP_Error( 'invalid_url', __( 'Invalid channel URL.', 'blitz-dock' ) );
		}

		$post_id = wp_insert_post(
			array(
				'post_type'   => PostType::POST_TYPE,
				'post_title'  => Providers::label( $type ),
				'post_status' => 'publish',
				'meta_input'  => array(
					PostType::META_TYPE   => $type,
					PostType::META_URL    => $stored_url,
					PostType::META_STATUS => $status,
				),
			),
			true
		);

		if ( is_wp_error( $post_id ) ) {
			return $post_id;
		}

		self::clear_active_cache();

		return (int) $post_id;
	}

	/**
	 * Update a channel status.
	 *
	 * @since 0.2.0
	 *
	 * @param int    $post_id Channel post ID.
	 * @param string $status  New status value.
	 * @return true|WP_Error True on success, WP_Error on failure.
	 */
	public static function update_status( int $post_id, string $status ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return new WP_Error( 'forbidden', __( 'You are not allowed to manage channels.', 'blitz-dock' ) );
		}

		$post_id = absint( $post_id );
		$status  = sanitize_key( $status );

		$post = get_post( $post_id );
		if ( ! $post instanceof WP_Post || PostType::POST_TYPE !== $post->post_type ) {
			return new WP_Error( 'not_found', __( 'Channel not found.', 'blitz-dock' ) );
		}

		if ( ! self::is_valid_status( $status ) ) {
			return new WP_Error( 'invalid_status', __( 'Invalid channel status.', 'blitz-dock' ) );
		}

		$current_status = (string) get_post_meta( $post_id, PostType::META_STATUS, true );
		if ( $status === $current_status ) {
			return true;
		}

		$updated = update_post_meta( $post_id, PostType::META_STATUS, $status );

		if ( false === $updated ) {
			return new WP_Error( 'update_failed', __( 'Unable to update channel status.', 'blitz-dock' ) );
		}

		self::clear_active_cache();

		return true;
	}

	/**
	 * Delete a channel entry.
	 *
	 * @since 0.2.0
	 *
	 * @param int $post_id Channel post ID.
	 * @return true|WP_Error True on success, WP_Error on failure.
	 */
	public static function delete( int $post_id ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return new WP_Error( 'forbidden', __( 'You are not allowed to manage channels.', 'blitz-dock' ) );
		}

		$post_id = absint( $post_id );

		$post = get_post( $post_id );
		if ( ! $post instanceof WP_Post || PostType::POST_TYPE !== $post->post_type ) {
			return new WP_Error( 'not_found', __( 'Channel not found.', 'blitz-dock' ) );
		}

		$deleted = wp_delete_post( $post_id, true );

		if ( ! $deleted ) {
			return new WP_Error( 'delete_failed', __( 'Unable to delete the channel.', 'blitz-dock' ) );
		}

		self::clear_active_cache();

		return true;
	}

	/**
	 * Retrieve active channels.
	 *
	 * @since 0.2.0
	 *
	 * @param bool $force Force cache refresh.
	 * @return array<int, array{ID:int,type:string,url:string,status:string,label:string}> Active channels.
	 */
	public static function get_active( bool $force = false ) : array {
		$cached = get_transient( self::ACTIVE_TRANSIENT );

		if ( $force || ! is_array( $cached ) ) {
			$cached = self::query_channels(
				array(
					'meta_query' => array(
						array(
							'key'   => PostType::META_STATUS,
							'value' => 'active',
						),
					),
				)
			);

			set_transient( self::ACTIVE_TRANSIENT, $cached, MINUTE_IN_SECONDS * 3 );
		}

		return $cached;
	}

	/**
	 * Retrieve a single channel entry.
	 *
	 * @since 0.2.0
	 *
	 * @param int $post_id Channel post ID.
	 * @return array{ID:int,type:string,url:string,status:string,label:string}|null Channel data or null.
	 */
	public static function get( int $post_id ) : ?array {
		$post_id = absint( $post_id );

		if ( 0 === $post_id ) {
			return null;
		}

		$post = get_post( $post_id );
		if ( ! $post instanceof WP_Post || PostType::POST_TYPE !== $post->post_type ) {
			return null;
		}

		return self::prepare_channel( $post_id );
	}

	/**
	 * Retrieve paginated channel data for the admin table.
	 *
	 * @since 0.2.0
	 *
	 * @param array<string, mixed> $args Query arguments.
	 * @return array{items:array<int, array{ID:int,type:string,url:string,status:string,label:string}>,total:int} Data and total.
	 */
	public static function get_all( array $args = array() ) : array {
		$defaults = array(
			'paged'          => 1,
			'posts_per_page' => 20,
		);

		$args = wp_parse_args( $args, $defaults );

		$paged          = max( 1, (int) $args['paged'] );
		$posts_per_page = max( 1, (int) $args['posts_per_page'] );

		$query_args = array(
			'post_type'      => PostType::POST_TYPE,
			'post_status'    => array( 'publish', 'draft', 'pending', 'private' ),
			'posts_per_page' => $posts_per_page,
			'paged'          => $paged,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'fields'         => 'ids',
		);

		$query = new WP_Query( $query_args );

		$items = array();

		foreach ( $query->posts as $item_id ) {
			$channel = self::prepare_channel( (int) $item_id );

			if ( null !== $channel ) {
				$items[] = $channel;
			}
		}

		wp_reset_postdata();

		return array(
			'items' => $items,
			'total' => (int) $query->found_posts,
		);
	}

	/**
	 * Clear cached active channels.
	 *
	 * @since 0.2.0
	 *
	 * @return void
	 */
	public static function clear_active_cache() : void {
		delete_transient( self::ACTIVE_TRANSIENT );
	}

	/**
	 * Validate a status value.
	 *
	 * @since 0.2.0
	 *
	 * @param string $status Status to validate.
	 * @return bool Whether the status is allowed.
	 */
	private static function is_valid_status( string $status ) : bool {
		return in_array( $status, self::STATUSES, true );
	}

	/**
	 * Validate a channel URL against provider rules.
	 *
	 * @since 0.2.0
	 *
	 * @param string $type Provider slug.
	 * @param string $url  URL to validate.
	 * @return bool Whether the URL is valid for the provider.
	 */
	private static function validate_url( string $type, string $url ) : bool {
		$pattern = Providers::validator( $type );

		if ( '' === $pattern ) {
			return false;
		}

		return 1 === preg_match( $pattern, $url );
	}

	/**
	 * Prepare a URL before validation and storage.
	 *
	 * @since 0.2.0
	 *
	 * @param string $url Raw URL input.
	 * @return string Sanitized URL string.
	 */
	private static function prepare_url( string $url ) : string {
		$url = wp_unslash( $url );
		$url = is_string( $url ) ? trim( wp_strip_all_tags( $url ) ) : '';

		return $url;
	}

	/**
	 * Build a normalized channel array.
	 *
	 * @since 0.2.0
	 *
	 * @param int $post_id Channel post ID.
	 * @return array{ID:int,type:string,url:string,status:string,label:string}|null Normalized data.
	 */
	private static function prepare_channel( int $post_id ) : ?array {
		$type   = sanitize_key( (string) get_post_meta( $post_id, PostType::META_TYPE, true ) );
		$url    = esc_url_raw( (string) get_post_meta( $post_id, PostType::META_URL, true ) );
		$status = sanitize_key( (string) get_post_meta( $post_id, PostType::META_STATUS, true ) );

		if ( '' === $type || ! Providers::exists( $type ) ) {
			return null;
		}

		if ( '' !== $url && ! self::validate_url( $type, $url ) ) {
			$url = '';
		}

		if ( ! self::is_valid_status( $status ) ) {
			$status = 'active';
		}

		return array(
			'ID'     => $post_id,
			'type'   => $type,
			'url'    => $url,
			'status' => $status,
			'label'  => Providers::label( $type ),
		);
	}

	/**
	 * Run a channel query and normalize the results.
	 *
	 * @since 0.2.0
	 *
	 * @param array<string, mixed> $args Additional WP_Query arguments.
	 * @return array<int, array{ID:int,type:string,url:string,status:string,label:string}> Normalized channels.
	 */
	private static function query_channels( array $args = array() ) : array {
		$defaults = array(
			'post_type'      => PostType::POST_TYPE,
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'orderby'        => 'date',
			'order'          => 'DESC',
			'no_found_rows'  => true,
		);

		$query = new WP_Query( wp_parse_args( $args, $defaults ) );

		$channels = array();

		foreach ( $query->posts as $item_id ) {
			$channel = self::prepare_channel( (int) $item_id );

			if ( null !== $channel && 'active' === $channel['status'] ) {
				$channels[] = $channel;
			}
		}

		wp_reset_postdata();

		return $channels;
	}
}