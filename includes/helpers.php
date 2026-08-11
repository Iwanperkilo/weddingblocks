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

        // Extensible: an add-on (e.g. WeddingBlocks Pro) can register new
        // entrance animation styles by hooking 'weddingblocks_animation_styles'.
        $allowed = apply_filters('weddingblocks_animation_styles', array('fadeUp', 'fadeIn', 'slideLeft', 'slideRight', 'zoomIn'));
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

if (! function_exists('weddingblocks_render_dot')) {
    /**
     * Render satu kepingan titik/dekorasi dengan style random via inline CSS var.
     */
    function weddingblocks_render_dot()
    {
        $size  = wp_rand(4, 8);
        $left  = wp_rand(2, 96);
        $dur   = wp_rand(10, 22);
        $delay = wp_rand(0, 14);
        $drift = wp_rand(-35, 35);
        $op    = wp_rand(45, 90) / 100;
        $style = sprintf(
            '--wb-size:%1$dpx;--wb-left:%2$d%%;--wb-dur:%3$ds;--wb-delay:%4$ds;--wb-drift:%5$dpx;--wb-op:%6$s;',
            $size,
            $left,
            $dur,
            $delay,
            $drift,
            esc_attr($op)
        );
        printf('<span class="wb-dot" style="%s"></span>', esc_attr($style));
    }
}

if (! function_exists('weddingblocks_render_single_butterfly')) {
    /**
     * Render satu kupu-kupu (sayap bergradasi + vena).
     *
     * @param string $mate_class Kelas untuk posisi pasangan (wb-mate-a / wb-mate-b).
     * @param int    $variant    Variasi warna/ukuran (0 = utama, 1 = pasangan).
     */
    function weddingblocks_render_single_butterfly($mate_class, $variant = 0)
    {
        $gid       = 'wbwing-' . wp_generate_password(6, false, false);
        $flap_dur  = wp_rand(28, 42) / 100;
        $body_tone = (0 === $variant) ? '#4a4436' : '#5c4a2e';
        $scale     = 1 - ($variant * 0.08); // pasangan kedua sedikit lebih kecil

        $motion_style = sprintf(
            '--wb-flap:%1$ss;transform:scale(%2$s);',
            esc_attr($flap_dur),
            esc_attr($scale)
        );

        printf('<span class="wb-flyer--butterfly %1$s">', esc_attr($mate_class));
        printf('<span class="wb-flyer-motion" style="%s">', esc_attr($motion_style));
        printf(
            '<svg viewBox="0 0 60 46" class="wb-flyer-svg" aria-hidden="true">
                <defs>
                    <linearGradient id="%1$s" x1="0%%" y1="0%%" x2="100%%" y2="100%%">
                        <stop offset="0%%" stop-color="#e3d4a3"/>
                        <stop offset="55%%" stop-color="#b5a46d"/>
                        <stop offset="100%%" stop-color="#8c7a48"/>
                    </linearGradient>
                </defs>
                <g class="wb-wing wb-wing--left">
                    <path fill="url(#%1$s)" d="M30,23 C21,4 3,2 1,13 C-1,23 10,29 20,26 C11,31 2,37 6,45 C10,52 23,43 30,23 Z"/>
                    <path fill="#fff" opacity="0.35" d="M28,23 C22,12 11,10 8,16 C6,21 13,25 19,23 Z"/>
                    <path fill="none" stroke="%2$s" stroke-width="0.6" opacity="0.5" d="M29,23 C21,15 12,13 6,17 M29,23 C18,25 9,29 6,37"/>
                </g>
                <g class="wb-wing wb-wing--right">
                    <path fill="url(#%1$s)" d="M30,23 C39,4 57,2 59,13 C61,23 50,29 40,26 C49,31 58,37 54,45 C50,52 37,43 30,23 Z"/>
                    <path fill="#fff" opacity="0.35" d="M32,23 C38,12 49,10 52,16 C54,21 47,25 41,23 Z"/>
                    <path fill="none" stroke="%2$s" stroke-width="0.6" opacity="0.5" d="M31,23 C39,15 48,13 54,17 M31,23 C42,25 51,29 54,37"/>
                </g>
                <ellipse class="wb-body" cx="30" cy="23" rx="1.8" ry="12" fill="%2$s"/>
                <path stroke="%2$s" stroke-width="0.7" fill="none" opacity="0.8" d="M29,12 C27,8 24,7 22,5 M31,12 C33,8 36,7 38,5"/>
              </svg>',
            esc_attr($gid),
            esc_attr($body_tone)
        );
        echo '</span></span>';
    }
}

