<?php

/**
 * Server-side rendering for the Couple Name block.
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
$name_type  = isset($attributes['nameType']) ? sanitize_key($attributes['nameType']) : 'full';
$align      = isset($attributes['align']) ? sanitize_key($attributes['align']) : 'center';

$font_size      = isset($attributes['fontSize']) ? intval($attributes['fontSize']) : 32;
$font_family    = isset($attributes['fontFamily']) ? sanitize_text_field($attributes['fontFamily']) : 'default';
$text_color     = isset($attributes['textColor']) ? sanitize_hex_color($attributes['textColor']) : '';
$text_transform = isset($attributes['textTransform']) ? sanitize_key($attributes['textTransform']) : 'none';

$full_name = '';
$nick_name = '';
$fallback  = '';
if ('bride' === $role) {
    $full_name = ! empty($attributes['brideName']) ? $attributes['brideName'] : get_post_meta(get_the_ID(), 'weddingblocks_bride_name', true);
    $nick_name = ! empty($attributes['brideNickname']) ? $attributes['brideNickname'] : get_post_meta(get_the_ID(), 'weddingblocks_bride_nickname', true);
    $fallback  = __('Mempelai Wanita', 'weddingblocks');
} else {
    $full_name = ! empty($attributes['groomName']) ? $attributes['groomName'] : get_post_meta(get_the_ID(), 'weddingblocks_groom_name', true);
    $nick_name = ! empty($attributes['groomNickname']) ? $attributes['groomNickname'] : get_post_meta(get_the_ID(), 'weddingblocks_groom_nickname', true);
    $fallback  = __('Mempelai Pria', 'weddingblocks');
}

if ('nickname' === $name_type) {
    $display = '' !== $nick_name ? $nick_name : $full_name;
    if ('' === $display) {
        $display = $fallback;
    }
} else {
    $display = '' !== $full_name ? $full_name : ('' !== $nick_name ? $nick_name : $fallback);
}

$wrapper_class = 'weddingblocks-atomic-couple-name role-' . sanitize_html_class($role) . ' type-' . sanitize_html_class($name_type) . ' align-' . sanitize_html_class($align);

// Font family mapping: jika 'default' / kosong, biarkan kosong agar mewarisi font tema
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

$color_style = '';
if ('' !== $text_color) {
    $color_style = ' color:' . $text_color . ';';
}

$style_attr = sprintf(
    'font-size:%1$dpx;%2$s%3$s text-transform:%4$s;',
    $font_size,
    $font_family_style,
    $color_style,
    $text_transform
);

$wrapper_attributes = get_block_wrapper_attributes(
    array_merge(
        array('class' => $wrapper_class),
        weddingblocks_get_animation_attrs($attributes)
    )
);
?>
<div <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
        ?>>
    <span class="atomic-name-text" style="<?php echo esc_attr($style_attr); ?>"><?php echo esc_html($display); ?></span>
</div>