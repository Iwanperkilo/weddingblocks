<?php

/**
 * Server-side rendering for the RSVP Form block.
 *
 * @package WeddingBlocks
 */

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals

if (! defined('ABSPATH')) {
    exit;
}

$current_post_id = get_the_ID();
// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$guest_name = isset($_GET['to']) ? sanitize_text_field(wp_unslash($_GET['to'])) : '';

// Use the shared color sanitizer (already used by the countdown block) so we
// never trust raw attribute values as CSS, while still allowing rgba()/alpha.
if (function_exists('weddingblocks_sanitize_color')) {
    $button_color       = ! empty($attributes['buttonColor']) ? weddingblocks_sanitize_color($attributes['buttonColor']) : '#b5a46d';
    $input_border_color = ! empty($attributes['inputBorderColor']) ? weddingblocks_sanitize_color($attributes['inputBorderColor']) : 'rgba(181, 164, 109, 0.3)';
} else {
    // Fallback if the helper isn't loaded yet for some reason.
    $button_color       = ! empty($attributes['buttonColor']) ? sanitize_text_field($attributes['buttonColor']) : '#b5a46d';
    $input_border_color = ! empty($attributes['inputBorderColor']) ? sanitize_text_field($attributes['inputBorderColor']) : 'rgba(181, 164, 109, 0.3)';
}

$rsvp_color_vars = sprintf(
    '--wb-rsvp-button-color: %s; --wb-rsvp-input-border-color: %s;',
    esc_attr($button_color),
    esc_attr($input_border_color)
);

// Text customization attributes, with sane fallbacks if omitted.
$show_guests_field   = ! isset($attributes['showGuestsField']) || ! empty($attributes['showGuestsField']);
$max_guests          = ! empty($attributes['maxGuests']) ? max(1, min(50, intval($attributes['maxGuests']))) : 10;
$label_name          = ! empty($attributes['labelName']) ? $attributes['labelName'] : __('Nama Anda', 'weddingblocks');
$label_attendance    = ! empty($attributes['labelAttendance']) ? $attributes['labelAttendance'] : __('Konfirmasi Kehadiran', 'weddingblocks');
$label_message       = ! empty($attributes['labelMessage']) ? $attributes['labelMessage'] : __('Ucapan & Doa Restu', 'weddingblocks');
$placeholder_message = ! empty($attributes['placeholderMessage']) ? $attributes['placeholderMessage'] : __('Berikan ucapan selamat & doa restu terbaik untuk mempelai...', 'weddingblocks');
$button_text         = ! empty($attributes['buttonText']) ? $attributes['buttonText'] : __('Kirim Konfirmasi', 'weddingblocks');
$success_message     = ! empty($attributes['successMessage']) ? $attributes['successMessage'] : __('Konfirmasi RSVP berhasil dikirim!', 'weddingblocks');
$error_message       = ! empty($attributes['errorMessage']) ? $attributes['errorMessage'] : __('Gagal mengirim konfirmasi. Silakan coba lagi.', 'weddingblocks');

// This now respects block supports (spacing/align/anchor from block.json)
// instead of bypassing the block supports engine with a raw <div>.
$wrapper_attributes = get_block_wrapper_attributes(array(
    'class' => 'weddingblocks-rsvp-form-container',
    'style' => $rsvp_color_vars,
));

?>
<div <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already escaped by get_block_wrapper_attributes(). 
        ?>>
    <form
        id="weddingblocks-rsvp-form"
        class="weddingblocks-form"
        data-post-id="<?php echo esc_attr($current_post_id); ?>"
        data-max-guests="<?php echo esc_attr($max_guests); ?>"
        data-success-message="<?php echo esc_attr($success_message); ?>"
        data-error-message="<?php echo esc_attr($error_message); ?>">
        <div class="form-group">
            <label for="rsvp-name"><?php echo esc_html($label_name); ?></label>
            <input type="text" id="rsvp-name" name="guest_name" value="<?php echo esc_attr($guest_name); ?>" placeholder="<?php esc_attr_e('Ketik nama lengkap...', 'weddingblocks'); ?>" required>
        </div>

        <div class="form-group">
            <label for="rsvp-attendance"><?php echo esc_html($label_attendance); ?></label>
            <select id="rsvp-attendance" name="attendance" required>
                <option value="" disabled selected><?php esc_html_e('Pilih kehadiran...', 'weddingblocks'); ?></option>
                <option value="hadir"><?php esc_html_e('Hadir', 'weddingblocks'); ?></option>
                <option value="tidak_hadir"><?php esc_html_e('Tidak Hadir', 'weddingblocks'); ?></option>
                <option value="ragu_ragu"><?php esc_html_e('Ragu-ragu', 'weddingblocks'); ?></option>
            </select>
        </div>

        <?php if ($show_guests_field) : ?>
            <div class="form-group" id="rsvp-guests-group" style="display: none;">
                <label for="rsvp-guests"><?php esc_html_e('Jumlah Tamu (Pax)', 'weddingblocks'); ?></label>
                <input type="number" id="rsvp-guests" name="guests_count" value="1" min="1" max="<?php echo esc_attr($max_guests); ?>">
            </div>
        <?php endif; ?>

        <div class="form-group">
            <label for="rsvp-message"><?php echo esc_html($label_message); ?></label>
            <textarea id="rsvp-message" name="message" rows="4" placeholder="<?php echo esc_attr($placeholder_message); ?>" required></textarea>
        </div>

        <button type="submit" class="weddingblocks-btn-gold" id="rsvp-submit-btn">
            <span class="btn-text"><?php echo esc_html($button_text); ?></span>
            <span class="btn-spinner" style="display: none;"></span>
        </button>
        <div id="rsvp-message-alert" class="rsvp-alert" role="status" aria-live="polite" style="display: none;"></div>
    </form>
</div>