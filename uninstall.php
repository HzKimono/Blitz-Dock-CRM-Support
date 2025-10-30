<?php
/**
 * Uninstall handler for Blitz Dock.
 *
 * Removes custom channel data when the plugin is uninstalled.
 *
 * @package BlitzDock
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

if ( function_exists( 'current_user_can' ) && ! current_user_can( 'manage_options' ) ) {
	return;
}

$delete_data = static function () : void {
	$channel_ids = get_posts(
		array(
			'post_type'        => 'blitz_channel',
			'post_status'      => 'any',
			'numberposts'      => -1,
			'fields'           => 'ids',
			'no_found_rows'    => true,
			'suppress_filters' => true,
		)
	);

	foreach ( $channel_ids as $channel_id ) {
		wp_delete_post( (int) $channel_id, true );
	}

	delete_transient( 'blitz_channels_active' );
	delete_option( 'blitz_dock_options' );
};

if ( is_multisite() ) {
	$site_ids = get_sites(
		array(
			'number' => 0,
			'fields' => 'ids',
		)
	);

	wp_suspend_cache_invalidation( true );

	foreach ( $site_ids as $site_id ) {
		switch_to_blog( (int) $site_id );
		$delete_data();
		restore_current_blog();
	}

	wp_suspend_cache_invalidation( false );
} else {
	$delete_data();
}