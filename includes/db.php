<?php

/**
 * Database operations for WeddingBlocks.
 */

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Create custom RSVP table on plugin activation.
 */
function weddingblocks_db_install()
{
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
    dbDelta($sql);
}

/**
 * Save an RSVP submission.
 *
 * @param array $data Submission data.
 * @return int|bool Inserted ID or false on failure.
 */
function weddingblocks_save_rsvp($data)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'weddingblocks_rsvps';

    $insert_data = array(
        'post_id'      => intval($data['post_id']),
        'guest_name'   => sanitize_text_field($data['guest_name']),
        'attendance'   => sanitize_text_field($data['attendance']),
        'guests_count' => intval($data['guests_count']),
        'message'      => sanitize_textarea_field($data['message']),
        'created_at'   => current_time('mysql'),
    );
    $insert_format = array('%d', '%s', '%s', '%d', '%s', '%s');

    /**
     * Extension point: Pro can add extra columns to the RSVP insert
     * (e.g. guest_id, linking the submission back to an imported guest
     * record) without Free needing to know that column exists. Pro is
     * responsible for adding the column itself (via dbDelta on its own
     * activation) before relying on this filter.
     *
     * @param array $insert_data Column => value pairs to insert.
     * @param array $data        Original raw RSVP submission data.
     */
    $insert_data = apply_filters('weddingblocks_rsvp_insert_data', $insert_data, $data);

    /**
     * Matching %d/%s/%f format specifiers for $insert_data, in the same
     * order. Must be kept in sync by whoever adds a column above.
     *
     * @param array $insert_format Format specifiers.
     * @param array $insert_data   Column => value pairs being inserted.
     */
    $insert_format = apply_filters('weddingblocks_rsvp_insert_format', $insert_format, $insert_data);

    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
    $inserted = $wpdb->insert($table_name, $insert_data, $insert_format);

    if ($inserted) {
        weddingblocks_clear_rsvps_count_cache(intval($data['post_id']));
        return $wpdb->insert_id;
    }

    return false;
}

/**
 * Retrieve RSVPs.
 *
 * @param int    $post_id Optional filter by invitation post ID.
 * @param int    $limit   Number of records to return.
 * @param int    $offset  Offset for pagination.
 * @param string $status  Optional status filter: 'pending'|'approved'|'hidden'.
 * @param string $search  Optional search term for guest name or WA.
 * @param string $attendance Optional attendance filter: 'hadir'|'tidak_hadir'|'ragu_ragu'.
 * @return array List of RSVPs.
 */
function weddingblocks_get_rsvps($post_id = 0, $limit = 100, $offset = 0, $status = '', $search = '', $attendance = '')
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'weddingblocks_rsvps';

    $sql    = "SELECT * FROM $table_name";
    $params = array();

    if ($post_id > 0) {
        $sql     .= " WHERE post_id = %d";
        $params[] = $post_id;
    }

    // Status filter is only enabled if an add-on (e.g. Pro) supports it.
    if (! empty($status) && apply_filters('weddingblocks_rsvp_enable_status_filter', false)) {
        $sql     .= (empty($params) ? ' WHERE ' : ' AND ') . 'status = %s';
        $params[] = $status;
    }

    if (! empty($search)) {
        $search_term   = '%' . $wpdb->esc_like($search) . '%';
        $search_fields = (array) apply_filters('weddingblocks_rsvp_search_fields', array('guest_name'));
        $clauses       = array();

        foreach ($search_fields as $field) {
            $field = preg_replace('/[^a-zA-Z0-9_]/', '', $field);
            if (! empty($field)) {
                $clauses[] = "$field LIKE %s";
                $params[]  = $search_term;
            }
        }

        if (! empty($clauses)) {
            $sql .= (empty($params) ? ' WHERE ' : ' AND ') . '(' . implode(' OR ', $clauses) . ')';
        }
    }

    if (! empty($attendance)) {
        $sql     .= (empty($params) ? ' WHERE ' : ' AND ') . 'attendance = %s';
        $params[] = $attendance;
    }

    $sql     .= " ORDER BY created_at DESC LIMIT %d OFFSET %d";
    $params[] = intval($limit);
    $params[] = intval($offset);

    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
    return $wpdb->get_results($wpdb->prepare($sql, ...$params));
}

