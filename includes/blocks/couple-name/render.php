<?php
/**
 * Server-side rendering for the Couple Name block.
 *
 * @package WeddingBlocks
 */

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$role       = isset( $attributes['role'] ) ? sanitize_key( $attributes['role'] ) : 'groom';
$name_type  = isset( $attributes['nameType'] ) ? sanitize_key( $attributes['nameType'] ) : 'full';
$align      = isset( $attributes['align'] ) ? sanitize_key( $attributes['align'] ) : 'center';

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

$wrapper_attributes = get_block_wrapper_attributes(
    array(
        'class' => $wrapper_class,
    )
);

?>
<div <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
    <span class="atomic-name-text" style="<?php echo esc_attr( $style_attr ); ?>"><?php echo esc_html( $display ); ?></span>
</div>
<?php

// Register the block
// The block registration will be handled by block.json
// add_action( 'init', 'weddingblocks_register_couple_name_block' );