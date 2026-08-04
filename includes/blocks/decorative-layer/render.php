<?php

/**
 * Server-side render for decorative-layer.
 *
 * @package WeddingBlocks
 *
 * @var array    $attributes
 * @var string   $content
 * @var WP_Block $block
 */

if (! defined('ABSPATH')) {
    exit;
}

$weddingblocks_decoration_type = $block->context['weddingblocks/decorationType'] ?? 'dots';
$weddingblocks_density         = $block->context['weddingblocks/density'] ?? 'medium';
$weddingblocks_layer           = $block->context['weddingblocks/layer'] ?? 'behind';

$weddingblocks_density_map = array(
    'low'    => array('dots' => 10,  'fly' => 2),
    'medium' => array('dots' => 20, 'fly' => 4),
    'high'   => array('dots' => 30, 'fly' => 6),
);
$weddingblocks_count = $weddingblocks_density_map[$weddingblocks_density] ?? $weddingblocks_density_map['medium'];

$weddingblocks_butterfly_pair_count = 1;

$weddingblocks_wrapper_attributes = get_block_wrapper_attributes(
    array(
        'class' => 'wb-decor-layer wb-decor-layer--' . esc_attr($weddingblocks_layer),
    )
);

/**
 * Helper: render satu kepingan salju dengan style random via inline CSS var.
 */
if (! function_exists('weddingblocks_render_dot')) {
    function weddingblocks_render_dot()
    {
        $size   = wp_rand(4, 8);
        $left   = wp_rand(2, 96);
        $dur    = wp_rand(10, 22);
        $delay  = wp_rand(0, 14);
        $drift  = wp_rand(-35, 35);
        $op     = wp_rand(45, 90) / 100;
        $style  = sprintf(
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

/**
 * Helper: render satu kupu-kupu (sayap bergradasi + vena, lebih detail/menarik).
 */
if (! function_exists('weddingblocks_render_single_butterfly')) {
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

/**
 * Helper: render SEPASANG kupu-kupu yang terbang berdekatan
 */
if (! function_exists('weddingblocks_render_butterfly_pair')) {
    function weddingblocks_render_butterfly_pair()
    {
        // Margin posisi (area sempit: top 20-55%, left 10-70%)
        $top   = wp_rand(20, 55);
        $left  = wp_rand(10, 70);
        $scale = wp_rand(85, 130) / 100;

        $dir = (1 === wp_rand(0, 1)) ? 1 : -1;

        $dur       = wp_rand(22, 34);
        $delay     = wp_rand(0, 10);

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

/**
 * Helper: render burung terbang (SVG siluet - Tampak Samping).
 */
if (! function_exists('weddingblocks_render_flyer')) {
    function weddingblocks_render_flyer($type)
    {
        $top   = wp_rand(5, 75);
        $dur   = wp_rand(11, 16);
        $delay = wp_rand(0, 14);
        $scale = wp_rand(75, 125) / 100;
        $flap_dur = wp_rand(35, 50) / 100;

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
?>
<div <?php echo $weddingblocks_wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
        ?> aria-hidden="true">
    <?php
    if ($weddingblocks_decoration_type === 'dots') {
        for ($weddingblocks_i = 0; $weddingblocks_i < $weddingblocks_count['dots']; $weddingblocks_i++) {
            weddingblocks_render_dot();
        }
    } elseif ($weddingblocks_decoration_type === 'butterfly') {
        for ($weddingblocks_i = 0; $weddingblocks_i < $weddingblocks_butterfly_pair_count; $weddingblocks_i++) {
            weddingblocks_render_butterfly_pair();
        }
    } elseif ($weddingblocks_decoration_type === 'bird') {
        for ($weddingblocks_i = 0; $weddingblocks_i < $weddingblocks_count['fly']; $weddingblocks_i++) {
            weddingblocks_render_flyer('bird');
        }
    }
    ?>
</div>