/**
 * Count total RSVPs.
 *
 * @param int    $post_id    Optional filter by invitation post ID.
 * @param string $status     Optional status filter: 'pending'|'approved'|'hidden'.
 * @param string $search     Optional search term for guest name or WA.
 * @param string $attendance Optional attendance filter: 'hadir'|'tidak_hadir'|'ragu_ragu'.
 * @return int Total RSVP count.
 */
function weddingblocks_get_rsvps_count($post_id = 0, $status = '', $search = '', $attendance = '')
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'weddingblocks_rsvps';

    $is_filtered = (! empty($status) || ! empty($search) || ! empty($attendance));
    $cache_key   = 'weddingblocks_rsvps_count_' . intval($post_id);
    $cache_group = 'weddingblocks';

    // Only cache base (unfiltered) count to ensure accurate cache invalidation on inserts/deletes.
    if (! $is_filtered) {
        $cached = wp_cache_get($cache_key, $cache_group);
        if (false !== $cached) {
            return intval($cached);
        }
    }

    $params = array();
    if ($post_id > 0) {
        $sql      = "SELECT COUNT(*) FROM $table_name WHERE post_id = %d";
        $params[] = $post_id;
    } else {
        $sql = "SELECT COUNT(*) FROM $table_name";
    }

    if (! empty($status) && apply_filters('weddingblocks_rsvp_enable_status_filter', false)) {
        $sql     .= (empty($params) ? ' WHERE ' : ' AND ') . 'status = %s';
        $params[] = $status;
    }

    if (! empty($search)) {
        $search_term   = '%' . $wpdb->esc_like($search) . '%';
        $search_fields = (array) apply_filters('weddingblocks_rsvp_search_fields', array('guest_name'));
        $clauses       = array();

        foreach ($search_fields as $field) {
            $field = preg_replace('/[^a-zA-Z0-9_]/', '', $field);
            if (! empty($field)) {
                $clauses[] = "$field LIKE %s";
                $params[]  = $search_term;
            }
        }

        if (! empty($clauses)) {
            $sql .= (empty($params) ? ' WHERE ' : ' AND ') . '(' . implode(' OR ', $clauses) . ')';
        }
    }

    if (! empty($attendance)) {
        $sql     .= (empty($params) ? ' WHERE ' : ' AND ') . 'attendance = %s';
        $params[] = $attendance;
    }

    if (! empty($params)) {
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
        $count = (int) $wpdb->get_var($wpdb->prepare($sql, ...$params));
    } else {
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
        $count = (int) $wpdb->get_var($sql);
    }

    if (! $is_filtered) {
        wp_cache_set($cache_key, $count, $cache_group, 5 * MINUTE_IN_SECONDS);
    }

    return $count;
}

/**
 * Clear cached RSVP counts for a given invitation (and the global total).
 *
 * @param int $post_id Invitation post ID.
 */
function weddingblocks_clear_rsvps_count_cache($post_id)
{
    $cache_group = 'weddingblocks';
    wp_cache_delete('weddingblocks_rsvps_count_' . intval($post_id), $cache_group);
    wp_cache_delete('weddingblocks_rsvps_count_0', $cache_group);

    do_action('weddingblocks_clear_rsvps_count_cache', $post_id);
}

/**
 * Delete an RSVP by ID.
 *
 * @param int $id RSVP ID.
 * @return bool True on success, false on failure.
 */
function weddingblocks_delete_rsvp($id)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'weddingblocks_rsvps';

    // Fetch the related post_id first so we know which cached count to invalidate.
    // This is a one-off lookup immediately followed by a delete, so caching it is not useful.
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
    $post_id = $wpdb->get_var($wpdb->prepare("SELECT post_id FROM %i WHERE id = %d", $table_name, intval($id)));

    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
    $deleted = $wpdb->delete(
        $table_name,
        array('id' => intval($id)),
        array('%d')
    );

    if ($deleted !== false) {
        weddingblocks_clear_rsvps_count_cache(intval($post_id));
    }

    return $deleted !== false;
}
