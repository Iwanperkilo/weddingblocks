<?php

/**
 * Server-side rendering for the Countdown block.
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

$target = '';
if (! empty($block_attributes['targetDate'])) {
    $target = $block_attributes['targetDate'];
} else {
    $target = get_post_meta(get_the_ID(), 'weddingblocks_wedding_date', true);
}
if (empty($target)) {
    $target = '2026-12-31T09:00';
}
$target = esc_attr($target);


$text_color           = ! empty($block_attributes['textColor']) ? weddingblocks_sanitize_color($block_attributes['textColor']) : '';
$label_color          = ! empty($block_attributes['labelColor']) ? weddingblocks_sanitize_color($block_attributes['labelColor']) : '';
$box_background_color = ! empty($block_attributes['boxBackgroundColor']) ? weddingblocks_sanitize_color($block_attributes['boxBackgroundColor']) : '';
$border_color          = ! empty($block_attributes['borderColor']) ? weddingblocks_sanitize_color($block_attributes['borderColor']) : '';

// Box style controls.
$border_width  = isset($block_attributes['borderWidth']) ? absint($block_attributes['borderWidth']) : 1;
$border_radius = isset($block_attributes['borderRadius']) ? absint($block_attributes['borderRadius']) : 15;
$box_shadow    = ! isset($block_attributes['boxShadow']) || (bool) $block_attributes['boxShadow'];
$value_font_size = isset($block_attributes['valueFontSize']) ? absint($block_attributes['valueFontSize']) : 32;
$label_font_size  = isset($block_attributes['labelFontSize']) ? absint($block_attributes['labelFontSize']) : 10;
$gap              = isset($block_attributes['gap']) ? absint($block_attributes['gap']) : 15;
$box_padding_vertical   = isset($block_attributes['boxPaddingVertical']) ? absint($block_attributes['boxPaddingVertical']) : 15;
$box_padding_horizontal = isset($block_attributes['boxPaddingHorizontal']) ? absint($block_attributes['boxPaddingHorizontal']) : 5;

$box_shadow_css = $box_shadow ? '3px 6px 8px rgb(145 145 145 / 54%)' : 'none';

// Warna hanya ditulis inline bila dikustom; jika kosong, block mengikuti
// tema warna undangan (--wb-color-*) dari CSS.
$item_style = sprintf(
    'border-width: %1$dpx; border-style: solid; border-radius: %2$dpx; box-shadow: %3$s; padding: %4$dpx %5$dpx;',
    $border_width,
    $border_radius,
    esc_attr($box_shadow_css),
    $box_padding_vertical,
    $box_padding_horizontal
);

if (! empty($box_background_color)) {
    $item_style .= ' background-color: ' . esc_attr($box_background_color) . ';';
}

if (! empty($border_color)) {
    $item_style .= ' border-color: ' . esc_attr($border_color) . ';';
}

$value_style = 'font-size: ' . $value_font_size . 'px;';
if (! empty($text_color)) {
    $value_style .= ' color: ' . esc_attr($text_color) . ';';
}

$label_style = 'font-size: ' . $label_font_size . 'px;';
if (! empty($label_color)) {
    $label_style .= ' color: ' . esc_attr($label_color) . ';';
}

$wrapper_style = sprintf('gap: %1$dpx;', $gap);

// Label texts.
$label_days    = ! empty($block_attributes['labelDays']) ? sanitize_text_field($block_attributes['labelDays']) : __('Hari', 'weddingblocks');
$label_hours   = ! empty($block_attributes['labelHours']) ? sanitize_text_field($block_attributes['labelHours']) : __('Jam', 'weddingblocks');
$label_minutes = ! empty($block_attributes['labelMinutes']) ? sanitize_text_field($block_attributes['labelMinutes']) : __('Menit', 'weddingblocks');
$label_seconds = ! empty($block_attributes['labelSeconds']) ? sanitize_text_field($block_attributes['labelSeconds']) : __('Detik', 'weddingblocks');
$wrapper_attributes = get_block_wrapper_attributes(
    array_merge(
        array(
            'class'       => 'weddingblocks-countdown',
            'style'       => $wrapper_style,
            'data-target' => $target,
        ),
        weddingblocks_get_animation_attrs( $block_attributes )
    )
);
?>
<div <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
        ?>>
    <div class="countdown-item" style="<?php echo esc_attr($item_style); ?>">
        <span class="countdown-value days" style="<?php echo esc_attr($value_style); ?>">00</span>
        <span class="countdown-label" style="<?php echo esc_attr($label_style); ?>"><?php echo esc_html($label_days); ?></span>
    </div>
    <div class="countdown-item" style="<?php echo esc_attr($item_style); ?>">
        <span class="countdown-value hours" style="<?php echo esc_attr($value_style); ?>">00</span>
        <span class="countdown-label" style="<?php echo esc_attr($label_style); ?>"><?php echo esc_html($label_hours); ?></span>
    </div>
    <div class="countdown-item" style="<?php echo esc_attr($item_style); ?>">
        <span class="countdown-value minutes" style="<?php echo esc_attr($value_style); ?>">00</span>
        <span class="countdown-label" style="<?php echo esc_attr($label_style); ?>"><?php echo esc_html($label_minutes); ?></span>
    </div>
    <div class="countdown-item" style="<?php echo esc_attr($item_style); ?>">
        <span class="countdown-value seconds" style="<?php echo esc_attr($value_style); ?>">00</span>
        <span class="countdown-label" style="<?php echo esc_attr($label_style); ?>"><?php echo esc_html($label_seconds); ?></span>
    </div>
</div>