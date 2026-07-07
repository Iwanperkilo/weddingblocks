<?php
/**
 * Server-side rendering for the Couple Info block.
 *
 * @package WeddingBlocks
 */

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get block attributes (provided by WordPress)
$swap_couple = false;
$layout = 'horizontal';
$show_parents_label = false;
$parents_label_groom = '';
$parents_label_bride = '';
$parents_label_font_size = 15;
$parents_label_font_family = 'georgia';
$parents_label_text_color = '#000000';
$name_font_size = 20;
$name_text_color = '#2c2c2c';
$avatar_border_color = '#b5a46d';
$avatar_border_width = 4;

if ( isset( $attributes['swapCouple'] ) ) {
	$swap_couple = (bool) $attributes['swapCouple'];
}
if ( isset( $attributes['layout'] ) ) {
	$layout = $attributes['layout'];
}
if ( isset( $attributes['showParentsLabel'] ) ) {
	$show_parents_label = (bool) $attributes['showParentsLabel'];
}
if ( isset( $attributes['parentsLabelGroom'] ) ) {
	$parents_label_groom = $attributes['parentsLabelGroom'];
}
if ( isset( $attributes['parentsLabelBride'] ) ) {
	$parents_label_bride = $attributes['parentsLabelBride'];
}
if ( isset( $attributes['parentsLabelFontSize'] ) ) {
	$parents_label_font_size = intval( $attributes['parentsLabelFontSize'] );
}
if ( isset( $attributes['parentsLabelFontFamily'] ) ) {
	$parents_label_font_family = $attributes['parentsLabelFontFamily'];
}
if ( isset( $attributes['parentsLabelTextColor'] ) ) {
	$parents_label_text_color = sanitize_hex_color( $attributes['parentsLabelTextColor'] );
}
if ( isset( $attributes['nameFontSize'] ) ) {
	$name_font_size = intval( $attributes['nameFontSize'] );
}
if ( isset( $attributes['nameTextColor'] ) ) {
	$name_text_color = sanitize_hex_color( $attributes['nameTextColor'] );
}
if ( isset( $attributes['avatarBorderColor'] ) ) {
	$avatar_border_color = sanitize_hex_color( $attributes['avatarBorderColor'] );
}
if ( isset( $attributes['avatarBorderWidth'] ) ) {
	$avatar_border_width = intval( $attributes['avatarBorderWidth'] );
}

// Get post meta
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

// Determine display order
if ( $swap_couple ) {
	$first_name = $groom_name;
	$first_parents = $groom_parents;
	$first_photo_url = $groom_photo_url;
	$second_name = $bride_name;
	$second_parents = $bride_parents;
	$second_photo_url = $bride_photo_url;
} else {
	$first_name = $bride_name;
	$first_parents = $bride_parents;
	$first_photo_url = $bride_photo_url;
	$second_name = $groom_name;
	$second_parents = $groom_parents;
	$second_photo_url = $groom_photo_url;
}

// Parents label defaults
if ( empty( $parents_label_groom ) ) {
	$parents_label_groom = __( 'Putra dari Bapak & Ibu Orang Tua Pria', 'weddingblocks' );
}
if ( empty( $parents_label_bride ) ) {
	$parents_label_bride = __( 'Putri dari Bapak & Ibu Orang Tua Wanita', 'weddingblocks' );
}

// Font family mapping for frontend
switch ( $parents_label_font_family ) {
	case 'playfair':
		$font_family_css = "'Playfair Display', Georgia, serif";
		break;
	case 'greatvibes':
		$font_family_css = "'Great Vibes', cursive";
		break;
	case 'montserrat':
		$font_family_css = "'Montserrat', sans-serif";
		break;
	case 'sans-serif':
		$font_family_css = "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif";
		break;
	case 'georgia':
	default:
		$font_family_css = "Georgia, serif";
		break;
}

$parents_label_style_attr = sprintf(
	'font-size: %dpx; font-family: %s; color: %s;',
	$parents_label_font_size,
	esc_attr( $font_family_css ),
	esc_attr( $parents_label_text_color )
);
$name_style_attr = sprintf(
	'font-size: %dpx; color: %s;',
	$name_font_size,
	esc_attr( $name_text_color )
);
$avatar_style_attr = sprintf(
	'border-color: %s; border-width: %dpx; border-style: solid;',
	esc_attr( $avatar_border_color ),
	$avatar_border_width
);

// Layout class
$layout_class = $layout === 'vertical' ? 'weddingblocks-couple-columns--vertical' : '';
?>
<div class="weddingblocks-couple-columns <?php echo esc_attr( $layout_class ); ?>">
	<div class="weddingblocks-couple-column">
		<div class="weddingblocks-avatar" style="<?php echo esc_attr( $avatar_style_attr ); ?>">
			<img src="<?php echo esc_url( $first_photo_url ); ?>" alt="<?php echo esc_attr( $first_name ); ?>">
		</div>
		<h3 style="<?php echo esc_attr( $name_style_attr ); ?>"><?php echo esc_html( $first_name ); ?></h3>
		<?php if ( $show_parents_label ) : ?>
			<p class="weddingblocks-parents-info">
				<span class="weddingblocks-parents-label" style="<?php echo esc_attr( $parents_label_style_attr ); ?>"><?php echo esc_html( $swap_couple ? $parents_label_groom : $parents_label_bride ); ?></span>
				<span class="weddingblocks-parents-names" style="<?php echo esc_attr( $parents_label_style_attr ); ?>"><?php echo esc_html( $first_parents ); ?></span>
			</p>
		<?php else : ?>
			<p><?php echo esc_html( $first_parents ); ?></p>
		<?php endif; ?>
	</div>
	<div class="weddingblocks-couple-column weddingblocks-separator-column">
		<p class="weddingblocks-ampersand">&</p>
	</div>
	<div class="weddingblocks-couple-column">
		<div class="weddingblocks-avatar" style="<?php echo esc_attr( $avatar_style_attr ); ?>">
			<img src="<?php echo esc_url( $second_photo_url ); ?>" alt="<?php echo esc_attr( $second_name ); ?>">
		</div>
		<h3 style="<?php echo esc_attr( $name_style_attr ); ?>"><?php echo esc_html( $second_name ); ?></h3>
		<?php if ( $show_parents_label ) : ?>
			<p class="weddingblocks-parents-info">
				<span class="weddingblocks-parents-label" style="<?php echo esc_attr( $parents_label_style_attr ); ?>"><?php echo esc_html( $swap_couple ? $parents_label_bride : $parents_label_groom ); ?></span>
				<span class="weddingblocks-parents-names" style="<?php echo esc_attr( $parents_label_style_attr ); ?>"><?php echo esc_html( $second_parents ); ?></span>
			</p>
		<?php else : ?>
			<p><?php echo esc_html( $second_parents ); ?></p>
		<?php endif; ?>
	</div>
</div>