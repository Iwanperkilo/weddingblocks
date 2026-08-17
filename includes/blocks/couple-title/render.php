<?php

/**
 * Server-side rendering for the Couple Title block.
 *
 * @package WeddingBlocks
 */

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals

if (! defined('ABSPATH')) {
    exit;
}

$block_attributes = array();
if (isset($attributes)) {
    if (is_array($attributes)) {
        $block_attributes = $attributes;
    } elseif (is_object($attributes) && isset($attributes->attributes) && is_array($attributes->attributes)) {
        $block_attributes = $attributes->attributes;
    }
}

$groom_display = get_post_meta(get_the_ID(), 'weddingblocks_groom_nickname', true);
if (empty($groom_display)) {
    $groom_display = get_post_meta(get_the_ID(), 'weddingblocks_groom_name', true);
}
if (empty($groom_display)) {
    $groom_display = __('Mempelai Pria', 'weddingblocks');
}

$bride_display = get_post_meta(get_the_ID(), 'weddingblocks_bride_nickname', true);
if (empty($bride_display)) {
    $bride_display = get_post_meta(get_the_ID(), 'weddingblocks_bride_name', true);
}
if (empty($bride_display)) {
    $bride_display = __('Mempelai Wanita', 'weddingblocks');
}

$text_color = ! empty($block_attributes['textColor']) ? $block_attributes['textColor'] : '#ffffff';

$allowed_transforms = array('none', 'uppercase', 'lowercase', 'capitalize');
$text_transform      = isset($block_attributes['textTransform']) ? $block_attributes['textTransform'] : 'none';
if (! in_array($text_transform, $allowed_transforms, true)) {
    $text_transform = 'none';
}

$allowed_aligns = array('left', 'center', 'right');
$text_align      = isset($block_attributes['textAlign']) ? $block_attributes['textAlign'] : 'center';
if (! in_array($text_align, $allowed_aligns, true)) {
    $text_align = 'center';
}

$separator    = isset($block_attributes['separator']) ? $block_attributes['separator'] : '&';
$text_shadow  = ! empty($block_attributes['textShadow']);
$font_size    = ! empty($block_attributes['style']['typography']['fontSize']) ? $block_attributes['style']['typography']['fontSize'] : '';

// Font Nama Cover: kunci bawaan atau CSS font-family mentah dari tema /
// Font Library WordPress. Nilai legacy `wbproFontFamily` (dari versi Pro)
// tetap dipakai bila ada.
$font_map = array(
    'playfair'   => "'Playfair Display', Georgia, serif",
    'greatvibes' => "'Great Vibes', cursive",
    'montserrat' => "'Montserrat', sans-serif",
    'georgia'    => "Georgia, 'Times New Roman', serif",
    'system'     => "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif",
    'sans-serif' => "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif",
    'monospace'  => "'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, Courier, monospace",
);

$font_raw = isset($block_attributes['fontFamily']) ? $block_attributes['fontFamily'] : '';
if (empty($font_raw) && isset($block_attributes['wbproFontFamily'])) {
    $font_raw = $block_attributes['wbproFontFamily'];
}
$font_raw   = is_string($font_raw) ? trim($font_raw) : '';
$font_stack = '';
if ('' !== $font_raw && 'default' !== $font_raw) {
    if (isset($font_map[$font_raw])) {
        $font_stack = $font_map[$font_raw];
    } else {
        $cleaned = (string) preg_replace('/[^a-zA-Z0-9\s,()\'"-.]/', '', wp_strip_all_tags($font_raw));
        $cleaned = preg_replace('/\s+/', ' ', $cleaned);
        $font_stack = trim($cleaned);
        if ('' === $font_stack || strlen($font_stack) > 300) {
            $font_stack = '';
        }
    }
}

$heart_presets      = array("\u{2764}\u{FE0F}", "\u{2764}");
$is_heart_separator = in_array($separator, $heart_presets, true);

$transform_text = static function ($text, $transform) {
    if ($transform === 'uppercase') {
        return strtoupper($text);
    } elseif ($transform === 'lowercase') {
        return strtolower($text);
    } elseif ($transform === 'capitalize') {
        return ucfirst(strtolower($text));
    }
    return $text;
};

$groom_transformed = $transform_text($groom_display, $text_transform);
$bride_transformed = $transform_text($bride_display, $text_transform);

$inline_style = sprintf(
    'color: %s !important; text-align: %s;',
    esc_attr($text_color),
    esc_attr($text_align)
);
if (! empty($font_size)) {
    $inline_style .= sprintf(' font-size: %s;', esc_attr($font_size));
}
if (! empty($font_stack)) {
    // Jangan pakai esc_attr di sini: get_block_wrapper_attributes() meng-escape
    // nilai style satu kali lagi, dan '' yang sudah jadi &#039; akan merusak
    // segmen berisi tanda kutip. $font_stack sudah dibatasi charset aman.
    $inline_style .= sprintf(' font-family: %s !important;', $font_stack);
}
if ($text_shadow) {
    $inline_style .= ' text-shadow: 0 2px 6px rgba(0,0,0,0.45);';
}

$extra_classes = 'weddingblocks-cover-title';
if ($text_shadow) {
    $extra_classes .= ' has-text-shadow';
}

$wrapper_attributes = get_block_wrapper_attributes(
    array_merge(
        array(
            'class' => $extra_classes,
            'style' => $inline_style,
        ),
        weddingblocks_get_animation_attrs($block_attributes)
    )
);
?>

<h1 <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
    ?>>
    <?php echo esc_html($groom_transformed); ?> <span class="weddingblocks-separator<?php echo $is_heart_separator ? ' weddingblocks-separator--icon' : ''; ?>" <?php echo $is_heart_separator ? ' aria-label="' . esc_attr__('dan', 'weddingblocks') . '"' : ''; ?>><?php echo $is_heart_separator ? '' : esc_html($separator); ?></span> <?php echo esc_html($bride_transformed); ?>
</h1>