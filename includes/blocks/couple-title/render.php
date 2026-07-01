<?php
/**
 * Server-side rendering for the Couple Title block.
 *
 * @package WeddingBlocks
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
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
?>
<h1 class="weddingblocks-cover-title text-align-center" style="text-align: center;">
    <?php echo esc_html( $groom_display ) . ' & ' . esc_html( $bride_display ); ?>
</h1>
