<?php
/**
 * Custom Post Type and Template Registration for WeddingBlocks.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register CPT 'undangan'.
 */
function weddingblocks_register_cpt() {
    $labels = array(
        'name'               => _x( 'Undangan', 'post type general name', 'weddingblocks' ),
        'singular_name'      => _x( 'Undangan', 'post type singular name', 'weddingblocks' ),
        'menu_name'          => _x( 'Undangan', 'admin menu', 'weddingblocks' ),
        'name_admin_bar'     => _x( 'Undangan', 'add new on admin bar', 'weddingblocks' ),
        'add_new'            => _x( 'Tambah Baru', 'undangan', 'weddingblocks' ),
        'add_new_item'       => __( 'Tambah Undangan Baru', 'weddingblocks' ),
        'new_item'           => __( 'Undangan Baru', 'weddingblocks' ),
        'edit_item'          => __( 'Edit Undangan', 'weddingblocks' ),
        'view_item'          => __( 'Lihat Undangan', 'weddingblocks' ),
        'all_items'          => __( 'Semua Undangan', 'weddingblocks' ),
        'search_items'       => __( 'Cari Undangan', 'weddingblocks' ),
        'parent_item_colon'  => __( 'Induk Undangan:', 'weddingblocks' ),
        'not_found'          => __( 'Undangan tidak ditemukan.', 'weddingblocks' ),
        'not_found_in_trash' => __( 'Undangan tidak ditemukan di kotak sampah.', 'weddingblocks' ),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'undangan', 'with_front' => false ),
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'menu_position'      => 30,
        'menu_icon'          => 'dashicons-email-alt2',
        'show_in_rest'       => true, // Enables Gutenberg editor.
        'supports'           => array( 'title', 'editor', 'thumbnail', 'revisions', 'custom-fields' ),
        'template'           => array(
            array( 'weddingblocks/music-player', array() ),
            array( 'weddingblocks/cover', array(), array(
                array( 'core/paragraph', array( 'placeholder' => 'WALIMATUL \'URSY', 'className' => 'weddingblocks-cover-welcome', 'align' => 'center' ) ),
                array( 'weddingblocks/couple-title', array() ),
                array( 'weddingblocks/guest-name', array( 'prefix' => 'Kepada Yth. Bapak/Ibu/Saudara/i:', 'fallback' => 'Tamu Undangan' ) )
            ) ),
            array( 'core/group', array( 'tagName' => 'main', 'className' => 'weddingblocks-main-wrapper' ), array(
                array( 'core/group', array( 'className' => 'weddingblocks-section weddingblocks-intro-section' ), array(
                    array( 'core/heading', array( 'textAlign' => 'center', 'placeholder' => 'Mempelai', 'level' => 2, 'className' => 'weddingblocks-heading' ) ),
                    array( 'core/paragraph', array( 'align' => 'center', 'placeholder' => 'Dengan memohon rahmat Tuhan, kami mengundang Anda untuk merayakan kebahagiaan kami', 'className' => 'weddingblocks-subheading' ) ),
                    array( 'weddingblocks/couple-info', array() ),
                ) ),
                array( 'core/group', array( 'className' => 'weddingblocks-section weddingblocks-countdown-section' ), array(
                    array( 'core/heading', array( 'textAlign' => 'center', 'placeholder' => 'Menuju Hari Bahagia', 'level' => 2, 'className' => 'weddingblocks-heading' ) ),
                    array( 'weddingblocks/countdown', array() ),
                ) ),
                array( 'core/group', array( 'className' => 'weddingblocks-section weddingblocks-event-section' ), array(
                    array( 'core/heading', array( 'textAlign' => 'center', 'placeholder' => 'Acara & Waktu', 'level' => 2, 'className' => 'weddingblocks-heading' ) ),
                    array( 'weddingblocks/event-info', array() ),
                ) ),
                array( 'core/group', array( 'className' => 'weddingblocks-section weddingblocks-rsvp-section' ), array(
                    array( 'core/heading', array( 'textAlign' => 'center', 'placeholder' => 'Konfirmasi Kehadiran', 'level' => 2, 'className' => 'weddingblocks-heading' ) ),
                    array( 'weddingblocks/rsvp-form', array() ),
                ) ),
                array( 'core/group', array( 'className' => 'weddingblocks-section weddingblocks-guestbook-section' ), array(
                    array( 'core/heading', array( 'textAlign' => 'center', 'placeholder' => 'Ucapan & Doa Restu', 'level' => 2, 'className' => 'weddingblocks-heading' ) ),
                    array( 'weddingblocks/guestbook', array() ),
                ) ),
            ) ),
        ),
    );

    register_post_type( 'wdbl_undangan', $args );
}
add_action( 'init', 'weddingblocks_register_cpt' );

/**
 * Register FSE template for 'undangan' post type (WordPress 6.7+).
 */
function weddingblocks_register_block_template() {
    // Only register template if function exists (WP 6.7+).
    if ( ! function_exists( 'register_block_template' ) ) {
        return;
    }

    $template_file = WEDDINGBLOCKS_PATH . 'templates/single-undangan.html';
    if ( file_exists( $template_file ) ) {
        $template_content = file_get_contents( $template_file );
        // phpcs:ignore wp_function_not_compatible_with_requires_wp
        register_block_template( 'weddingblocks//single-undangan', array(
            'title'       => __( 'Template Undangan (WeddingBlocks)', 'weddingblocks' ),
            'description' => __( 'Template default halaman undangan digital bersih tanpa header/footer tema.', 'weddingblocks' ),
            'content'     => $template_content,
            'post_types'  => array( 'wdbl_undangan' ),
        ) );
    }
}
add_action( 'init', 'weddingblocks_register_block_template', 20 );

/**
 * Fallback for classic (non-block) themes to load a clean full-screen canvas.
 */
function weddingblocks_template_include( $template ) {
    if ( is_singular( 'wdbl_undangan' ) ) {
        // If not using a block theme, use classic-undangan.php to bypass theme header/footer.
        if ( ! function_exists( 'wp_is_block_theme' ) || ! wp_is_block_theme() ) {
            $fallback_file = WEDDINGBLOCKS_PATH . 'templates/classic-undangan.php';
            if ( file_exists( $fallback_file ) ) {
                return $fallback_file;
            }
        }
    }
    return $template;
}
add_filter( 'template_include', 'weddingblocks_template_include' );