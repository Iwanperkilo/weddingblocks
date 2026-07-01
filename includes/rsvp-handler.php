<?php
/**
 * RSVP Form AJAX/REST API Handler for WeddingBlocks.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register REST API route for RSVP.
 */
function weddingblocks_register_rsvp_route() {
    register_rest_route( 'weddingblocks/v1', '/rsvp', array(
        'methods'             => 'POST',
        'callback'            => 'weddingblocks_handle_rsvp_submission',
        'permission_callback' => 'weddingblocks_rsvp_permissions_check',
    ) );
}
add_action( 'rest_api_init', 'weddingblocks_register_rsvp_route' );

/**
 * Permission check for RSVP submissions (publicly open).
 */
function weddingblocks_rsvp_permissions_check() {
    return true; // Allow anyone to submit RSVP.
}

/**
 * Handle RSVP REST submission.
 *
 * @param WP_REST_Request $request The REST request.
 * @return WP_REST_Response|WP_Error Response object.
 */
function weddingblocks_handle_rsvp_submission( $request ) {
    $params = $request->get_params();

    // 1. Validation & Sanitization.
    $post_id      = isset( $params['post_id'] ) ? intval( $params['post_id'] ) : 0;
    $guest_name   = isset( $params['guest_name'] ) ? sanitize_text_field( $params['guest_name'] ) : '';
    $attendance   = isset( $params['attendance'] ) ? sanitize_text_field( $params['attendance'] ) : '';
    $guests_count = isset( $params['guests_count'] ) ? intval( $params['guests_count'] ) : 1;
    $message      = isset( $params['message'] ) ? sanitize_textarea_field( $params['message'] ) : '';

    if ( ! $post_id || get_post_type( $post_id ) !== 'undangan' ) {
        return new WP_REST_Response( array(
            'success' => false,
            'message' => __( 'ID Undangan tidak valid.', 'weddingblocks' )
        ), 400 );
    }

    if ( empty( $guest_name ) ) {
        return new WP_REST_Response( array(
            'success' => false,
            'message' => __( 'Silakan isi nama Anda.', 'weddingblocks' )
        ), 400 );
    }

    if ( ! in_array( $attendance, array( 'hadir', 'tidak_hadir', 'ragu_ragu' ) ) ) {
        return new WP_REST_Response( array(
            'success' => false,
            'message' => __( 'Pilihan kehadiran tidak valid.', 'weddingblocks' )
        ), 400 );
    }

    if ( $attendance === 'tidak_hadir' ) {
        $guests_count = 0;
    } elseif ( $guests_count < 1 ) {
        $guests_count = 1;
    }

    // 2. Save in database.
    $rsvp_data = array(
        'post_id'      => $post_id,
        'guest_name'   => $guest_name,
        'attendance'   => $attendance,
        'guests_count' => $guests_count,
        'message'      => $message,
    );

    $inserted_id = weddingblocks_save_rsvp( $rsvp_data );

    if ( ! $inserted_id ) {
        return new WP_REST_Response( array(
            'success' => false,
            'message' => __( 'Gagal menyimpan data RSVP. Silakan coba lagi.', 'weddingblocks' )
        ), 500 );
    }

    // Pro Hook: Allow Pro version to hook into RSVP submission (e.g. for WhatsApp notifications).
    do_action( 'weddingblocks_rsvp_submitted', $inserted_id, $rsvp_data );

    // 3. Return response.
    $formatted_time = human_time_diff( current_time( 'timestamp' ), current_time( 'timestamp' ) ) . ' ' . __( 'yang lalu', 'weddingblocks' );
    
    return new WP_REST_Response( array(
        'success'  => true,
        'message'  => __( 'Konfirmasi RSVP berhasil dikirim!', 'weddingblocks' ),
        'data'     => array(
            'id'             => $inserted_id,
            'guest_name'     => $guest_name,
            'attendance'     => $attendance,
            'guests_count'   => $guests_count,
            'message'        => esc_html( $message ),
            'formatted_time' => $formatted_time,
        )
    ), 200 );
}
