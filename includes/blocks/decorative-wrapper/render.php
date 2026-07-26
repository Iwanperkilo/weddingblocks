<?php

/**
 * Server-side render for decorative-wrapper.
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

$weddingblocks_mobile_class = ! empty($attributes['enableOnMobile']) ? '' : ' wb-decor-hide-mobile';

$weddingblocks_wrapper_attributes = get_block_wrapper_attributes(
    array(
        'class' => 'wb-decor-wrapper' . esc_attr($weddingblocks_mobile_class),
        'style' => 'position:relative;',
    )
);
?>
<div <?php echo $weddingblocks_wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
        ?>>
    <?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
    ?>
</div>