<?php

/**
 * Server-side rendering for the Couple Photo block.
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
$shape      = isset($attributes['shape']) ? sanitize_key($attributes['shape']) : 'circle';
$shape      = in_array($shape, array('circle', 'rounded', 'square'), true) ? $shape : 'circle';
$size       = isset($attributes['size']) ? (int) $attributes['size'] : 200;
if ($size < 40) {
    $size = 40;
}
if ($size > 800) {
    $size = 800;
}
$show_frame  = isset($attributes['showFrame']) ? (bool) $attributes['showFrame'] : true;
$frame_color = isset($attributes['frameColor']) && preg_match('/^#[0-9a-fA-F]{3,8}$/', $attributes['frameColor']) ? $attributes['frameColor'] : '';
$frame_width = isset($attributes['frameWidth']) ? (int) $attributes['frameWidth'] : 3;
if ($frame_width < 1) {
    $frame_width = 1;
}
if ($frame_width > 10) {
    $frame_width = 10;
}
$align      = isset($attributes['align']) ? sanitize_key($attributes['align']) : 'center';
$align      = in_array($align, array('left', 'center', 'right'), true) ? $align : 'center';

// Focal point (free-form, admin-set at theme-design time) + zoom: lets a
// section frame just the face, upper body, etc. instead of always showing
// the photo's full natural composition. object-position anchors which part
// of the photo stays visible under object-fit:cover; the transform:scale()
// zooms in further from that same anchor point (clipped by the figure's
// own overflow:hidden), so no destructive crop or extra image variant is
// needed — it's the same photo used elsewhere (e.g. section background).
$focal_point = isset($attributes['photoFocalPoint']) && is_array($attributes['photoFocalPoint']) ? $attributes['photoFocalPoint'] : array();
$focal_x     = isset($focal_point['x']) ? max(0, min(1, (float) $focal_point['x'])) : 0.5;
$focal_y     = isset($focal_point['y']) ? max(0, min(1, (float) $focal_point['y'])) : 0.5;
$focal_css   = sprintf('%d%% %d%%', round($focal_x * 100), round($focal_y * 100));

$zoom = isset($attributes['photoZoom']) ? (float) $attributes['photoZoom'] : 100;
$zoom = max(100, min(300, $zoom));

$photo_img_style = sprintf(
    'object-position:%1$s;transform-origin:%1$s;transform:scale(%2$s);',
    $focal_css,
    $zoom / 100
);

if ('bride' === $role) {
    $photo     = ! empty($attributes['bridePhoto']) ? $attributes['bridePhoto'] : get_post_meta(get_the_ID(), 'weddingblocks_bride_photo', true);
    $name      = ! empty($attributes['brideName'])  ? $attributes['brideName']  : get_post_meta(get_the_ID(), 'weddingblocks_bride_name', true);
    $fallback  = __('Mempelai Wanita', 'weddingblocks');
} else {
    $photo     = ! empty($attributes['groomPhoto']) ? $attributes['groomPhoto'] : get_post_meta(get_the_ID(), 'weddingblocks_groom_photo', true);
    $name      = ! empty($attributes['groomName'])  ? $attributes['groomName']  : get_post_meta(get_the_ID(), 'weddingblocks_groom_name', true);
    $fallback  = __('Mempelai Pria', 'weddingblocks');
}
if ('' === $name) {
    $name = $fallback;
}

$placeholder_svg = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%23b5a46d"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>';

$inline_style = sprintf('width:%1$dpx;height:%1$dpx;', $size);
if ($show_frame) {
    if ('' !== $frame_color) {
        $inline_style .= sprintf('border-color:%s;', $frame_color);
    }
    $inline_style .= sprintf('border-width:%dpx;', $frame_width);
}
$frame_class  = $show_frame ? ' has-frame' : ' no-frame';
$shape_class  = ' shape-' . sanitize_html_class($shape);
$wrapper_class = 'weddingblocks-atomic-couple-photo role-' . sanitize_html_class($role) . ' align-' . sanitize_html_class($align) . $frame_class;
$figure_class  = 'atomic-photo' . $shape_class;

$wrapper_attributes = get_block_wrapper_attributes(
    array_merge(
        array('class' => $wrapper_class),
        weddingblocks_get_animation_attrs($attributes)
    )
);

?>
<div <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
        ?>>
    <figure class="<?php echo esc_attr($figure_class); ?>" style="<?php echo esc_attr($inline_style); ?>">
        <?php if ('' !== $photo) : ?>
            <img src="<?php echo esc_url($photo); ?>" alt="<?php echo esc_attr($name); ?>" style="<?php echo esc_attr($photo_img_style); ?>" />
        <?php else : ?>
            <img src="<?php echo esc_url($placeholder_svg); ?>" alt="<?php echo esc_attr($name); ?>" class="atomic-photo-placeholder" />
        <?php endif; ?>
    </figure>
</div>