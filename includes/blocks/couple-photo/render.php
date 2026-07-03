<?php
/**
 * Server-side rendering for the Couple Photo block.
 *
 * @package WeddingBlocks
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$role       = isset( $attributes['role'] ) ? sanitize_key( $attributes['role'] ) : 'groom';
$shape      = isset( $attributes['shape'] ) ? sanitize_key( $attributes['shape'] ) : 'circle';
$shape      = in_array( $shape, array( 'circle', 'rounded', 'square' ), true ) ? $shape : 'circle';
$size       = isset( $attributes['size'] ) ? (int) $attributes['size'] : 200;
if ( $size < 40 )  { $size = 40; }
if ( $size > 800 ) { $size = 800; }
$show_frame = isset( $attributes['showFrame'] ) ? (bool) $attributes['showFrame'] : true;
$align      = isset( $attributes['align'] ) ? sanitize_key( $attributes['align'] ) : 'center';
$align      = in_array( $align, array( 'left', 'center', 'right' ), true ) ? $align : 'center';

if ( 'bride' === $role ) {
    $photo     = ! empty( $attributes['bridePhoto'] ) ? $attributes['bridePhoto'] : get_post_meta( get_the_ID(), 'weddingblocks_bride_photo', true );
    $name      = ! empty( $attributes['brideName'] )  ? $attributes['brideName']  : get_post_meta( get_the_ID(), 'weddingblocks_bride_name', true );
    $fallback  = __( 'Mempelai Wanita', 'weddingblocks' );
} else {
    $photo     = ! empty( $attributes['groomPhoto'] ) ? $attributes['groomPhoto'] : get_post_meta( get_the_ID(), 'weddingblocks_groom_photo', true );
    $name      = ! empty( $attributes['groomName'] )  ? $attributes['groomName']  : get_post_meta( get_the_ID(), 'weddingblocks_groom_name', true );
    $fallback  = __( 'Mempelai Pria', 'weddingblocks' );
}
if ( '' === $name ) { $name = $fallback; }

$placeholder_svg = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%23b5a46d"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>';

$inline_style = sprintf( 'width:%1$dpx;height:%1$dpx;', $size );
$frame_class  = $show_frame ? ' has-frame' : ' no-frame';
$shape_class  = ' shape-' . sanitize_html_class( $shape );
$wrapper_class = 'weddingblocks-atomic-couple-photo role-' . sanitize_html_class( $role ) . ' align-' . sanitize_html_class( $align ) . $frame_class;
$figure_class  = 'atomic-photo' . $shape_class;

?>
<div class="<?php echo esc_attr( $wrapper_class ); ?>">
    <figure class="<?php echo esc_attr( $figure_class ); ?>" style="<?php echo esc_attr( $inline_style ); ?>">
        <?php if ( '' !== $photo ) : ?>
            <img src="<?php echo esc_url( $photo ); ?>" alt="<?php echo esc_attr( $name ); ?>" />
        <?php else : ?>
            <img src="<?php echo esc_url( $placeholder_svg ); ?>" alt="<?php echo esc_attr( $name ); ?>" class="atomic-photo-placeholder" />
        <?php endif; ?>
    </figure>
