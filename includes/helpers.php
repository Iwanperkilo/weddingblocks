<?php

/**
 * Helper functions for WeddingBlocks.
 *
 * @package WeddingBlocks
 */

if (! defined('ABSPATH')) {
    exit;
}

if (! function_exists('weddingblocks_is_pro')) {
    /**
     * Whether WeddingBlocks Pro is active and its license is valid.
     *
     * Free always returns false by default and knows nothing about how
     * Pro's licensing works internally — Pro hooks into the
     * 'weddingblocks_is_pro' filter itself (after validating its own
     * license) so any Free-side code can gate behaviour with a single,
     * consistent check.
     *
     * @return bool
     */
    function weddingblocks_is_pro()
    {
        return (bool) apply_filters('weddingblocks_is_pro', false);
    }
}

if (! function_exists('weddingblocks_get_animation_attrs')) {
    /**
     * Build data-* attributes for scroll-triggered animations.
     *
     * Returns an empty array when animation is disabled or not set,
     * so callers can safely pass the result to get_block_wrapper_attributes()
     * via array_merge without any side-effects.
     *
     * @param array $attributes Block attributes.
     * @return array Associative array of HTML attributes, or empty array.
     */
    function weddingblocks_get_animation_attrs($attributes)
    {
        $style = isset($attributes['animationStyle']) ? $attributes['animationStyle'] : 'none';
        if ('none' === $style || empty($style)) {
            return array();
        }

        $allowed = array('fadeUp', 'fadeIn', 'slideLeft', 'slideRight', 'zoomIn');
        if (! in_array($style, $allowed, true)) {
            return array();
        }

        return array(
            'data-wb-anim'     => $style,
            'data-wb-duration' => isset($attributes['animationDuration']) ? absint($attributes['animationDuration']) : 600,
            'data-wb-delay'    => isset($attributes['animationDelay']) ? absint($attributes['animationDelay']) : 0,
        );
    }
}

if (! function_exists('weddingblocks_get_attention_attrs')) {
    /**
     * Build data-* attributes for continuous/attention animations
     * (sway, float, pulse, wobble, shake).
     *
     * Unlike weddingblocks_get_animation_attrs() above, these are NOT
     * scroll-triggered one-shot effects — they loop for as long as the
     * element stays visible on screen. Kept as a fully separate attribute
     * namespace (data-wb-attn*) so an element can use an entrance animation
     * and a continuous attention effect at the same time without either
     * one overwriting the other.
     *
     * Returns an empty array when the effect is disabled or not set, so
     * callers can safely array_merge() the result.
     *
     * @param array $attributes Block attributes.
     * @return array Associative array of HTML attributes, or empty array.
     */
    function weddingblocks_get_attention_attrs($attributes)
    {
        $effect = isset($attributes['attentionEffect']) ? $attributes['attentionEffect'] : 'none';
        if ('none' === $effect || empty($effect)) {
            return array();
        }

        $allowed_effects = array('sway', 'float', 'pulse', 'wobble', 'shake');
        if (! in_array($effect, $allowed_effects, true)) {
            return array();
        }

        $allowed_speeds = array('slow', 'normal', 'fast');
        $speed = isset($attributes['attentionSpeed']) ? $attributes['attentionSpeed'] : 'normal';
        if (! in_array($speed, $allowed_speeds, true)) {
            $speed = 'normal';
        }

        $allowed_intensities = array('subtle', 'normal', 'strong');
        $intensity = isset($attributes['attentionIntensity']) ? $attributes['attentionIntensity'] : 'normal';
        if (! in_array($intensity, $allowed_intensities, true)) {
            $intensity = 'normal';
        }

        // Pivot point for rotate/scale-based effects (sway, wobble, pulse).
        // Has no visual effect on float/shake (translate-based), but is safe
        // to include either way.
        $allowed_origins = array('center', 'top', 'bottom', 'left', 'right', 'top-left', 'top-right', 'bottom-left', 'bottom-right');
        $origin = isset($attributes['attentionOrigin']) ? $attributes['attentionOrigin'] : 'center';
        if (! in_array($origin, $allowed_origins, true)) {
            $origin = 'center';
        }

        return array(
            'data-wb-attn'           => $effect,
            'data-wb-attn-speed'     => $speed,
            'data-wb-attn-intensity' => $intensity,
            'data-wb-attn-origin'    => $origin,
        );
    }
}

if (! function_exists('weddingblocks_sanitize_color')) {
    /**
     * Sanitize a color value, allowing hex (#fff / #ffffff / #ffffffff with alpha),
     * rgb()/rgba(), and the literal 'transparent'. Falls back to an empty string
     * (caller decides the default) if the value doesn't match any allowed format.
     *
     * @param string $color Raw color value from block attributes.
     * @return string
     */
    function weddingblocks_sanitize_color($color)
    {
        if (empty($color)) {
            return '';
        }

        $color = trim($color);

        if ('transparent' === strtolower($color)) {
            return 'transparent';
        }

        // #fff, #ffffff, or #ffffffff (8-digit hex with alpha channel).
        if (preg_match('/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6}|[A-Fa-f0-9]{8})$/', $color)) {
            return $color;
        }

        // rgb(r, g, b) or rgba(r, g, b, a).
        if (preg_match('/^rgba?\(\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*\d{1,3}\s*(,\s*(0|1|0?\.\d+)\s*)?\)$/i', $color)) {
            return $color;
        }

        return '';
    }
}

if (! function_exists('weddingblocks_get_contrast_color')) {
    /**
     * Auto-contrast text color for buttons, so light accent colors
     * don't render invisible white-on-white text.
     *
     * @param string $hex_color
     * @return string
     */
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
