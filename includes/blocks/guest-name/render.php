<?php
/**
 * Server-side rendering for the Guest Name block.
 *
 * @package WeddingBlocks
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$prefix = isset( $attributes['prefix'] ) ? $attributes['prefix'] : 'Kepada Yth. Bapak/Ibu/Saudara/i:';
$fallback = isset( $attributes['fallback'] ) ? $attributes['fallback'] : 'Tamu Undangan';
$guest_name = isset( $_GET['to'] ) ? sanitize_text_field( $_GET['to'] ) : $fallback;
?>
<div class="weddingblocks-guest-name-block">
    <p class="guest-prefix-text"><?php echo esc_html( $prefix ); ?></p>
    <h4 class="guest-name-text"><?php echo esc_html( $guest_name ); ?></h4>
</div>
