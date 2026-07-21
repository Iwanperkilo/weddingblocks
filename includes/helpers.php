<?php
/**
 * Helper functions for WeddingBlocks.
 *
 * @package WeddingBlocks
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'weddingblocks_get_animation_attrs' ) ) {
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
    function weddingblocks_get_animation_attrs( $attributes ) {
        $style = isset( $attributes['animationStyle'] ) ? $attributes['animationStyle'] : 'none';
        if ( 'none' === $style || empty( $style ) ) {
            return array();
        }

        $allowed = array( 'fadeUp', 'fadeIn', 'slideLeft', 'slideRight', 'zoomIn' );
        if ( ! in_array( $style, $allowed, true ) ) {
            return array();
        }

        return array(
            'data-wb-anim'     => $style,
            'data-wb-duration' => isset( $attributes['animationDuration'] ) ? absint( $attributes['animationDuration'] ) : 600,
            'data-wb-delay'    => isset( $attributes['animationDelay'] ) ? absint( $attributes['animationDelay'] ) : 0,
        );
    }
}

if ( ! function_exists( 'weddingblocks_sanitize_color' ) ) {
    /**
     * Sanitize a color value, allowing hex (#fff / #ffffff / #ffffffff with alpha),
     * rgb()/rgba(), and the literal 'transparent'. Falls back to an empty string
     * (caller decides the default) if the value doesn't match any allowed format.
     *
     * @param string $color Raw color value from block attributes.
     * @return string
     */
    function weddingblocks_sanitize_color( $color ) {
        if ( empty( $color ) ) {
            return '';
        }

        $color = trim( $color );

        if ( 'transparent' === strtolower( $color ) ) {
            return 'transparent';
        }

        // #fff, #ffffff, or #ffffffff (8-digit hex with alpha channel).
        if ( preg_match( '/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6}|[A-Fa-f0-9]{8})$/', $color ) ) {
            return $color;
        }

        // rgb(r, g, b) or rgba(r, g, b, a).
        if ( preg_match( '/^rgba?\(\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*\d{1,3}\s*(,\s*(0|1|0?\.\d+)\s*)?\)$/i', $color ) ) {
            return $color;
        }

        return '';
    }
}

if ( ! function_exists( 'weddingblocks_get_contrast_color' ) ) {
    /**
     * Auto-contrast text color for buttons, so light accent colors
     * don't render invisible white-on-white text.
     *
     * @param string $hex_color
     * @return string
     */
    function weddingblocks_get_contrast_color( $hex_color ) {
        $hex_color = ltrim( (string) $hex_color, '#' );
        if ( strlen( $hex_color ) === 3 ) {
            $hex_color = $hex_color[0] . $hex_color[0] . $hex_color[1] . $hex_color[1] . $hex_color[2] . $hex_color[2];
        }
        if ( strlen( $hex_color ) !== 6 ) {
            return '#ffffff';
        }
        $r = hexdec( substr( $hex_color, 0, 2 ) );
        $g = hexdec( substr( $hex_color, 2, 2 ) );
        $b = hexdec( substr( $hex_color, 4, 2 ) );
        // Relative luminance (per WCAG).
        $luminance = ( 0.299 * $r + 0.587 * $g + 0.114 * $b ) / 255;
        return $luminance > 0.6 ? '#1c1d1d' : '#ffffff';
    }
}

