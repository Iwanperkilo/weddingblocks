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

/* --------------------------------------------------------------
 * Get groom display name
 * -------------------------------------------------------------- */
$groom_display = get_post_meta( get_the_ID(), 'weddingblocks_groom_nickname', true );
if ( empty( $groom_display ) ) {
    $groom_display = get_post_meta( get_the_ID(), 'weddingblocks_groom_name', true );
}
if ( empty( $groom_display ) ) {
    $groom_display = __( 'Mempelai Pria', 'weddingblocks' );
}

/* --------------------------------------------------------------
 * Get bride display name
 * -------------------------------------------------------------- */
$bride_display = get_post_meta( get_the_ID(), 'weddingblocks_bride_nickname', true );
if ( empty( $bride_display ) ) {
    $bride_display = get_post_meta( get_the_ID(), 'weddingblocks_bride_name', true );
}
if ( empty( $bride_display ) ) {
    $bride_display = __( 'Mempelai Wanita', 'weddingblocks' );
}

/* --------------------------------------------------------------
 * Get style attributes
 * -------------------------------------------------------------- */
$text_color     = ! empty( $block_attributes['textColor'] ) ? $block_attributes['textColor'] : '#ffffff';
$text_transform = ! empty( $block_attributes['textTransform'] ) ? $block_attributes['textTransform'] : 'none';
$separator      = isset( $block_attributes['separator'] ) ? $block_attributes['separator'] : '&';

/* --------------------------------------------------------------
 * Text transformation helper
 * -------------------------------------------------------------- */
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

/* --------------------------------------------------------------
 * Apply transformations
 * -------------------------------------------------------------- */
$groom_transformed = $transform_text( $groom_display, $text_transform );
$bride_transformed = $transform_text( $bride_display, $text_transform );
?>

<h1 class="weddingblocks-cover-title text-align-center"
    style="text-align: center; color: <?php echo esc_attr( $text_color ); ?> !important; text-transform: <?php echo esc_attr( $text_transform ); ?>;">
    <?php echo esc_html( $groom_transformed ); ?> <?php echo esc_html( $separator ); ?> <?php echo esc_html( $bride_transformed ); ?>
</h1>
