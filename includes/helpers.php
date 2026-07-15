<?php
/**
 * Helper functions for WeddingBlocks.
 *
 * @package WeddingBlocks
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
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
