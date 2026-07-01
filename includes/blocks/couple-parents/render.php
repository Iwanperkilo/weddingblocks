<?php
/**
 * Server-side rendering for the Couple Parents block.
 *
 * @package WeddingBlocks
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$role       = isset( $attributes['role'] ) ? sanitize_key( $attributes['role'] ) : 'groom';
$label      = isset( $attributes['label'] ) ? $attributes['label'] : '';
$show_label = isset( $attributes['showLabel'] ) ? (bool) $attributes['showLabel'] : true;
$align      = isset( $attributes['align'] ) ? sanitize_key( $attributes['align'] ) : 'center';
$align      = in_array( $align, array( 'left', 'center', 'right' ), true ) ? $align : 'center';

if ( 'bride' === $role ) {
    $parents  = ! empty( $attributes['brideParents'] ) ? $attributes['brideParents'] : get_post_meta( get_the_ID(), 'weddingblocks_bride_parents', true );
    $default_label = __( 'Putri dari', 'weddingblocks' );
    $fallback       = __( 'Bapak & Ibu Orang Tua Wanita', 'weddingblocks' );
} else {
    $parents  = ! empty( $attributes['groomParents'] ) ? $attributes['groomParents'] : get_post_meta( get_the_ID(), 'weddingblocks_groom_parents', true );
    $default_label = __( 'Putra dari', 'weddingblocks' );
    $fallback       = __( 'Bapak & Ibu Orang Tua Pria', 'weddingblocks' );
}

if ( '' === $parents ) { $parents = $fallback; }
if ( '' === $label )   { $label   = $default_label; }

$wrapper_class = 'weddingblocks-atomic-couple-parents role-' . sanitize_html_class( $role ) . ' align-' . sanitize_html_class( $align );
?>
<div class="<?php echo esc_attr( $wrapper_class ); ?>">
    <?php if ( $show_label ) : ?>
        <span class="atomic-parents-label"><?php echo esc_html( $label ); ?></span>
    <?php endif; ?>
    <span class="atomic-parents-names"><?php echo esc_html( $parents ); ?></span>
</div>
