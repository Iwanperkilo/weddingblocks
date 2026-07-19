<?php
/**
 * Server-side rendering for the Couple Title block.
 *
 * @package WeddingBlocks
 */

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$block_attributes = array();
if ( isset( $attributes ) ) {
    if ( is_array( $attributes ) ) {
        $block_attributes = $attributes;
    } elseif ( is_object( $attributes ) && isset( $attributes->attributes ) && is_array( $attributes->attributes ) ) {
        $block_attributes = $attributes->attributes;
    }
}

$groom_display = get_post_meta( get_the_ID(), 'weddingblocks_groom_nickname', true );
if ( empty( $groom_display ) ) {
    $groom_display = get_post_meta( get_the_ID(), 'weddingblocks_groom_name', true );
}
if ( empty( $groom_display ) ) {
    $groom_display = __( 'Mempelai Pria', 'weddingblocks' );
}

$bride_display = get_post_meta( get_the_ID(), 'weddingblocks_bride_nickname', true );
if ( empty( $bride_display ) ) {
    $bride_display = get_post_meta( get_the_ID(), 'weddingblocks_bride_name', true );
}
if ( empty( $bride_display ) ) {
    $bride_display = __( 'Mempelai Wanita', 'weddingblocks' );
}

$text_color = ! empty( $block_attributes['textColor'] ) ? $block_attributes['textColor'] : '#ffffff';

$allowed_transforms = array( 'none', 'uppercase', 'lowercase', 'capitalize' );
$text_transform      = isset( $block_attributes['textTransform'] ) ? $block_attributes['textTransform'] : 'none';
if ( ! in_array( $text_transform, $allowed_transforms, true ) ) {
    $text_transform = 'none';
}

$allowed_aligns = array( 'left', 'center', 'right' );
$text_align      = isset( $block_attributes['textAlign'] ) ? $block_attributes['textAlign'] : 'center';
if ( ! in_array( $text_align, $allowed_aligns, true ) ) {
    $text_align = 'center';
}

$separator    = isset( $block_attributes['separator'] ) ? $block_attributes['separator'] : '&';
$text_shadow  = ! empty( $block_attributes['textShadow'] );
$font_size    = ! empty( $block_attributes['style']['typography']['fontSize'] ) ? $block_attributes['style']['typography']['fontSize'] : '';

$transform_text = static function ( $text, $transform ) {
    if ( $transform === 'uppercase' ) {
        return strtoupper( $text );
    } elseif ( $transform === 'lowercase' ) {
        return strtolower( $text );
    } elseif ( $transform === 'capitalize' ) {
        return ucfirst( strtolower( $text ) );
    }
    return $text;
};

$groom_transformed = $transform_text( $groom_display, $text_transform );
$bride_transformed = $transform_text( $bride_display, $text_transform );

$inline_style = sprintf( 'color: %s !important; text-transform: %s; text-align: %s;',
    esc_attr( $text_color ),
    esc_attr( $text_transform ),
    esc_attr( $text_align )
);
if ( ! empty( $font_size ) ) {
    $inline_style .= sprintf( ' font-size: %s;', esc_attr( $font_size ) );
}
if ( $text_shadow ) {
    $inline_style .= ' text-shadow: 0 2px 6px rgba(0,0,0,0.45);';
}

$extra_classes = 'weddingblocks-cover-title';
if ( $text_shadow ) {
    $extra_classes .= ' has-text-shadow';
}

$wrapper_attributes = get_block_wrapper_attributes(
    array_merge(
        array(
            'class' => $extra_classes,
            'style' => $inline_style,
        ),
        weddingblocks_get_animation_attrs( $block_attributes )
    )
);
?>

<h1 <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
    <?php echo esc_html( $groom_transformed ); ?> <?php echo esc_html( $separator ); ?> <?php echo esc_html( $bride_transformed ); ?>
</h1>