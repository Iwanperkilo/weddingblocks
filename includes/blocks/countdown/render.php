<?php
/**
 * Server-side rendering for the Countdown block.
 *
 * @package WeddingBlocks
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$target = '';
if ( ! empty( $attributes['targetDate'] ) ) {
    $target = $attributes['targetDate'];
} else {
    $target = get_post_meta( get_the_ID(), 'weddingblocks_wedding_date', true );
}
if ( empty( $target ) ) {
    $target = '2026-12-31T09:00';
}
$target = esc_attr( $target );
?>
<div class="weddingblocks-countdown" data-target="<?php echo esc_attr( $target ); ?>">
    <div class="countdown-item">
        <span class="countdown-value days">00</span>
        <span class="countdown-label"><?php _e( 'Hari', 'weddingblocks' ); ?></span>
    </div>
    <div class="countdown-item">
        <span class="countdown-value hours">00</span>
        <span class="countdown-label"><?php _e( 'Jam', 'weddingblocks' ); ?></span>
    </div>
    <div class="countdown-item">
        <span class="countdown-value minutes">00</span>
        <span class="countdown-label"><?php _e( 'Menit', 'weddingblocks' ); ?></span>
    </div>
    <div class="countdown-item">
        <span class="countdown-value seconds">00</span>
        <span class="countdown-label"><?php _e( 'Detik', 'weddingblocks' ); ?></span>
    </div>
</div>
