<?php
/**
 * Database operations for WeddingBlocks.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Create custom RSVP table on plugin activation.
 */
function weddingblocks_db_install() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'weddingblocks_rsvps';
    
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        post_id bigint(20) NOT NULL,
        guest_name varchar(255) NOT NULL,
        attendance varchar(50) NOT NULL,
        guests_count int(11) NOT NULL DEFAULT 1,
        message text NOT NULL,
        created_at datetime NOT NULL,
        PRIMARY KEY  (id),
        KEY post_id (post_id)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );
}

/**
 * Save an RSVP submission.
 *
 * @param array $data Submission data.
 * @return int|bool Inserted ID or false on failure.
 */
function weddingblocks_save_rsvp( $data ) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'weddingblocks_rsvps';

    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
    $inserted = $wpdb->insert(
        $table_name,
        array(
            'post_id'      => intval( $data['post_id'] ),
            'guest_name'   => sanitize_text_field( $data['guest_name'] ),
            'attendance'   => sanitize_text_field( $data['attendance'] ),
            'guests_count' => intval( $data['guests_count'] ),
            'message'      => sanitize_textarea_field( $data['message'] ),
            'created_at'   => current_time( 'mysql' ),
        ),
        array( '%d', '%s', '%s', '%d', '%s', '%s' )
    );

    if ( $inserted ) {
        return $wpdb->insert_id;
    }

    return false;
}

/**
 * Retrieve RSVPs.
 *
 * @param int $post_id Optional filter by invitation post ID.
 * @param int $limit Number of records to return.
 * @param int $offset Offset for pagination.
 * @return array List of RSVPs.
 */
function weddingblocks_get_rsvps( $post_id = 0, $limit = 100, $offset = 0 ) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'weddingblocks_rsvps';

    $sql = "SELECT * FROM $table_name";
    $params = array();

    if ( $post_id > 0 ) {
        $sql .= " WHERE post_id = %d";
        $params[] = $post_id;
    }

    $sql .= " ORDER BY created_at DESC LIMIT %d OFFSET %d";
    $params[] = intval( $limit );
    $params[] = intval( $offset );

    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
    return $wpdb->get_results( $wpdb->prepare( $sql, $params ) );
}

/**
 * Count total RSVPs.
 *
 * @param int $post_id Optional filter by invitation post ID.
 * @return int Total RSVP count.
 */
function weddingblocks_get_rsvps_count( $post_id = 0 ) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'weddingblocks_rsvps';

    if ( $post_id > 0 ) {
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
        return intval( $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table_name WHERE post_id = %d", $post_id ) ) );
    }

    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
    return intval( $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" ) );
}

/**
 * Delete an RSVP by ID.
 *
 * @param int $id RSVP ID.
 * @return bool True on success, false on failure.
 */
function weddingblocks_delete_rsvp( $id ) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'weddingblocks_rsvps';

    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
    $deleted = $wpdb->delete(
        $table_name,
        array( 'id' => intval( $id ) ),
        array( '%d' )
    );

    return $deleted !== false;
}
