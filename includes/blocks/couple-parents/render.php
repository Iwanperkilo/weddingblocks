<?php

/**
 * Server-side rendering for the Couple Parents block.
 *
 * @package WeddingBlocks
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block content.
 * @var WP_Block $block      Block instance.
 */

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals

if (! defined('ABSPATH')) {
    exit;
}
$role       = isset($attributes['role']) ? sanitize_key($attributes['role']) : 'groom';
$label      = isset($attributes['label']) ? $attributes['label'] : '';
$show_label = isset($attributes['showLabel']) ? (bool) $attributes['showLabel'] : true;
$align      = isset($attributes['align']) ? sanitize_key($attributes['align']) : 'center';
$align      = in_array($align, array('left', 'center', 'right'), true) ? $align : 'center';
// Style attributes
$font_size   = isset($attributes['fontSize']) ? intval($attributes['fontSize']) : 17;
$font_family = isset($attributes['fontFamily']) ? sanitize_text_field($attributes['fontFamily']) : 'default';
$text_color  = isset($attributes['textColor']) ? sanitize_hex_color($attributes['textColor']) : '#333333';
if (! $text_color) {
    $text_color = '#333333';
}
if ('bride' === $role) {
    $parents  = ! empty($attributes['brideParents']) ? $attributes['brideParents'] : get_post_meta(get_the_ID(), 'weddingblocks_bride_parents', true);
    $default_label = __('Putri dari', 'weddingblocks');
    $fallback       = __('Bapak & Ibu Orang Tua Wanita', 'weddingblocks');
} else {
    $parents  = ! empty($attributes['groomParents']) ? $attributes['groomParents'] : get_post_meta(get_the_ID(), 'weddingblocks_groom_parents', true);
    $default_label = __('Putra dari', 'weddingblocks');
    $fallback       = __('Bapak & Ibu Orang Tua Pria', 'weddingblocks');
}
if ('' === $parents) {
    $parents = $fallback;
}
if ('' === $label) {
    $label   = $default_label;
}
// Font family mapping: jika 'default' / kosong, biarkan kosong agar mewarisi font tema (--wb-font-body)
$font_family_style = '';
if (! empty($font_family) && 'default' !== $font_family) {
    if ('playfair' === $font_family) {
        $font_family_style = " font-family: 'Playfair Display', Georgia, serif !important;";
    } elseif ('greatvibes' === $font_family) {
        $font_family_style = " font-family: 'Great Vibes', cursive !important;";
    } elseif ('montserrat' === $font_family) {
        $font_family_style = " font-family: 'Montserrat', sans-serif !important;";
    } elseif ('georgia' === $font_family) {
        $font_family_style = " font-family: Georgia, serif !important;";
    } elseif ('sans-serif' === $font_family) {
        $font_family_style = " font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;";
    } else {
        $cleaned = (string) preg_replace('/[^a-zA-Z0-9\s,()\'"-.]/', '', wp_strip_all_tags($font_family));
        $cleaned = preg_replace('/\s+/', ' ', $cleaned);
        $font_stack = trim($cleaned);
        if ('' !== $font_stack && strlen($font_stack) <= 300) {
            $font_family_style = ' font-family: ' . $font_stack . ' !important;';
        }
    }
}
$style_attr = sprintf(
    'font-size:%1$dpx;%2$s color:%3$s;',
    $font_size,
    $font_family_style,
    $text_color
);
$wrapper_class = 'weddingblocks-atomic-couple-parents role-' . sanitize_html_class($role) . ' align-' . sanitize_html_class($align);

$wrapper_attributes = get_block_wrapper_attributes(
    array_merge(
        array('class' => $wrapper_class),
        weddingblocks_get_animation_attrs($attributes)
    )
);
?>
<div <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
        ?>>
    <?php if ($show_label) : ?>
        <span class="atomic-parents-label" style="<?php echo esc_attr($style_attr); ?>"><?php echo esc_html($label); ?></span>
    <?php endif; ?>
    <span class="atomic-parents-names" style="<?php echo esc_attr($style_attr); ?>"><?php echo esc_html($parents); ?></span>
</div>