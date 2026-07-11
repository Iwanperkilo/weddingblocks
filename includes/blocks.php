<?php
/**
 * Gutenberg Block Registration and Asset Loading for WeddingBlocks.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register post meta fields for CPT 'undangan'.
 */
function weddingblocks_register_post_meta() {
    $meta_fields = array(
        'weddingblocks_groom_name'                => 'string',
        'weddingblocks_groom_nickname'            => 'string',
        'weddingblocks_groom_parents'             => 'string',
        'weddingblocks_groom_photo'               => 'string',
        'weddingblocks_bride_name'                => 'string',
        'weddingblocks_bride_nickname'            => 'string',
        'weddingblocks_bride_parents'             => 'string',
        'weddingblocks_bride_photo'               => 'string',
        'weddingblocks_wedding_date'              => 'string',
        'weddingblocks_akad_time_label'           => 'string',
        'weddingblocks_akad_location_name'        => 'string',
        'weddingblocks_akad_location_address'     => 'string',
        'weddingblocks_resepsi_time_label'        => 'string',
        'weddingblocks_resepsi_location_name'     => 'string',
        'weddingblocks_resepsi_location_address'  => 'string',
        'weddingblocks_maps_coords'               => 'string',
        'weddingblocks_whatsapp_number'           => 'string',
    );

    foreach ( $meta_fields as $meta_key => $type ) {
        register_post_meta( 'wdbl_undangan', $meta_key, array(
            'show_in_rest'      => true,
            'single'            => true,
            'type'              => $type,
            'sanitize_callback' => ( $meta_key === 'weddingblocks_akad_location_address' || $meta_key === 'weddingblocks_resepsi_location_address' ) ? 'sanitize_textarea_field' : 'sanitize_text_field',
        ) );
    }
}
add_action( 'init', 'weddingblocks_register_post_meta' );

/**
 * Register blocks, scripts, and styles.
 */
/**
 * Register custom block category for WeddingBlocks.
 */
function weddingblocks_register_block_category( $categories, $post ) {
    return array_merge(
        $categories,
        array(
            array(
                'slug'  => 'weddingblocks',
                'title' => __( 'Wedding Blocks', 'weddingblocks' ),
                'icon'  => 'heart',
            ),
        )
    );
}
add_filter( 'block_categories_all', 'weddingblocks_register_block_category', 10, 2 );

function weddingblocks_register_blocks() {
    // 1. Register Scripts.
    wp_register_script(
        'weddingblocks-editor-script',
        WEDDINGBLOCKS_URL . 'assets/js/blocks-editor.js',
        array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n', 'wp-edit-post', 'wp-plugins' ),
        WEDDINGBLOCKS_VERSION,
        true
    );

    wp_register_script(
        'weddingblocks-frontend-script',
        WEDDINGBLOCKS_URL . 'assets/js/blocks-frontend.js',
        array(),
        WEDDINGBLOCKS_VERSION,
        true
    );

    // Inject API endpoint URL into frontend script.
    wp_localize_script( 'weddingblocks-frontend-script', 'weddingblocks_api', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'rest_url' => esc_url_raw( rest_url( 'weddingblocks/v1' ) ),
        'nonce'    => wp_create_nonce( 'wp_rest' ),
    ) );

    // 2. Register Styles.
    wp_register_style(
        'weddingblocks-editor-style',
        WEDDINGBLOCKS_URL . 'assets/css/blocks-editor.css',
        array(),
        WEDDINGBLOCKS_VERSION
    );

    wp_register_style(
        'weddingblocks-frontend-style',
        WEDDINGBLOCKS_URL . 'assets/css/blocks-frontend.css',
        array(),
        WEDDINGBLOCKS_VERSION
    );
    wp_register_style(
        'weddingblocks-atomic-style',
        WEDDINGBLOCKS_URL . 'assets/css/atomic-blocks.css',
        array(),
        WEDDINGBLOCKS_VERSION
    );
    wp_register_style(
        'weddingblocks-editor-preview-style',
        WEDDINGBLOCKS_URL . 'assets/css/editor-preview.css',
        array(),
        WEDDINGBLOCKS_VERSION
    );
    
    // 3. Register blocks from their block.json files.
    $blocks = array(
        'countdown',
        'couple-info',
        'couple-name',
        'couple-parents',
        'couple-photo',
        'couple-title',
        'cover',
        'guest-name',
        'music-player',
        'event-info',
        'rsvp-form',
        'guestbook'
    );
    foreach ( $blocks as $block ) {
        $block_path = WEDDINGBLOCKS_PATH . 'includes/blocks/' . $block;
        if ( file_exists( $block_path . '/block.json' ) ) {
            register_block_type( $block_path, [
                'editor_script' => 'weddingblocks-editor-script',
                'editor_style'  => 'weddingblocks-editor-style',
                'style'         => 'weddingblocks-frontend-style',
                'script'        => 'weddingblocks-frontend-script',
            ] );
        }
    }
}
add_action( 'init', 'weddingblocks_register_blocks' );

/**
 * Enqueue editor-preview.css and editor setting panel in block editor.
 */
function weddingblocks_enqueue_editor_preview_assets() {
    wp_enqueue_style( 'weddingblocks-editor-preview-style' );
    wp_enqueue_script( 'weddingblocks-editor-script' );
}
add_action( 'enqueue_block_editor_assets', 'weddingblocks_enqueue_editor_preview_assets' );