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
    'object-position:%1$s;transform-origin:%1$s;--wb-photo-zoom:%2$s;',
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

// Native WP border attributes with fallback to legacy frame settings.
$style_border = isset($attributes['style']['border']) && is_array($attributes['style']['border']) ? $attributes['style']['border'] : array();

// 1. Border radius handling: native WP border radius with fallback to legacy shape.
$border_radius = '';
if (! empty($style_border['radius'])) {
    $radius_raw = $style_border['radius'];
    if (is_array($radius_raw)) {
        $tl = isset($radius_raw['topLeft']) ? $radius_raw['topLeft'] : (isset($radius_raw['top']) ? $radius_raw['top'] : '0');
        $tr = isset($radius_raw['topRight']) ? $radius_raw['topRight'] : (isset($radius_raw['right']) ? $radius_raw['right'] : '0');
        $br = isset($radius_raw['bottomRight']) ? $radius_raw['bottomRight'] : (isset($radius_raw['bottom']) ? $radius_raw['bottom'] : '0');
        $bl = isset($radius_raw['bottomLeft']) ? $radius_raw['bottomLeft'] : (isset($radius_raw['left']) ? $radius_raw['left'] : '0');
        $border_radius = esc_attr(trim("$tl $tr $br $bl"));
    } elseif (is_string($radius_raw) && '' !== trim($radius_raw)) {
        $border_radius = esc_attr(trim($radius_raw));
    }
}

if ('' === $border_radius) {
    if ('rounded' === $shape) {
        $border_radius = '16px';
    } elseif ('square' === $shape) {
        $border_radius = '0px';
    } else {
        $border_radius = '50%';
    }
}

// 2. Border color: native WP border color / preset slug, with fallback to legacy frameColor.
$border_color = '';
if (! empty($style_border['color'])) {
    $border_color = esc_attr($style_border['color']);
} elseif (! empty($attributes['borderColor'])) {
    $border_color = sprintf('var(--wp--preset--color--%s)', sanitize_key($attributes['borderColor']));
} elseif ($show_frame && '' !== $frame_color) {
    $border_color = esc_attr($frame_color);
}

// 3. Border width: native WP border width, with fallback to legacy frameWidth.
$border_width = '';
if (! empty($style_border['width'])) {
    $width_raw = $style_border['width'];
    if (is_array($width_raw)) {
        $top    = isset($width_raw['top']) ? $width_raw['top'] : '0';
        $right  = isset($width_raw['right']) ? $width_raw['right'] : '0';
        $bottom = isset($width_raw['bottom']) ? $width_raw['bottom'] : '0';
        $left   = isset($width_raw['left']) ? $width_raw['left'] : '0';
        $border_width = esc_attr(trim("$top $right $bottom $left"));
    } elseif (is_string($width_raw) && '' !== trim($width_raw)) {
        $border_width = esc_attr(trim($width_raw));
    }
} elseif (isset($attributes['style']['border']) && array_key_exists('width', $attributes['style']['border'])) {
    $border_width = '0px';
} elseif ($show_frame) {
    $border_width = sprintf('%dpx', $frame_width);
} else {
    $border_width = '0px';
}

// 4. Border style: native WP border style, defaulting to solid if width is present.
$border_style = '';
if (! empty($style_border['style'])) {
    $border_style = esc_attr($style_border['style']);
} elseif ('' !== $border_width && '0px' !== $border_width && '0' !== $border_width) {
    $border_style = 'solid';
}

$placeholder_svg = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%23b5a46d"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>';

$inline_style = sprintf('width:%1$dpx;height:%1$dpx;border-radius:%2$s;', $size, $border_radius);
if ('' !== $border_width) {
    $inline_style .= sprintf('border-width:%s;', $border_width);
}
if ('' !== $border_color) {
    $inline_style .= sprintf('border-color:%s;', $border_color);
}
if ('' !== $border_style) {
    $inline_style .= sprintf('border-style:%s;', $border_style);
}
$frame_class  = ($show_frame || ('' !== $border_width && '0px' !== $border_width && '0' !== $border_width)) ? ' has-frame' : ' no-frame';
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