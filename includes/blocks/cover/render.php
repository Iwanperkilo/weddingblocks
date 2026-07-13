<?php

/**
 * Server-side rendering for the Cover block.
 *
 * @package WeddingBlocks
 */

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals

if (! defined('ABSPATH')) {
    exit;
}

$bg_image = ! empty($attributes['backgroundImage']) ? esc_url($attributes['backgroundImage']) : '';
$button_text = ! empty($attributes['buttonText']) ? wp_kses_post($attributes['buttonText']) : __('Buka Undangan', 'weddingblocks');
$button_border_radius = isset($attributes['buttonBorderRadius']) ? intval($attributes['buttonBorderRadius']) : 30;

// Style attributes
$overlay_opacity = isset($attributes['overlayOpacity']) ? intval($attributes['overlayOpacity']) : 35;
$overlay_color = ! empty($attributes['overlayColor']) ? sanitize_hex_color($attributes['overlayColor']) : '#000000';
$accent_color = ! empty($attributes['accentColor']) ? sanitize_hex_color($attributes['accentColor']) : '#b5a46d';

$bg_style = $bg_image ? "background-image: url('$bg_image');" : "";

// Cover width mode: "full" (default, edge-to-edge), "mobile" (fixed ~480px
// card centered on screen), or "custom" (user-defined px width).
$cover_width_mode = ! empty($attributes['coverWidth']) && in_array($attributes['coverWidth'], array('full', 'mobile', 'custom'), true)
    ? $attributes['coverWidth']
    : 'full';

$wrapper_classes = array('weddingblocks-cover-wrapper');

if ('mobile' === $cover_width_mode) {
    $wrapper_classes[] = 'weddingblocks-cover-wrapper--boxed';
    $bg_style         .= '--wb-cover-max-width: 480px;';
} elseif ('custom' === $cover_width_mode) {
    // Clamp to a sane range so the cover never collapses or overflows oddly.
    $cover_custom_width = isset($attributes['coverCustomWidth']) ? intval($attributes['coverCustomWidth']) : 480;
    $cover_custom_width = max(280, min(960, $cover_custom_width));

    $wrapper_classes[] = 'weddingblocks-cover-wrapper--boxed';
    $bg_style          .= '--wb-cover-max-width: ' . $cover_custom_width . 'px;';
}

// Auto-contrast text color for the button, so a light/white accent color
// doesn't render invisible white-on-white text.
if (! function_exists('weddingblocks_get_contrast_color')) {
    function weddingblocks_get_contrast_color($hex_color)
    {
        $hex_color = ltrim((string) $hex_color, '#');
        if (strlen($hex_color) === 3) {
            $hex_color = $hex_color[0] . $hex_color[0] . $hex_color[1] . $hex_color[1] . $hex_color[2] . $hex_color[2];
        }
        if (strlen($hex_color) !== 6) {
            return '#ffffff';
        }
        $r = hexdec(substr($hex_color, 0, 2));
        $g = hexdec(substr($hex_color, 2, 2));
        $b = hexdec(substr($hex_color, 4, 2));
        // Relative luminance (per WCAG).
        $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
        return $luminance > 0.6 ? '#1c1d1d' : '#ffffff';
    }
}

$button_text_color = weddingblocks_get_contrast_color($accent_color);

$wrapper_attributes = get_block_wrapper_attributes(
    array(
        'id'    => 'weddingblocks-cover',
        'class' => implode(' ', $wrapper_classes),
        'style' => $bg_style,
    )
);
?>
<div <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped internally by get_block_wrapper_attributes(). 
        ?>>
    <div class="weddingblocks-cover-overlay" style="background-color: <?php echo esc_attr($overlay_color); ?>; opacity: <?php echo esc_attr($overlay_opacity / 100); ?>;"></div>
    <div class="weddingblocks-cover-content">
        <?php echo wp_kses_post($content); ?>

        <button id="weddingblocks-open-btn" type="button" class="weddingblocks-btn-gold" style="background-color: <?php echo esc_attr($accent_color); ?>; border-color: <?php echo esc_attr($accent_color); ?>; color: <?php echo esc_attr($button_text_color); ?>; border-radius: <?php echo esc_attr($button_border_radius); ?>px;">
            <svg class="icon-mail" viewBox="0 0 24 24" width="20" height="20" fill="currentColor" style="margin-right: 8px; vertical-align: middle;">
                <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z" />
            </svg>
            <span><?php echo wp_kses_post($button_text); ?></span>
        </button>
    </div>
</div>