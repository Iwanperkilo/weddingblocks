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