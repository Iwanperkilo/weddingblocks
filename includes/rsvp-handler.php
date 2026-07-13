<?php

/**
 * RSVP Form AJAX/REST API Handler for WeddingBlocks.
 */

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Register REST API route for RSVP.
 */
function weddingblocks_register_rsvp_route()
{
    register_rest_route('weddingblocks/v1', '/rsvp', array(
        'methods'             => 'POST',
        'callback'            => 'weddingblocks_handle_rsvp_submission',
        'permission_callback' => 'weddingblocks_rsvp_permissions_check',
    ));
}
add_action('rest_api_init', 'weddingblocks_register_rsvp_route');

/**
 * Permission check for RSVP submissions (publicly open).
 */
function weddingblocks_rsvp_permissions_check()
{
    return true; // Allow anyone to submit RSVP.
}

/**
 * Get the client IP address (best-effort, for rate limiting purposes only).
 *
 * @return string
 */
function weddingblocks_get_client_ip()
{
    // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
    $ip = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '';
    return $ip;
}

/**
 * Simple per-IP rate limit for RSVP submissions using transients.
 *
 * @param string $ip Client IP.
 * @return bool True if the request is allowed, false if rate-limited.
 */
function weddingblocks_rsvp_rate_limit_ok($ip)
{
    if (empty($ip)) {
        return true;
    }

    $key     = 'wdbl_rsvp_rl_' . md5($ip);
    $count   = (int) get_transient($key);
    $limit   = 5;
    $window  = 300;

    if ($count >= $limit) {
        return false;
    }

    set_transient($key, $count + 1, $window);
    return true;
}

/**
 * Handle RSVP REST submission.
 *
 * @param WP_REST_Request $request The REST request.
 * @return WP_REST_Response|WP_Error Response object.
 */
function weddingblocks_handle_rsvp_submission($request)
{
    $params = $request->get_params();

    $client_ip = weddingblocks_get_client_ip();
    if (! weddingblocks_rsvp_rate_limit_ok($client_ip)) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => __('Terlalu banyak percobaan. Silakan coba lagi dalam beberapa menit.', 'weddingblocks'),
        ), 429);
    }

    $post_id      = isset($params['post_id']) ? absint($params['post_id']) : 0;
    $guest_name   = isset($params['guest_name']) ? sanitize_text_field($params['guest_name']) : '';
    $attendance   = isset($params['attendance']) ? sanitize_text_field($params['attendance']) : '';
    $guests_count = isset($params['guests_count']) ? intval($params['guests_count']) : 1;
    $message      = isset($params['message']) ? sanitize_textarea_field($params['message']) : '';

    $max_guests = isset($params['max_guests']) ? absint($params['max_guests']) : 10;
    $max_guests = max(1, min(50, $max_guests));

    if (! $post_id || get_post_type($post_id) !== 'wdbl_undangan') {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => __('ID Undangan tidak valid.', 'weddingblocks'),
        ), 400);
    }

    if ('publish' !== get_post_status($post_id)) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => __('Undangan ini belum tersedia untuk menerima RSVP.', 'weddingblocks'),
        ), 400);
    }

    if (empty($guest_name)) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => __('Silakan isi nama Anda.', 'weddingblocks'),
        ), 400);
    }

    if (mb_strlen($guest_name) > 100) {
        $guest_name = mb_substr($guest_name, 0, 100);
    }

    if (! in_array($attendance, array('hadir', 'tidak_hadir', 'ragu_ragu'), true)) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => __('Pilihan kehadiran tidak valid.', 'weddingblocks'),
        ), 400);
    }

    if ('tidak_hadir' === $attendance) {
        $guests_count = 0;
    } else {
        $guests_count = max(1, min($max_guests, $guests_count));
    }

    if (mb_strlen($message) > 1000) {
        $message = mb_substr($message, 0, 1000);
    }
    $rsvp_data = array(
        'post_id'      => $post_id,
        'guest_name'   => $guest_name,
        'attendance'   => $attendance,
        'guests_count' => $guests_count,
        'message'      => $message,
    );

    $inserted_id = weddingblocks_save_rsvp($rsvp_data);

    if (! $inserted_id) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => __('Gagal menyimpan data RSVP. Silakan coba lagi.', 'weddingblocks'),
        ), 500);
    }

    do_action('weddingblocks_rsvp_submitted', $inserted_id, $rsvp_data);

    $formatted_time = __('Baru saja', 'weddingblocks');

    return new WP_REST_Response(array(
        'success' => true,
        'message' => __('Konfirmasi RSVP berhasil dikirim!', 'weddingblocks'),
        'data'    => array(
            'id'             => $inserted_id,
            'guest_name'     => $guest_name,
            'attendance'     => $attendance,
            'guests_count'   => $guests_count,
            'message'        => $message,
            'formatted_time' => $formatted_time,
        ),
    ), 200);
}
