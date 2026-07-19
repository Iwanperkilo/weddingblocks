<?php

/**
 * Server-side rendering for the Guest Name block.
 *
 * @package WeddingBlocks
 */

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals

if (! defined('ABSPATH')) {
    exit;
}

$prefix   = isset($attributes['prefix']) ? $attributes['prefix'] : 'Kepada Yth. Bapak/Ibu/Saudara/i:';
$fallback = isset($attributes['fallback']) ? $attributes['fallback'] : 'Tamu Undangan';
// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$guest_name = isset($_GET['to']) ? sanitize_text_field(wp_unslash($_GET['to'])) : $fallback;

$show_prefix = isset($attributes['showPrefix']) ? (bool) $attributes['showPrefix'] : true;
$text_align  = isset($attributes['textAlign']) ? $attributes['textAlign'] : 'center';
$font_size   = isset($attributes['fontSize']) ? $attributes['fontSize'] : 'medium';

// Whitelist supaya nilai attribute yang tidak valid tidak lolos ke output.
$allowed_aligns = array('left', 'center', 'right');
if (! in_array($text_align, $allowed_aligns, true)) {
    $text_align = 'center';
}

$font_size_map = array(
    'small'  => '15px',
    'medium' => '18px',
    'large'  => '24px',
);
$name_font_size = isset($font_size_map[$font_size]) ? $font_size_map[$font_size] : $font_size_map['medium'];

/**
 * Warna mendukung transparansi (alpha). Gunakan helper terpusat jika sudah
 * tersedia di includes/helpers.php, dengan fallback ke sanitize_hex_color()
 * (yang tidak mendukung alpha) untuk jaga-jaga.
 */
if (function_exists('weddingblocks_sanitize_color')) {
    $text_color       = weddingblocks_sanitize_color(isset($attributes['textColor']) ? $attributes['textColor'] : '#333333');
    $background_color = weddingblocks_sanitize_color(isset($attributes['backgroundColor']) ? $attributes['backgroundColor'] : 'transparent');
} else {
    $text_color       = isset($attributes['textColor']) ? sanitize_hex_color($attributes['textColor']) : '#333333';
    $background_color = isset($attributes['backgroundColor']) ? $attributes['backgroundColor'] : 'transparent';
}

if (empty($text_color)) {
    $text_color = '#333333';
}
if (empty($background_color)) {
    $background_color = 'transparent';
}
$anim_attrs = weddingblocks_get_animation_attrs( $attributes );
$anim_data  = '';
foreach ( $anim_attrs as $attr_key => $attr_val ) {
    $anim_data .= ' ' . esc_attr( $attr_key ) . '="' . esc_attr( $attr_val ) . '"';
}
?>
<div class="weddingblocks-guest-name-block" style="background-color: <?php echo esc_attr($background_color); ?>; text-align: <?php echo esc_attr($text_align); ?>;"<?php echo $anim_data; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
    <?php if ($show_prefix) : ?>
        <p class="guest-prefix-text" style="color: <?php echo esc_attr($text_color); ?>;"><?php echo esc_html($prefix); ?></p>
    <?php endif; ?>
    <h4 class="guest-name-text" style="color: <?php echo esc_attr($text_color); ?>; font-size: <?php echo esc_attr($name_font_size); ?>;"><?php echo esc_html($guest_name); ?></h4>
</div>