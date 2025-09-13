<?php
/**
 * Uninstall handler for Blitz Dock.
 *
 * Deletes plugin options on uninstall (single site and multisite).
 *
 * @package BlitzDock
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

/**
 * Delete options for current blog.
 *
 * @return void
 */
function blitz_dock_delete_options() {
    $keys = array(
        'blitz_dock_options',
    );

    foreach ( $keys as $key ) {
        delete_option( $key );
    }
    // If any site-options are ever used, add delete_site_option( $key ) here.
}

if ( is_multisite() ) {
    $site_ids = get_sites(
        array(
            'number' => 0,
            'fields'  => 'ids',
        )
    );

    foreach ( $site_ids as $site_id ) {
        switch_to_blog( (int) $site_id );
        blitz_dock_delete_options();
        restore_current_blog();
    }
} else {
    blitz_dock_delete_options();
}