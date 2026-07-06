<?php
/**
 * Server-side rendering for the RSVP Form block.
 *
 * @package WeddingBlocks
 */

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$current_post_id = get_the_ID();
// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$guest_name = isset( $_GET['to'] ) ? sanitize_text_field( wp_unslash( $_GET['to'] ) ) : '';

$button_color       = ! empty( $attributes['buttonColor'] ) ? $attributes['buttonColor'] : '#b5a46d';
$input_border_color = ! empty( $attributes['inputBorderColor'] ) ? $attributes['inputBorderColor'] : 'rgba(181, 164, 109, 0.3)';

$rsvp_color_vars = sprintf(
    '--wb-rsvp-button-color: %s; --wb-rsvp-input-border-color: %s;',
    esc_attr( $button_color ),
    esc_attr( $input_border_color )
);

?>
<div class="weddingblocks-rsvp-form-container" style="<?php echo esc_attr( $rsvp_color_vars ); ?>">
    <form id="weddingblocks-rsvp-form" class="weddingblocks-form" data-post-id="<?php echo esc_attr( $current_post_id ); ?>">
        <div class="form-group">
            <label for="rsvp-name"><?php esc_html_e( 'Nama Anda', 'weddingblocks' ); ?></label>
            <input type="text" id="rsvp-name" name="guest_name" value="<?php echo esc_attr( $guest_name ); ?>" placeholder="<?php esc_attr_e( 'Ketik nama lengkap...', 'weddingblocks' ); ?>" required>
        </div>

        <div class="form-group">
            <label for="rsvp-attendance"><?php esc_html_e( 'Konfirmasi Kehadiran', 'weddingblocks' ); ?></label>
            <select id="rsvp-attendance" name="attendance" required>
                <option value="" disabled selected><?php esc_html_e( 'Pilih kehadiran...', 'weddingblocks' ); ?></option>
                <option value="hadir"><?php esc_html_e( 'Hadir', 'weddingblocks' ); ?></option>
                <option value="tidak_hadir"><?php esc_html_e( 'Tidak Hadir', 'weddingblocks' ); ?></option>
                <option value="ragu_ragu"><?php esc_html_e( 'Ragu-ragu', 'weddingblocks' ); ?></option>
            </select>
        </div>

        <div class="form-group" id="rsvp-guests-group" style="display: none;">
            <label for="rsvp-guests"><?php esc_html_e( 'Jumlah Tamu (Pax)', 'weddingblocks' ); ?></label>
            <input type="number" id="rsvp-guests" name="guests_count" value="1" min="1" max="10">
        </div>

        <div class="form-group">
            <label for="rsvp-message"><?php esc_html_e( 'Ucapan & Doa Restu', 'weddingblocks' ); ?></label>
            <textarea id="rsvp-message" name="message" rows="4" placeholder="<?php esc_attr_e( 'Berikan ucapan selamat & doa restu terbaik untuk mempelai...', 'weddingblocks' ); ?>" required></textarea>
        </div>

        <button type="submit" class="weddingblocks-btn-gold" id="rsvp-submit-btn">
            <span class="btn-text"><?php esc_html_e( 'Kirim Konfirmasi', 'weddingblocks' ); ?></span>
            <span class="btn-spinner" style="display: none;"></span>
        </button>
        <div id="rsvp-message-alert" class="rsvp-alert" style="display: none;"></div>
    </form>
</div>