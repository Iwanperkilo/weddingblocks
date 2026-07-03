<?php
/**
 * Server-side rendering for the Couple Parents block.
 *
 * @package WeddingBlocks
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
$role       = isset( $attributes['role'] ) ? sanitize_key( $attributes['role'] ) : 'groom';
$label      = isset( $attributes['label'] ) ? $attributes['label'] : '';
$show_label = isset( $attributes['showLabel'] ) ? (bool) $attributes['showLabel'] : true;
$align      = isset( $attributes['align'] ) ? sanitize_key( $attributes['align'] ) : 'center';
$align      = in_array( $align, array( 'left', 'center', 'right' ), true ) ? $align : 'center';
// Style attributes
$font_size   = isset( $attributes['fontSize'] ) ? intval( $attributes['fontSize'] ) : 17;
$font_family = isset( $attributes['fontFamily'] ) ? sanitize_text_field( $attributes['fontFamily'] ) : 'playfair';
$text_color  = isset( $attributes['textColor'] ) ? sanitize_hex_color( $attributes['textColor'] ) : '#333333';
if ( ! $text_color ) {
    $text_color = '#333333';
}
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
// Font family mapping
$font_family_css = "Georgia, serif";
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
$style_attr = sprintf(
    'font-size:%1$dpx; font-family:%2$s; color:%3$s;',
    $font_size,
    $font_family_css,
    $text_color
);
$wrapper_class = 'weddingblocks-atomic-couple-parents role-' . sanitize_html_class( $role ) . ' align-' . sanitize_html_class( $align );
?>
<div class="<?php echo esc_attr( $wrapper_class ); ?>">
    <?php if ( $show_label ) : ?>
        <span class="atomic-parents-label" style="<?php echo esc_attr( $style_attr ); ?>"><?php echo esc_html( $label ); ?></span>
    <?php endif; ?>
    <span class="atomic-parents-names" style="<?php echo esc_attr( $style_attr ); ?>"><?php echo esc_html( $parents ); ?></span>
</div>
