<?php
/**
 * Server-side rendering for the Couple Info block.
 *
 * @package WeddingBlocks
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$groom_name = get_post_meta( get_the_ID(), 'weddingblocks_groom_name', true );
$groom_parents = get_post_meta( get_the_ID(), 'weddingblocks_groom_parents', true );
$groom_photo = get_post_meta( get_the_ID(), 'weddingblocks_groom_photo', true );

$bride_name = get_post_meta( get_the_ID(), 'weddingblocks_bride_name', true );
$bride_parents = get_post_meta( get_the_ID(), 'weddingblocks_bride_parents', true );
$bride_photo = get_post_meta( get_the_ID(), 'weddingblocks_bride_photo', true );

// Fallbacks
if ( empty( $groom_name ) ) $groom_name = __( 'Mempelai Pria', 'weddingblocks' );
if ( empty( $groom_parents ) ) $groom_parents = __( 'Putra dari Bapak & Ibu Orang Tua Pria', 'weddingblocks' );
if ( empty( $bride_name ) ) $bride_name = __( 'Mempelai Wanita', 'weddingblocks' );
if ( empty( $bride_parents ) ) $bride_parents = __( 'Putri dari Bapak & Ibu Orang Tua Wanita', 'weddingblocks' );

$placeholder_svg = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%23b5a46d"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>';

$groom_photo_url = ! empty( $groom_photo ) ? esc_url( $groom_photo ) : $placeholder_svg;
$bride_photo_url = ! empty( $bride_photo ) ? esc_url( $bride_photo ) : $placeholder_svg;
?>
<div class="weddingblocks-couple-columns">
    <div class="weddingblocks-couple-column">
        <div class="weddingblocks-avatar">
            <img src="<?php echo esc_url( $bride_photo_url ); ?>" alt="<?php echo esc_attr( $bride_name ); ?>">
        </div>
        <h3><?php echo esc_html( $bride_name ); ?></h3>
        <p><?php echo esc_html( $bride_parents ); ?></p>
    </div>
    <div class="weddingblocks-couple-column weddingblocks-separator-column">
        <p class="weddingblocks-ampersand">&</p>
    </div>
    <div class="weddingblocks-couple-column">
        <div class="weddingblocks-avatar">
            <img src="<?php echo esc_url( $groom_photo_url ); ?>" alt="<?php echo esc_attr( $groom_name ); ?>">
        </div>
        <h3><?php echo esc_html( $groom_name ); ?></h3>
        <p><?php echo esc_html( $groom_parents ); ?></p>
    </div>
</div>