if (! function_exists('weddingblocks_render_butterfly_pair')) {
    /**
     * Render SEPASANG kupu-kupu yang terbang berdekatan.
     */
    function weddingblocks_render_butterfly_pair()
    {
        // Margin posisi (area sempit: top 20-55%, left 10-70%).
        $top   = wp_rand(20, 55);
        $left  = wp_rand(10, 70);
        $scale = wp_rand(85, 130) / 100;

        $dir = (1 === wp_rand(0, 1)) ? 1 : -1;

        $dur   = wp_rand(22, 34);
        $delay = wp_rand(0, 10);

        $dx1       = $dir * wp_rand(12, 20);
        $dy1       = -1 * wp_rand(10, 18);
        $dx2       = $dir * wp_rand(20, 32);
        $dy2       = -1 * wp_rand(16, 28);
        $rot       = $dir * wp_rand(6, 12);
        $orbit_dur = wp_rand(45, 70) / 10;

        $anchor_style = sprintf(
            '--wb-top:%1$d%%;--wb-left:%2$d%%;',
            $top,
            $left
        );

        $pair_style = sprintf(
            'transform:scale(%1$s);--wb-dur:%2$ds;--wb-delay:%3$ds;--wb-dx1:%4$d%%;--wb-dy1:%5$d%%;--wb-dx2:%6$d%%;--wb-dy2:%7$d%%;--wb-rot:%8$ddeg;--wb-orbit-dur:%9$ss;',
            esc_attr($scale),
            $dur,
            $delay,
            $dx1,
            $dy1,
            $dx2,
            $dy2,
            $rot,
            esc_attr($orbit_dur)
        );

        printf('<span class="wb-butterfly-pair" style="%s">', esc_attr($pair_style));
        printf('<span class="wb-butterfly-anchor" style="%s">', esc_attr($anchor_style));
        weddingblocks_render_single_butterfly('wb-mate-a', 0);
        weddingblocks_render_single_butterfly('wb-mate-b', 1);
        echo '</span></span>';
    }
}

if (! function_exists('weddingblocks_render_flyer')) {
    /**
     * Render burung terbang (SVG siluet - tampak samping).
     *
     * @param string $type Jenis flyer ('bird').
     */
    function weddingblocks_render_flyer($type)
    {
        $top       = wp_rand(5, 75);
        $dur       = wp_rand(11, 16);
        $delay     = wp_rand(0, 14);
        $scale     = wp_rand(75, 125) / 100;
        $flap_dur  = wp_rand(35, 50) / 100;

        $style = sprintf(
            '--wb-top:%1$d%%;--wb-dur:%2$ds;--wb-delay:%3$ds;--wb-scale:%4$s;--wb-bird-flap:%5$ss;',
            $top,
            $dur,
            $delay,
            esc_attr($scale),
            esc_attr($flap_dur)
        );

        printf('<span class="wb-flyer wb-flyer--%1$s" style="%2$s">', esc_attr($type), esc_attr($style));
        echo '<span class="wb-bird-motion">';
        echo '<svg
    viewBox="0 0 100 62"
    class="wb-flyer-svg wb-bird-svg"
    aria-hidden="true"
>

    <!-- SAYAP JAUH -->
    <path
        class="wb-wing wb-wing--bird-far"
        d="
            M46,28
            C42,19 34,10 24,4
            C20,2 16,1 12,2
            C16,5 19,8 21,11
            C17,9 13,8 9,8
            C13,12 17,15 20,18
            C16,17 12,17 8,18
            C13,21 18,24 23,25
            C19,25 16,26 13,28
            C19,29 26,29 32,27
            C37,28 42,29 46,28
            Z
        "
    />

    <!-- EKOR -->
    <path
        class="wb-bird-tail"
        d="
            M22,34
            C16,33 9,31 3,27
            C7,31 10,35 9,41
            C13,37 15,39 12,47
            C17,42 18,39 19,37
            C20,42 21,45 19,50
            C24,44 25,40 24,37
            C24,36 23,35 22,34
            Z
        "
    />

    <!-- BADAN BURUNG TAMPAK SAMPING -->
    <path
        class="wb-bird-body"
        d="
            M18,32
            C22,24 32,19 43,19
            C50,19 55,21 59,24
            C63,22 68,21 73,22
            L81,23
            L75,25
            L81,27
            L73,26
            C69,27 64,28 60,28
            C55,33 47,37 38,38
            C31,39 24,38 19,35
            C17,34 17,33 18,32
            Z
        "
    />

    <!-- SAYAP DEKAT -->
    <path
        class="wb-wing wb-wing--bird-near"
        d="
            M50,27
            C45,16 36,6 25,-1
            C21,-3 16,-4 12,-3
            C16,1 20,4 22,8
            C18,5 13,4 9,4
            C13,9 18,12 21,16
            C17,14 12,14 8,15
            C13,19 19,22 24,24
            C20,24 16,25 12,27
            C19,29 27,29 34,27
            C39,28 45,29 50,27
            Z
        "
    />

</svg>';
        echo '</span>'; // .wb-bird-motion
        echo '</span>';
    }
}
