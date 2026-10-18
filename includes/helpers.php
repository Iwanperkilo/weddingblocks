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
     * Render burung merpati terbang (SVG siluet anggun - tema pernikahan).
     *
     * @param string $type Jenis flyer ('bird').
     */
    function weddingblocks_render_flyer($type)
    {
        // Ketinggian difokuskan pada area langit/atas (5% - 35%) agar tidak menabrak teks nama mempelai.
        $top       = wp_rand(5, 35);
        $dur       = wp_rand(14, 22);
        $delay     = wp_rand(0, 12);
        $scale     = wp_rand(80, 115) / 100;
        $flap_dur  = wp_rand(38, 48) / 100;

        // Variasi arah terbang: 1 = kiri ke kanan (ltr), -1 = kanan ke kiri (rtl).
        $dir       = (1 === wp_rand(0, 1)) ? 'ltr' : 'rtl';
        $bird_id   = 'wbb-' . wp_generate_password(6, false, false);

        $style = sprintf(
            '--wb-top:%1$d%%;--wb-dur:%2$ds;--wb-delay:%3$ds;--wb-scale:%4$s;--wb-bird-flap:%5$ss;',
            $top,
            $dur,
            $delay,
            esc_attr($scale),
            esc_attr($flap_dur)
        );

        printf(
            '<span class="wb-flyer wb-flyer--%1$s wb-flyer--%2$s" style="%3$s">',
            esc_attr($type),
            esc_attr($dir),
            esc_attr($style)
        );
        echo '<span class="wb-bird-motion">';
        printf(
            '<svg viewBox="0 0 355.25 477" class="wb-flyer-svg wb-bird-svg" aria-hidden="true">
                <defs>
                    <!-- Palet Ivory & Pearl Wedding: Emas Hangat Paruh & Kepala -->
                    <linearGradient id="%1$s-g-gold" x1="0%%" y1="0%%" x2="100%%" y2="100%%">
                        <stop offset="0%%" stop-color="#f7dfa3"/>
                        <stop offset="100%%" stop-color="#cf9c4e"/>
                    </linearGradient>

                    <!-- Sayap Depan: Putih Mutiara Lembut (#ffffff -> #f8f4ed -> #dfcfb9) -->
                    <radialGradient id="%1$s-rg-front" cx="585.22" cy="256.94" fx="585.22" fy="256.94" r="126.6" gradientTransform="matrix(1.9705703,2.7543782,-1.808079,1.2935578,-54.89385,-1716.0615)" gradientUnits="userSpaceOnUse">
                        <stop offset="0%%" stop-color="#ffffff"/>
                        <stop offset="45%%" stop-color="#f8f4ed"/>
                        <stop offset="100%%" stop-color="#dfcfb9"/>
                    </radialGradient>

                    <!-- Sayap Belakang: Taupe-Champagne Teduh (#ede4d6 -> #ba9d7e) untuk Kedalaman 3D -->
                    <radialGradient id="%1$s-rg-back" cx="754.9" cy="139.63" fx="754.9" fy="139.63" r="104.56" gradientTransform="matrix(0.82750573,0.32706221,-1.0104512,2.55656,239.80162,-424.88439)" gradientUnits="userSpaceOnUse">
                        <stop offset="0%%" stop-color="#ede4d6"/>
                        <stop offset="60%%" stop-color="#d4c2aa"/>
                        <stop offset="100%%" stop-color="#ba9d7e"/>
                    </radialGradient>

                    <!-- Badan Burung: Putih Mutiara Lembut (#ffffff -> #f8f4ed -> #dfcfb9) -->
                    <radialGradient id="%1$s-rg-body" cx="733.23" cy="306.99" fx="733.23" fy="306.99" r="177.62" gradientTransform="matrix(0.75228474,-0.32090386,0.15747022,0.36915245,139.70106,362.72348)" gradientUnits="userSpaceOnUse">
                        <stop offset="0%%" stop-color="#ffffff"/>
                        <stop offset="50%%" stop-color="#f8f4ed"/>
                        <stop offset="100%%" stop-color="#dfcfb9"/>
                    </radialGradient>
                </defs>
                <g transform="translate(-502.05578,0.41472026)">
                    <!-- SAYAP JAUH / BELAKANG -->
                    <path class="wb-bird-wing wb-bird-wing--far" fill="url(#%1$s-rg-back)" stroke="#a89379" stroke-width="0.8" d="m 815.03095,0.09789 c -0.79855,-0.0301 -1.92616,0.13238 -2.8615,0.43818 -3.04856,0.99662 -3.68804,2.85798 -3.14785,9.14906 0.42497,4.94925 0.40973,5.02057 -0.74253,3.68545 -1.94196,-2.25017 -9.32315,-7.79778 -10.89502,-8.18858 -3.96419,-0.98556 -4.65731,1.57437 -2.93259,10.83333 0.71613,3.8445 1.26207,7.04095 1.21253,7.10485 -0.0495,0.0638 -1.58954,-1.68844 -3.42234,-3.89476 -3.28786,-3.95794 -7.42651,-6.67057 -10.1762,-6.67057 -0.71363,0 -1.83591,0.58819 -2.49417,1.30869 -1.48756,1.62818 -1.5039,6.62396 -0.0336,10.61619 0.55523,1.50756 0.79889,2.63513 0.5411,2.50587 -6.45589,-3.23708 -12.55081,-4.12941 -13.39118,-1.96009 -0.23151,0.59762 -0.0815,1.18134 0.33967,1.32042 0.41494,0.137 0.75437,1.38862 0.75437,2.78364 0,1.48812 0.88488,4.54328 2.1407,7.39046 1.17727,2.66906 2.34169,5.67246 2.58897,6.67449 0.39245,1.59035 0.30405,1.7606 -0.70106,1.33411 -2.96271,-1.25715 -13.50573,-4.08942 -15.21788,-4.08842 -2.77649,0.002 -3.22047,0.51338 -4.05033,4.67919 -1.0119,5.07968 -0.9376,9.26589 0.22316,12.65259 l 0.96963,2.82864 -3.55268,3.3255 c -5.96952,5.59247 -8.10011,10.43095 -11.8147,24.63352 -0.45246,1.72996 -4.17029,5.5476 -12.46225,13.37662 -15.22111,14.37131 -26.85694,35.54576 -29.0308,38.77433 -10.26548,15.64819 -59.22147,69.21873 -70.97301,79.99061 l 133.88562,-48.74838 c 7.8554,-4.87907 42.21447,-38.83659 50.84427,-47.2932 7.01979,-6.87888 9.04302,-10.49305 17.53827,-28.56807 3.12728,-6.6538 7.26172,-18.95423 6.82889,-33.80282 -0.64732,-22.20878 -4.07351,-28.51084 -8.12436,-40.95657 -3.33713,-10.2521 -8.67692,-20.35345 -11.1737,-21.13843 -0.1738,-0.0547 -0.40328,-0.0858 -0.66946,-0.0958 z"/>

                    <!-- BADAN, EKOR, PARUH & KEPALA BURUNG -->
                    <g class="wb-bird-body">
                        <path fill="url(#%1$s-rg-body)" stroke="#b59f80" stroke-width="0.8" d="m 806.80333,132.62545 c -28.22084,-4.33883 -35.46618,13.90003 -42.51214,16.10776 -37.41985,11.72487 -63.43203,11.94951 -93.34605,23.57042 -21.08482,8.19097 -59.75537,32.15978 -59.75537,32.15978 l -11.54767,6.66329 c -2.80389,1.61791 -9.66508,4.98741 -14.22354,6.69601 -5.07112,1.90077 -17.87012,5.34403 -24.03125,6.4668 -3.30416,0.60213 -8.42645,1.62648 -11.38281,2.27539 -2.95635,0.6489 -6.79805,1.49139 -8.53711,1.87305 -1.73903,0.38166 -6.29132,1.25125 -10.11719,1.93164 -17.16525,3.05261 -26.25228,5.56948 -28.08789,7.78125 -0.41059,0.49472 -0.90605,1.88699 -1.10156,3.09179 -0.3081,1.89863 -0.03,2.55141 2.08593,4.89844 2.25142,2.4973 7.60272,5.64946 9.61524,5.66406 0.46834,0.003 2.08892,1.09406 3.59961,2.42383 1.779,1.56594 4.37514,2.95921 7.36914,3.95313 2.54245,0.84403 5.12244,2.15162 5.73437,2.90625 1.55424,1.91668 9.32944,4.20799 14.50586,4.27539 4.19482,0.0546 4.25955,0.0819 6.60352,2.82031 1.30177,1.52082 3.49214,3.58259 4.86914,4.58008 2.85573,2.06867 4.97509,2.25571 10.38672,0.91601 5.44614,-1.34823 9.37937,-2.10011 15.32617,-2.92773 2.95636,-0.41144 7.08309,-1.27469 9.16992,-1.91797 2.08684,-0.64327 6.35411,-1.72013 9.48438,-2.39258 13.86052,-2.97758 23.65095,-6.21392 30.96679,-10.24023 l 3.77344,-2.07813 5.08008,0.81641 c 9.5223,1.52995 12.327,1.65871 23.87891,1.0957 6.35434,-0.30969 13.26081,-0.52787 15.34765,-0.48437 6.52136,0.13567 18.33789,-1.14353 28.33399,-3.06641 5.2846,-1.01655 10.84565,-2.04037 12.35742,-2.27539 1.51176,-0.23502 4.27791,-1.10295 6.07031,-2.04297 1.79239,-0.94002 4.61839,-2.1693 6.35742,-2.61914 13.48925,-3.48933 23.06329,-7.604 31.61914,-13.58984 7.10749,-4.97253 10.3459,-7.1983 18.65625,-12.82227 3.99977,-2.70682 7.98401,-5.4938 8.85352,-6.19336 2.49177,-2.00483 14.07398,-13.57603 17.42969,-17.41211 3.63274,-4.15276 10.63499,-10.34816 15.813,-13.24689 5.12468,-2.86889 8.24282,-9.41288 16.17631,-10.2241 l -0.87017,-17.37666 c -2.79024,-0.81054 -3.90035,-1.5055 -4.82813,-3.0254 -2.04135,-3.3442 -7.26436,-9.08407 -10.20312,-11.21289 -3.79966,-2.75246 -9.04763,-4.2377 -13.69922,-6.04882 -1.71227,-0.66668 -3.40456,-1.49031 -5.2207,-1.76953 z"/>
                        <!-- Mata Mutiara Emas -->
                        <circle cx="817.3" cy="150.25" r="4.6" fill="#f7dfa3" stroke="#b59f80" stroke-width="0.8"/>
                        <circle cx="817.4" cy="150.38" r="2.4" fill="#36291a"/>
                        <circle cx="816.5" cy="149.5" r="0.9" fill="#ffffff"/>
                        <!-- Aksen Kepala & Paruh Emas Hangat -->
                        <path fill="url(#%1$s-g-gold)" d="m 858.70118,179.32939 c -1.81651,-0.6478 -2.21277,-2.77534 -4.37772,-4.50096 -4.77335,-3.78242 -10.69137,-1.74775 -15.42935,-1.95982 -3.06645,-0.17757 -6.90076,0.38373 -10.36849,0.097 -3.87907,-0.32076 -8.18332,0.3307 -9.55703,-2.45912 3.93576,-3.08425 10.51814,-9.25414 13.2883,-11.23251 2.88894,-2.06319 3.86788,-5.84336 8.79652,-5.10258 3.5505,0.53364 5.75849,1.3001 8.89611,4.52854 1.38612,1.42624 3.06669,3.5319 4.12951,5.94938 2.0647,4.69636 5.48384,13.79511 4.62215,14.68009 z"/>
                    </g>

                    <!-- SAYAP DEKAT / DEPAN -->
                    <path class="wb-bird-wing wb-bird-wing--near" fill="url(#%1$s-rg-front)" stroke="#b59f80" stroke-width="0.8" d="m 677.32115,215.55367 c 0.69382,0.3247 76.51003,-38.15693 77.14186,-38.0857 l 1.83085,-3.52316 c -4.86628,-0.88463 -20.65051,-15.97364 -29.35043,-27.61842 -4.2386,-5.67334 -9.8723,-13.19272 -13.81445,-18.43945 -1.1062,-1.47228 -2.8713,-4.38174 -3.92383,-6.4668 -1.05253,-2.08505 -2.82598,-5.17534 -3.93945,-6.86719 -1.11347,-1.69182 -3.50134,-5.35231 -5.30859,-8.13476 -1.80726,-2.78245 -4.62287,-7.11655 -6.25586,-9.63086 -3.01907,-4.64846 -9.58007,-11.04153 -13.22657,-12.88867 -2.12298,-1.07539 -11.87905,-3.5969 -14.02734,-3.625 -0.69561,-0.009 -4.25233,-0.61771 -7.9043,-1.35352 -3.65196,-0.73581 -12.18848,-2.16061 -18.9707,-3.16601 -7.15623,-1.06084 -14.18891,-2.48651 -16.75781,-3.39844 -2.43464,-0.86427 -10.5456,-3.69495 -18.02344,-6.28906 -15.47127,-5.36706 -17.83282,-6.27872 -25.29492,-9.76368 -2.95636,-1.38065 -7.98684,-3.53921 -11.17969,-4.79687 -6.6869,-2.63397 -18.24305,-8.23169 -22.19726,-10.75195 -8.07217,-5.14488 -18.70597,-14.24937 -22.25585,-19.05469 -2.28175,-3.08867 -8.92712,-15.63167 -9.76563,-18.43359 -0.61825,-2.06591 -1.94338,-3.06016 -3.34961,-3.16993 l 0,-7e-5 c -0.20089,-0.0157 -0.40307,-0.0141 -0.60547,0.006 -2.08175,0.2048 -4.14541,2.26315 -4.21093,5.58789 -0.10867,5.51378 0.8781,22.18224 1.46484,24.73438 1.53988,6.69802 10.00404,25.24567 15.07031,33.02344 2.83925,4.35884 12.58122,15.6415 17.375,20.12304 l 3.29296,3.07813 -2.84569,-1.75977 c -10.86883,-6.72178 -13.6199,-8.78506 -22.09961,-16.58203 -1.23599,-1.13647 -3.16889,-2.70204 -4.29688,-3.47851 -1.128,-0.77646 -2.05364,-1.74885 -2.05664,-2.16016 -0.006,-1.11384 -2.14293,-1.58226 -3.99804,-0.87695 -2.94491,1.11965 -3.76722,3.23295 -3.4004,8.73047 0.6464,9.68722 2.88539,16.06511 7.9336,22.59374 10.36629,13.40635 11.43102,14.38579 30.44725,27.95118 6.01794,4.29294 16.88272,10.16854 23.17188,12.53125 l 2.43945,0.91601 -3.47851,-0.34765 c -1.91294,-0.19134 -4.75714,-0.71995 -6.32227,-1.17383 -1.56513,-0.45387 -3.87093,-0.82613 -5.12305,-0.82813 -1.25211,-0.002 -3.24325,-0.5074 -4.42578,-1.12109 -1.19349,-0.61939 -4.12091,-1.24312 -6.57812,-1.40234 -4.20459,-0.27246 -4.51643,-0.19524 -6.23437,1.52343 -1.61897,1.61966 -1.77308,2.11945 -1.45898,4.77149 0.61365,5.18127 4.98734,12.0096 11.90624,18.59375 7.25833,6.90715 13.43412,10.30636 23.50976,12.93554 9.45761,2.46792 14.65657,3.18375 26.64258,3.67383 l 10.09766,0.41406 -3.77344,1.99219 c -2.07581,1.09588 -6.70712,2.89158 -10.29101,3.99024 -6.38565,1.95757 -8.03048,3.12186 -6.53125,4.62109 0.39398,0.39399 0.71679,1.6562 0.71679,2.80469 0,1.82363 0.48673,2.44626 3.84961,4.92187 4.523,3.32965 10.90808,5.52882 19.22266,6.6211 3.12511,0.41054 5.75776,0.82221 5.85156,0.91601 0.0938,0.0938 -0.98131,0.55116 -2.38867,1.01563 -5.96709,1.96932 -7.33036,6.39044 -3.92774,12.72656 2.87906,5.3612 6.12103,8.53836 11.11524,10.89648 8.43467,3.98261 14.36229,6.51342 20.82031,8.88672 2.32081,0.85289 2.05321,2.76953 3.38867,2.79102 l 11.22051,-6.68531 9.84026,-6.83036 z"/>
                </g>
            </svg>',
            esc_attr($bird_id)
        );
        echo '</span>'; // .wb-bird-motion
        echo '</span>';
    }
}

if (! function_exists('weddingblocks_get_placeholder_avatar_svg')) {
    /**
     * Get inline SVG markup for couple photo/avatar placeholder.
     *
     * Used when no photo is uploaded so the frontend displays a clean,
     * elegant avatar silhouette without broken image icons or leaking alt text.
     *
     * @param string $class Additional CSS classes.
     * @return string SVG HTML markup.
     */
    function weddingblocks_get_placeholder_avatar_svg($class = '')
    {
        $classes = trim('wb-avatar-placeholder ' . $class);
        return sprintf(
            '<svg class="%s" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false" preserveAspectRatio="xMidYMax meet"><path d="M12 12c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm0 2c-3.33 0-10 1.67-10 5v5h20v-5c0-3.33-6.67-5-10-5z"/></svg>',
            esc_attr($classes)
        );
    }
}
