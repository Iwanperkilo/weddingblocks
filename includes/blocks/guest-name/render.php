<?php
/**
 * Server-side rendering for the Guest Name block.
 *
 * @package WeddingBlocks
 */

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$prefix = isset( $attributes['prefix'] ) ? $attributes['prefix'] : 'Kepada Yth. Bapak/Ibu/Saudara/i:';
$fallback = isset( $attributes['fallback'] ) ? $attributes['fallback'] : 'Tamu Undangan';
// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$guest_name = isset( $_GET['to'] ) ? sanitize_text_field( wp_unslash( $_GET['to'] ) ) : $fallback;
?>
<?php
$text_color = isset( $attributes['textColor'] ) ? $attributes['textColor'] : '#333333';
$background_color = isset( $attributes['backgroundColor'] ) ? $attributes['backgroundColor'] : 'transparent';
?>
<div class="weddingblocks-guest-name-block" style="background-color: <?php echo esc_attr( $background_color ); ?>;">
    <p class="guest-prefix-text" style="color: <?php echo esc_attr( $text_color ); ?>;"><?php echo esc_html( $prefix ); ?></p>
    <h4 class="guest-name-text" style="color: <?php echo esc_attr( $text_color ); ?>;"><?php echo esc_html( $guest_name ); ?></h4>
</div>
