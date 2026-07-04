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
        register_post_meta( 'undangan', $meta_key, array(
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

    // 3. Register Block Types. (Semua blok didaftarkan melalui block.json)
    // Tidak perlu lagi memanggil register_block_type secara manual untuk setiap blok di sini.
    
    // Register blocks from their block.json files.
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

/**
 * Render Callbacks for Custom Blocks
 */

// 1. Music Player
function weddingblocks_render_music_player( $attributes ) {
    $music_url = ! empty( $attributes['musicUrl'] ) ? esc_url( $attributes['musicUrl'] ) : 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3';
    ob_start();
    ?>
    <div class="weddingblocks-music-container" data-music-url="<?php echo esc_attr( $music_url ); ?>">
        <audio id="weddingblocks-audio" loop preload="auto">
            <source src="<?php echo esc_attr( $music_url ); ?>" type="audio/mpeg">
        </audio>
        <button id="weddingblocks-music-toggle" class="weddingblocks-music-btn paused" aria-label="Toggle Music" style="display: none;">
            <svg class="icon-play" viewBox="0 0 24 24" width="24" height="24" fill="currentColor">
                <path d="M8 5v14l11-7z"/>
            </svg>
            <svg class="icon-pause" viewBox="0 0 24 24" width="24" height="24" fill="currentColor" style="display:none;">
                <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>
            </svg>
        </button>
    </div>
    <?php
    return ob_get_clean();
}

function weddingblocks_render_couple_name( $attributes ) {
    $role       = isset( $attributes['role'] ) ? sanitize_key( $attributes['role'] ) : 'groom';
    $name_type  = isset( $attributes['nameType'] ) ? sanitize_key( $attributes['nameType'] ) : 'full';
    $align      = isset( $attributes['align'] ) ? sanitize_key( $attributes['align'] ) : 'center';
    $align      = in_array( $align, array( 'left', 'center', 'right' ), true ) ? $align : 'center';

    // Style attributes
    $font_size      = isset( $attributes['fontSize'] ) ? intval( $attributes['fontSize'] ) : 32;
    $font_family    = isset( $attributes['fontFamily'] ) ? sanitize_text_field( $attributes['fontFamily'] ) : 'playfair';
    $text_color     = isset( $attributes['textColor'] ) ? sanitize_hex_color( $attributes['textColor'] ) : '#b5a46d';
    $text_transform = isset( $attributes['textTransform'] ) ? sanitize_key( $attributes['textTransform'] ) : 'none';

    $full_name  = '';
    $nick_name  = '';
    $fallback   = '';
    if ( 'bride' === $role ) {
        $full_name = ! empty( $attributes['brideName'] ) ? $attributes['brideName'] : get_post_meta( get_the_ID(), 'weddingblocks_bride_name', true );
        $nick_name = ! empty( $attributes['brideNickname'] ) ? $attributes['brideNickname'] : get_post_meta( get_the_ID(), 'weddingblocks_bride_nickname', true );
        $fallback  = __( 'Mempelai Wanita', 'weddingblocks' );
    } else {
        $full_name = ! empty( $attributes['groomName'] ) ? $attributes['groomName'] : get_post_meta( get_the_ID(), 'weddingblocks_groom_name', true );
        $nick_name = ! empty( $attributes['groomNickname'] ) ? $attributes['groomNickname'] : get_post_meta( get_the_ID(), 'weddingblocks_groom_nickname', true );
        $fallback  = __( 'Mempelai Pria', 'weddingblocks' );
    }

    if ( 'nickname' === $name_type ) {
        $display = '' !== $nick_name ? $nick_name : $full_name;
        if ( '' === $display ) { $display = $fallback; }
    } elseif ( 'both' === $name_type ) {
        $display = trim( $nick_name . ' ' . $full_name );
        if ( '' === $display ) { $display = $fallback; }
    } else {
        $display = '' !== $full_name ? $full_name : ( '' !== $nick_name ? $nick_name : $fallback );
    }

    $wrapper_class = 'weddingblocks-atomic-couple-name role-' . sanitize_html_class( $role ) . ' type-' . sanitize_html_class( $name_type ) . ' align-' . sanitize_html_class( $align );

    // Font Family Mapping
    $font_family_css = "serif";
    if ( 'playfair' === $font_family ) {
        $font_family_css = "'Playfair Display', Georgia, serif";
    } elseif ( 'greatvibes' === $font_family ) {
        $font_family_css = "'Great Vibes', cursive";
    } elseif ( 'montserrat' === $font_family ) {
        $font_family_css = "'Montserrat', sans-serif";
    } elseif ( 'georgia' === $font_family ) {
        $font_family_css = "Georgia, serif";
    } elseif ( 'sans-serif' === $font_family ) {
        $font_family_css = "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif";
    }

    // Build inline style
    $style_attr = sprintf(
        'font-size:%1$dpx; font-family:%2$s; color:%3$s; text-transform:%4$s;',
        $font_size,
        $font_family_css,
        $text_color,
        $text_transform
    );

    ob_start();
    ?>
    <div class="<?php echo esc_attr( $wrapper_class ); ?>">
        <span class="atomic-name-text" style="<?php echo esc_attr( $style_attr ); ?>"><?php echo esc_html( $display ); ?></span>
    </div>
    <?php
    return ob_get_clean();
}

function weddingblocks_render_couple_parents( $attributes ) {
    $role       = isset( $attributes['role'] ) ? sanitize_key( $attributes['role'] ) : 'groom';
    $label      = isset( $attributes['label'] ) ? $attributes['label'] : '';
    $show_label = isset( $attributes['showLabel'] ) ? (bool) $attributes['showLabel'] : true;
    $align      = isset( $attributes['align'] ) ? sanitize_key( $attributes['align'] ) : 'center';
    $align      = in_array( $align, array( 'left', 'center', 'right' ), true ) ? $align : 'center';

    if ( 'bride' === $role ) {
        $parents  = ! empty( $attributes['brideParents'] ) ? $attributes['brideParents'] : get_post_meta( get_the_ID(), 'weddingblocks_bride_parents', true );
        $default_label = __( 'Putri dari', 'weddingblocks' );
        $fallback       = __( 'Bapak & Ibu Orang Tua Wanita', 'weddingblocks' );
    } else {
        $parents  = ! empty( $attributes['groomParents'] ) ? $attributes['groomParents'] : get_post_meta( get_the_ID(), 'weddingblocks_groom_parents', true );
        $default_label = __( 'Putra dari', 'weddingblocks' );
        $fallback       = __( 'Bapak & Ibu Orang Tua Pria', 'weddingblocks' );
    }

    if ( '' === $parents ) { $parents = $fallback; }
    if ( '' === $label )   { $label   = $default_label; }

    $wrapper_class = 'weddingblocks-atomic-couple-parents role-' . sanitize_html_class( $role ) . ' align-' . sanitize_html_class( $align );

    ob_start();
    ?>
    <div class="<?php echo esc_attr( $wrapper_class ); ?>">
        <?php if ( $show_label ) : ?>
            <span class="atomic-parents-label"><?php echo esc_html( $label ); ?></span>
        <?php endif; ?>
        <span class="atomic-parents-names"><?php echo esc_html( $parents ); ?></span>
    </div>
    <?php
    return ob_get_clean();
}

function weddingblocks_render_couple_photo( $attributes ) {
    $role       = isset( $attributes['role'] ) ? sanitize_key( $attributes['role'] ) : 'groom';
    $shape      = isset( $attributes['shape'] ) ? sanitize_key( $attributes['shape'] ) : 'circle';
    $shape      = in_array( $shape, array( 'circle', 'rounded', 'square' ), true ) ? $shape : 'circle';
    $size       = isset( $attributes['size'] ) ? (int) $attributes['size'] : 200;
    if ( $size < 40 )  { $size = 40; }
    if ( $size > 800 ) { $size = 800; }
    $show_frame = isset( $attributes['showFrame'] ) ? (bool) $attributes['showFrame'] : true;
    $align      = isset( $attributes['align'] ) ? sanitize_key( $attributes['align'] ) : 'center';
    $align      = in_array( $align, array( 'left', 'center', 'right' ), true ) ? $align : 'center';

    if ( 'bride' === $role ) {
        $photo     = ! empty( $attributes['bridePhoto'] ) ? $attributes['bridePhoto'] : get_post_meta( get_the_ID(), 'weddingblocks_bride_photo', true );
        $name      = ! empty( $attributes['brideName'] )  ? $attributes['brideName']  : get_post_meta( get_the_ID(), 'weddingblocks_bride_name', true );
        $fallback  = __( 'Mempelai Wanita', 'weddingblocks' );
    } else {
        $photo     = ! empty( $attributes['groomPhoto'] ) ? $attributes['groomPhoto'] : get_post_meta( get_the_ID(), 'weddingblocks_groom_photo', true );
        $name      = ! empty( $attributes['groomName'] )  ? $attributes['groomName']  : get_post_meta( get_the_ID(), 'weddingblocks_groom_name', true );
        $fallback  = __( 'Mempelai Pria', 'weddingblocks' );
    }
    if ( '' === $name ) { $name = $fallback; }

    $placeholder_svg = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%23b5a46d"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>';

    $inline_style = sprintf( 'width:%1$dpx;height:%1$dpx;', $size );
    $frame_class  = $show_frame ? ' has-frame' : ' no-frame';
    $wrapper_class = 'weddingblocks-atomic-couple-photo role-' . sanitize_html_class( $role ) . ' shape-' . sanitize_html_class( $shape ) . ' align-' . sanitize_html_class( $align ) . $frame_class;

    ob_start();
    ?>
    <div class="<?php echo esc_attr( $wrapper_class ); ?>">
        <figure class="atomic-photo" style="<?php echo esc_attr( $inline_style ); ?>">
            <?php if ( '' !== $photo ) : ?>
                <img src="<?php echo esc_url( $photo ); ?>" alt="<?php echo esc_attr( $name ); ?>" />
            <?php else : ?>
                <img src="<?php echo esc_url( $placeholder_svg ); ?>" alt="<?php echo esc_attr( $name ); ?>" class="atomic-photo-placeholder" />
            <?php endif; ?>
        </figure>
    </div>
    <?php
    return ob_get_clean();
}

// 2. Cover
function weddingblocks_render_cover( $attributes, $content = '' ) {
    $bg_image = ! empty( $attributes['backgroundImage'] ) ? esc_url( $attributes['backgroundImage'] ) : '';
    $button_text = ! empty( $attributes['buttonText'] ) ? wp_kses_post( $attributes['buttonText'] ) : __( 'Buka Undangan', 'weddingblocks' );
    $button_border_radius = isset( $attributes['buttonBorderRadius'] ) ? intval( $attributes['buttonBorderRadius'] ) : 30;

    // Style attributes
    $overlay_opacity = isset( $attributes['overlayOpacity'] ) ? intval( $attributes['overlayOpacity'] ) : 35;
    $overlay_color = ! empty( $attributes['overlayColor'] ) ? sanitize_hex_color( $attributes['overlayColor'] ) : '#000000';
    $accent_color = ! empty( $attributes['accentColor'] ) ? sanitize_hex_color( $attributes['accentColor'] ) : '#b5a46d';

    $bg_style = $bg_image ? "background-image: url('$bg_image');" : "";

    ob_start();
    ?>
    <div id="weddingblocks-cover" class="weddingblocks-cover-wrapper" style="<?php echo esc_attr( $bg_style ); ?>">
        <div class="weddingblocks-cover-overlay" style="background-color: <?php echo esc_attr( $overlay_color ); ?>; opacity: <?php echo esc_attr( $overlay_opacity / 100 ); ?>;"></div>
        <div class="weddingblocks-cover-content">
            <?php echo $content; ?>

            <button id="weddingblocks-open-btn" class="weddingblocks-btn-gold" style="background-color: <?php echo esc_attr( $accent_color ); ?>; border-color: <?php echo esc_attr( $accent_color ); ?>; color: #ffffff !important; border-radius: <?php echo esc_attr( $button_border_radius ); ?>px;">
                <svg class="icon-mail" viewBox="0 0 24 24" width="20" height="20" fill="currentColor" style="margin-right: 8px; vertical-align: middle;">
                    <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                </svg>
                <span><?php echo $button_text; ?></span>
            </button>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

// 3. Guest Name
function weddingblocks_render_guest_name( $attributes ) {
    $prefix = isset( $attributes['prefix'] ) ? esc_html( $attributes['prefix'] ) : 'Kepada Yth. Bapak/Ibu/Saudara/i:';
    $fallback = isset( $attributes['fallback'] ) ? esc_html( $attributes['fallback'] ) : 'Tamu Undangan';
    $guest_name = isset( $_GET['to'] ) ? sanitize_text_field( $_GET['to'] ) : $fallback;

    ob_start();
    ?>
    <div class="weddingblocks-guest-name-block">
        <p class="guest-prefix-text"><?php echo esc_html( $prefix ); ?></p>
        <h4 class="guest-name-text"><?php echo esc_html( $guest_name ); ?></h4>
    </div>
    <?php
    return ob_get_clean();
}

// 4. Countdown
function weddingblocks_render_countdown( $attributes ) {
    $target = '';
    if ( ! empty( $attributes['targetDate'] ) ) {
        $target = $attributes['targetDate'];
    } else {
        $target = get_post_meta( get_the_ID(), 'weddingblocks_wedding_date', true );
    }
    if ( empty( $target ) ) {
        $target = '2026-12-31T09:00';
    }
    $target = esc_attr( $target );

    ob_start();
    ?>
    <div class="weddingblocks-countdown" data-target="<?php echo esc_attr( $target ); ?>">
        <div class="countdown-item">
            <span class="countdown-value days">00</span>
            <span class="countdown-label"><?php _e( 'Hari', 'weddingblocks' ); ?></span>
        </div>
        <div class="countdown-item">
            <span class="countdown-value hours">00</span>
            <span class="countdown-label"><?php _e( 'Jam', 'weddingblocks' ); ?></span>
        </div>
        <div class="countdown-item">
            <span class="countdown-value minutes">00</span>
            <span class="countdown-label"><?php _e( 'Menit', 'weddingblocks' ); ?></span>
        </div>
        <div class="countdown-item">
            <span class="countdown-value seconds">00</span>
            <span class="countdown-label"><?php _e( 'Detik', 'weddingblocks' ); ?></span>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

// 7. Couple Title
function weddingblocks_render_couple_title( $attributes ) {
    $groom_display = get_post_meta( get_the_ID(), 'weddingblocks_groom_nickname', true );
    if ( empty( $groom_display ) ) {
        $groom_display = get_post_meta( get_the_ID(), 'weddingblocks_groom_name', true );
    }
    if ( empty( $groom_display ) ) {
        $groom_display = __( 'Mempelai Pria', 'weddingblocks' );
    }

    $bride_display = get_post_meta( get_the_ID(), 'weddingblocks_bride_nickname', true );
    if ( empty( $bride_display ) ) {
        $bride_display = get_post_meta( get_the_ID(), 'weddingblocks_bride_name', true );
    }
    if ( empty( $bride_display ) ) {
        $bride_display = __( 'Mempelai Wanita', 'weddingblocks' );
    }

    ob_start();
    ?>
    <h1 class="weddingblocks-cover-title text-align-center" style="text-align: center;">
        <?php echo esc_html( $groom_display ) . ' & ' . esc_html( $bride_display ); ?>
    </h1>
    <?php
    return ob_get_clean();
}

// 8. Couple Info
function weddingblocks_render_couple_info( $attributes ) {
    $groom_name = get_post_meta( get_the_ID(), 'weddingblocks_groom_name', true );
    $groom_parents = get_post_meta( get_the_ID(), 'weddingblocks_groom_parents', true );
    $groom_photo = get_post_meta( get_the_ID(), 'weddingblocks_groom_photo', true );

    $bride_name = get_post_meta( get_the_ID(), 'weddingblocks_bride_name', true );
    $bride_parents = get_post_meta( get_the_ID(), 'weddingblocks_bride_parents', true );
    $bride_photo = get_post_meta( get_the_ID(), 'weddingblocks_bride_photo', true );

    // Fallbacks
    if ( empty( $groom_name ) ) $groom_name = __( 'Mempelai Pria', 'weddingblocks' );
    if ( empty( $groom_parents ) ) $groom_parents = __( 'Putra dari Bapak & Ibu Orang Tua Pria', 'weddingblocks' );
    if ( empty( $bride_name ) ) $bride_name = __( 'Mempelai Wanita', 'weddingblocks' );
    if ( empty( $bride_parents ) ) $bride_parents = __( 'Putri dari Bapak & Ibu Orang Tua Wanita', 'weddingblocks' );

    $placeholder_svg = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%23b5a46d"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>';

    $groom_photo_url = ! empty( $groom_photo ) ? esc_url( $groom_photo ) : $placeholder_svg;
    $bride_photo_url = ! empty( $bride_photo ) ? esc_url( $bride_photo ) : $placeholder_svg;
    $name_font_size = isset( $attributes['nameFontSize'] ) ? intval( $attributes['nameFontSize'] ) : 24;
    $name_style_attr = sprintf( 'font-size: %dpx;', $name_font_size );

    ob_start();
    ?>
    <div class="weddingblocks-couple-columns">
        <div class="weddingblocks-couple-column">
            <div class="weddingblocks-avatar">
                <img src="<?php echo esc_url( $bride_photo_url ); ?>" alt="<?php echo esc_attr( $bride_name ); ?>">
            </div>
            <h3 style="<?php echo esc_attr( $name_style_attr ); ?>"><?php echo esc_html( $bride_name ); ?></h3>
            <p><?php echo esc_html( $bride_parents ); ?></p>
        </div>
        <div class="weddingblocks-couple-column weddingblocks-separator-column">
            <p class="weddingblocks-ampersand">&</p>
        </div>
        <div class="weddingblocks-couple-column">
            <div class="weddingblocks-avatar">
                <img src="<?php echo esc_url( $groom_photo_url ); ?>" alt="<?php echo esc_attr( $groom_name ); ?>">
            </div>
            <h3 style="<?php echo esc_attr( $name_style_attr ); ?>"><?php echo esc_html( $groom_name ); ?></h3>
            <p><?php echo esc_html( $groom_parents ); ?></p>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

// 9. Event Info
function weddingblocks_render_event_info( $attributes ) {
    $akad_time = get_post_meta( get_the_ID(), 'weddingblocks_akad_time_label', true );
    $akad_loc_name = get_post_meta( get_the_ID(), 'weddingblocks_akad_location_name', true );
    $akad_loc_addr = get_post_meta( get_the_ID(), 'weddingblocks_akad_location_address', true );

    $resepsi_time = get_post_meta( get_the_ID(), 'weddingblocks_resepsi_time_label', true );
    $resepsi_loc_name = get_post_meta( get_the_ID(), 'weddingblocks_resepsi_location_name', true );
    $resepsi_loc_addr = get_post_meta( get_the_ID(), 'weddingblocks_resepsi_location_address', true );

    $maps_coords = get_post_meta( get_the_ID(), 'weddingblocks_maps_coords', true );
    $whatsapp = get_post_meta( get_the_ID(), 'weddingblocks_whatsapp_number', true );

    // Fallbacks
    if ( empty( $akad_time ) ) $akad_time = 'Pukul 08:00 - 10:00 WIB';
    if ( empty( $akad_loc_name ) ) $akad_loc_name = 'Masjid Agung Kota';
    if ( empty( $akad_loc_addr ) ) $akad_loc_addr = 'Jl. Cempaka No. 12';

    if ( empty( $resepsi_time ) ) $resepsi_time = 'Pukul 11:00 - Selesai';
    if ( empty( $resepsi_loc_name ) ) $resepsi_loc_name = 'Gedung Serbaguna Indah';
    if ( empty( $resepsi_loc_addr ) ) $resepsi_loc_addr = 'Jl. Melati No. 45';

    ob_start();
    ?>
    <div class="weddingblocks-event-columns">
        <div class="weddingblocks-event-card">
            <h3><?php _e( 'Akad Nikah', 'weddingblocks' ); ?></h3>
            <p>
                <strong><?php echo esc_html( $akad_time ); ?></strong><br>
                <?php echo esc_html( $akad_loc_name ); ?><br>
                <?php echo esc_html( $akad_loc_addr ); ?>
            </p>
            <?php if ( ! empty( $maps_coords ) ) : ?>
                <div class="weddingblocks-event-actions" style="margin-top: 15px;">
                    <a href="<?php echo esc_url( $maps_coords ); ?>" target="_blank" rel="noopener noreferrer" class="weddingblocks-btn-gold" style="padding: 8px 16px; font-size: 11px; border-radius: 20px;">
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor" style="vertical-align: middle; margin-right: 4px;">
                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                        </svg>
                        <?php _e( 'Google Maps', 'weddingblocks' ); ?>
                    </a>
                </div>
            <?php endif; ?>
        </div>
        <div class="weddingblocks-event-card">
            <h3><?php _e( 'Resepsi', 'weddingblocks' ); ?></h3>
            <p>
                <strong><?php echo esc_html( $resepsi_time ); ?></strong><br>
                <?php echo esc_html( $resepsi_loc_name ); ?><br>
                <?php echo esc_html( $resepsi_loc_addr ); ?>
            </p>
            <?php if ( ! empty( $maps_coords ) ) : ?>
                <div class="weddingblocks-event-actions" style="margin-top: 15px;">
                    <a href="<?php echo esc_url( $maps_coords ); ?>" target="_blank" rel="noopener noreferrer" class="weddingblocks-btn-gold" style="padding: 8px 16px; font-size: 11px; border-radius: 20px;">
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor" style="vertical-align: middle; margin-right: 4px;">
                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                        </svg>
                        <?php _e( 'Google Maps', 'weddingblocks' ); ?>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php if ( ! empty( $whatsapp ) ) :
        // Format whatsapp link
        $wa_clean = preg_replace( '/[^0-9]/', '', $whatsapp );
        if ( strpos( $wa_clean, '0' ) === 0 ) {
            $wa_clean = '62' . substr( $wa_clean, 1 );
        }
        $wa_url = 'https://wa.me/' . $wa_clean;
        ?>
        <div class="weddingblocks-wa-action" style="margin: 25px auto 0; text-align: center;">
            <a href="<?php echo esc_url( $wa_url ); ?>" target="_blank" rel="noopener noreferrer" class="weddingblocks-btn-gold" style="background-color: #25D366; border-color: #25D366; color: #ffffff !important; box-shadow: 0 4px 15px rgba(37, 211, 102, 0.2);">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" style="vertical-align: middle; margin-right: 6px;">
                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.517 2.266 2.27 3.51 5.276 3.508 8.48-.005 6.66-5.342 11.997-11.953 11.997-2.005-.001-3.973-.503-5.733-1.458L0 24zm6.59-4.846c1.6.95 3.188 1.449 4.825 1.451 5.436 0 9.858-4.42 9.862-9.864.002-2.637-1.017-5.114-2.873-6.973C16.597 1.91 14.12 .89 11.48.887c-5.441 0-9.863 4.422-9.867 9.868-.002 1.77.464 3.498 1.353 5.031L1.93 21.09l5.059-1.328c1.558.85 3.238 1.293 4.908 1.292h.001zM17.65 14.28c-.319-.16-1.89-.933-2.21-1.049-.32-.116-.552-.174-.784.174-.232.348-.898 1.132-1.101 1.364-.203.232-.406.261-.726.101-1.821-.913-3.003-1.83-4.2-3.89-.319-.549.319-.51.913-1.7.093-.188.046-.352-.023-.512-.069-.16-.552-1.332-.756-1.82-.2-.48-.403-.414-.552-.422-.143-.007-.308-.009-.472-.009-.165 0-.435.062-.663.31-.228.249-.871.851-.871 2.074 0 1.223.89 2.406.99 2.541.101.135 1.751 2.674 4.241 3.746.592.255 1.055.408 1.417.523.595.19 1.137.163 1.564.099.477-.072 1.89-.773 2.158-1.52.268-.747.268-1.388.188-1.52-.08-.135-.299-.214-.619-.374z"/>
                </svg>
                <?php _e( 'Hubungi Admin (WhatsApp)', 'weddingblocks' ); ?>
            </a>
        </div>
    <?php endif; ?>
    <?php
    return ob_get_clean();
}


// 5. RSVP Form
function weddingblocks_render_rsvp_form( $attributes ) {
    $current_post_id = get_the_ID();
    $guest_name = isset( $_GET['to'] ) ? sanitize_text_field( $_GET['to'] ) : '';

    ob_start();
    ?>
    <div class="weddingblocks-rsvp-form-container">
        <form id="weddingblocks-rsvp-form" class="weddingblocks-form" data-post-id="<?php echo esc_attr( $current_post_id ); ?>">
            <div class="form-group">
                <label for="rsvp-name"><?php _e( 'Nama Anda', 'weddingblocks' ); ?></label>
                <input type="text" id="rsvp-name" name="guest_name" value="<?php echo esc_attr( $guest_name ); ?>" placeholder="<?php esc_attr_e( 'Ketik nama lengkap...', 'weddingblocks' ); ?>" required>
            </div>

            <div class="form-group">
                <label for="rsvp-attendance"><?php _e( 'Konfirmasi Kehadiran', 'weddingblocks' ); ?></label>
                <select id="rsvp-attendance" name="attendance" required>
                    <option value="" disabled selected><?php _e( 'Pilih kehadiran...', 'weddingblocks' ); ?></option>
                    <option value="hadir"><?php _e( 'Hadir', 'weddingblocks' ); ?></option>
                    <option value="tidak_hadir"><?php _e( 'Tidak Hadir', 'weddingblocks' ); ?></option>
                    <option value="ragu_ragu"><?php _e( 'Ragu-ragu', 'weddingblocks' ); ?></option>
                </select>
            </div>

            <div class="form-group" id="rsvp-guests-group" style="display: none;">
                <label for="rsvp-guests"><?php _e( 'Jumlah Tamu (Pax)', 'weddingblocks' ); ?></label>
                <input type="number" id="rsvp-guests" name="guests_count" value="1" min="1" max="10">
            </div>

            <div class="form-group">
                <label for="rsvp-message"><?php _e( 'Ucapan & Doa Restu', 'weddingblocks' ); ?></label>
                <textarea id="rsvp-message" name="message" rows="4" placeholder="<?php esc_attr_e( 'Berikan ucapan selamat & doa restu terbaik untuk mempelai...', 'weddingblocks' ); ?>" required></textarea>
            </div>

            <button type="submit" class="weddingblocks-btn-gold" id="rsvp-submit-btn">
                <span class="btn-text"><?php _e( 'Kirim Konfirmasi', 'weddingblocks' ); ?></span>
                <span class="btn-spinner" style="display: none;"></span>
            </button>
            <div id="rsvp-message-alert" class="rsvp-alert" style="display: none;"></div>
        </form>
    </div>
    <?php
    return ob_get_clean();
}

// 6. Guestbook
function weddingblocks_render_guestbook( $attributes ) {
    $current_post_id = get_the_ID();
    $rsvps = weddingblocks_get_rsvps( $current_post_id, 50, 0 );

    ob_start();
    ?>
    <div class="weddingblocks-guestbook-container">
        <div class="guestbook-list" id="weddingblocks-guestbook-list">
            <?php if ( ! empty( $rsvps ) ) : ?>
                <?php foreach ( $rsvps as $rsvp ) :
                    $formatted_time = human_time_diff( strtotime( $rsvp->created_at ), current_time( 'timestamp' ) ) . ' ' . __( 'yang lalu', 'weddingblocks' );

                    $badge_class = '';
                    $badge_label = '';
                    switch ( $rsvp->attendance ) {
                        case 'hadir':
                            $badge_class = 'badge-hadir';
                            $badge_label = __( 'Hadir', 'weddingblocks' );
                            break;
                        case 'tidak_hadir':
                            $badge_class = 'badge-tidak-hadir';
                            $badge_label = __( 'Tidak Hadir', 'weddingblocks' );
                            break;
                        case 'ragu_ragu':
                            $badge_class = 'badge-ragu';
                            $badge_label = __( 'Ragu-ragu', 'weddingblocks' );
                            break;
                    }
                    ?>
                    <div class="guestbook-item">
                        <div class="guestbook-header">
                            <h5 class="guest-name"><?php echo esc_html( $rsvp->guest_name ); ?></h5>
                            <span class="guest-status <?php echo esc_attr( $badge_class ); ?>"><?php echo esc_html( $badge_label ); ?></span>
                        </div>
                        <p class="guest-message"><?php echo nl2br( esc_html( $rsvp->message ) ); ?></p>
                        <span class="guest-time"><?php echo esc_html( $formatted_time ); ?></span>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="guestbook-empty" id="guestbook-empty-placeholder">
                    <p><?php _e( 'Belum ada ucapan. Jadilah yang pertama memberikan doa restu!', 'weddingblocks' ); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
