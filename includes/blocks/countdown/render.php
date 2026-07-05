<?php
/**
 * Server-side rendering for the Countdown block.
 *
 * @package WeddingBlocks
 */

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

$target = '';
if ( ! empty( $block_attributes['targetDate'] ) ) {
    $target = $block_attributes['targetDate'];
} else {
    $target = get_post_meta( get_the_ID(), 'weddingblocks_wedding_date', true );
}
if ( empty( $target ) ) {
    $target = '2026-12-31T09:00';
}
$target = esc_attr( $target );

$text_color           = ! empty( $block_attributes['textColor'] ) ? $block_attributes['textColor'] : '#b5a46d';
$label_color          = ! empty( $block_attributes['labelColor'] ) ? $block_attributes['labelColor'] : '#888888';
$box_background_color = ! empty( $block_attributes['boxBackgroundColor'] ) ? $block_attributes['boxBackgroundColor'] : '#ffffff';
$border_color         = ! empty( $block_attributes['borderColor'] ) ? $block_attributes['borderColor'] : '#b5a46d';
?>
<div class="weddingblocks-countdown" data-target="<?php echo esc_attr( $target ); ?>">
    <div class="countdown-item" style="background-color: <?php echo esc_attr( $box_background_color ); ?>; border-color: <?php echo esc_attr( $border_color ); ?>;">
        <span class="countdown-value days" style="color: <?php echo esc_attr( $text_color ); ?>;">00</span>
        <span class="countdown-label" style="color: <?php echo esc_attr( $label_color ); ?>;"><?php _e( 'Hari', 'weddingblocks' ); ?></span>
    </div>
    <div class="countdown-item" style="background-color: <?php echo esc_attr( $box_background_color ); ?>; border-color: <?php echo esc_attr( $border_color ); ?>;">
        <span class="countdown-value hours" style="color: <?php echo esc_attr( $text_color ); ?>;">00</span>
        <span class="countdown-label" style="color: <?php echo esc_attr( $label_color ); ?>;"><?php _e( 'Jam', 'weddingblocks' ); ?></span>
    </div>
    <div class="countdown-item" style="background-color: <?php echo esc_attr( $box_background_color ); ?>; border-color: <?php echo esc_attr( $border_color ); ?>;">
        <span class="countdown-value minutes" style="color: <?php echo esc_attr( $text_color ); ?>;">00</span>
        <span class="countdown-label" style="color: <?php echo esc_attr( $label_color ); ?>;"><?php _e( 'Menit', 'weddingblocks' ); ?></span>
    </div>
    <div class="countdown-item" style="background-color: <?php echo esc_attr( $box_background_color ); ?>; border-color: <?php echo esc_attr( $border_color ); ?>;">
        <span class="countdown-value seconds" style="color: <?php echo esc_attr( $text_color ); ?>;">00</span>
        <span class="countdown-label" style="color: <?php echo esc_attr( $label_color ); ?>;"><?php _e( 'Detik', 'weddingblocks' ); ?></span>
    </div>
</div>
