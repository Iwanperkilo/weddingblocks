<?php
/**
 * Server-side rendering for the Cover block.
 *
 * @package WeddingBlocks
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$bg_image = ! empty( $attributes['backgroundImage'] ) ? esc_url( $attributes['backgroundImage'] ) : '';
$button_text = ! empty( $attributes['buttonText'] ) ? wp_kses_post( $attributes['buttonText'] ) : __( 'Buka Undangan', 'weddingblocks' );
$button_border_radius = isset( $attributes['buttonBorderRadius'] ) ? intval( $attributes['buttonBorderRadius'] ) : 30;

// Style attributes
$overlay_opacity = isset( $attributes['overlayOpacity'] ) ? intval( $attributes['overlayOpacity'] ) : 35;
$overlay_color = ! empty( $attributes['overlayColor'] ) ? sanitize_hex_color( $attributes['overlayColor'] ) : '#000000';
$accent_color = ! empty( $attributes['accentColor'] ) ? sanitize_hex_color( $attributes['accentColor'] ) : '#b5a46d';

$bg_style = $bg_image ? "background-image: url('$bg_image');" : "";
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
