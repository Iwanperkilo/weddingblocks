<?php

/**
 * Server-side rendering for the Event Info block.
 *
 * @package WeddingBlocks
 */

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals

if (! defined('ABSPATH')) {
    exit;
}

$akad_time     = get_post_meta(get_the_ID(), 'weddingblocks_akad_time_label', true);
$akad_loc_name = get_post_meta(get_the_ID(), 'weddingblocks_akad_location_name', true);
$akad_loc_addr = get_post_meta(get_the_ID(), 'weddingblocks_akad_location_address', true);

$resepsi_time     = get_post_meta(get_the_ID(), 'weddingblocks_resepsi_time_label', true);
$resepsi_loc_name = get_post_meta(get_the_ID(), 'weddingblocks_resepsi_location_name', true);
$resepsi_loc_addr = get_post_meta(get_the_ID(), 'weddingblocks_resepsi_location_address', true);

$maps_coords = get_post_meta(get_the_ID(), 'weddingblocks_maps_coords', true);
$whatsapp    = get_post_meta(get_the_ID(), 'weddingblocks_whatsapp_number', true);

$layout_variation = isset($attributes['layoutVariation']) ? $attributes['layoutVariation'] : 'horizontal';

// Use the shared alpha-aware sanitizer when available, falling back to core's hex-only sanitizer.
$sanitize_color = function_exists('weddingblocks_sanitize_color')
    ? 'weddingblocks_sanitize_color'
    : 'sanitize_hex_color';

$primary_color = ! empty($attributes['primaryColor']) ? call_user_func($sanitize_color, $attributes['primaryColor']) : '#b5a46d';
$accent_color  = ! empty($attributes['accentColor']) ? call_user_func($sanitize_color, $attributes['accentColor']) : '#b5a46d';
$text_color    = ! empty($attributes['textColor']) ? call_user_func($sanitize_color, $attributes['textColor']) : '';

if (empty($primary_color)) {
    $primary_color = '#b5a46d';
}

if (empty($accent_color)) {
    $accent_color = '#b5a46d';
}

// Auto-calculate WCAG-safe button text color based on the chosen primary color.
$button_text_color = function_exists('weddingblocks_get_contrast_color')
    ? weddingblocks_get_contrast_color($primary_color)
    : '#ffffff';

$allowed_variants = array('vertical', 'horizontal', 'timeline');

if (! in_array($layout_variation, $allowed_variants, true)) {
    $layout_variation = 'horizontal';
}

if (empty($akad_time)) {
    $akad_time = 'Pukul 08:00 - 10:00 WIB';
}

if (empty($akad_loc_name)) {
    $akad_loc_name = 'Masjid Agung Kota';
}

if (empty($akad_loc_addr)) {
    $akad_loc_addr = 'Jl. Cempaka No. 12';
}

if (empty($resepsi_time)) {
    $resepsi_time = 'Pukul 11:00 - Selesai';
}

if (empty($resepsi_loc_name)) {
    $resepsi_loc_name = 'Gedung Serbaguna Indah';
}

if (empty($resepsi_loc_addr)) {
    $resepsi_loc_addr = 'Jl. Melati No. 45';
}

// Custom event labels (fallback to default translated strings when empty).
$akad_event_label    = isset($attributes['akadEventLabel']) && '' !== trim($attributes['akadEventLabel'])
    ? $attributes['akadEventLabel']
    : __('Akad Nikah', 'weddingblocks');

$resepsi_event_label = isset($attributes['resepsiEventLabel']) && '' !== trim($attributes['resepsiEventLabel'])
    ? $attributes['resepsiEventLabel']
    : __('Resepsi', 'weddingblocks');

// Button visibility toggles (default true when attribute is not explicitly set).
$show_maps_button     = ! isset($attributes['showMapsButton']) || ! empty($attributes['showMapsButton']);
$show_whatsapp_button = ! isset($attributes['showWhatsappButton']) || ! empty($attributes['showWhatsappButton']);

$whatsapp_button_label = isset($attributes['whatsappButtonLabel']) && '' !== trim($attributes['whatsappButtonLabel'])
    ? $attributes['whatsappButtonLabel']
    : __('Hubungi Admin (WhatsApp)', 'weddingblocks');

$events = array(
    array(
        'title' => $akad_event_label,
        'time'  => $akad_time,
        'name'  => $akad_loc_name,
        'addr'  => $akad_loc_addr,
    ),
    array(
        'title' => $resepsi_event_label,
        'time'  => $resepsi_time,
        'name'  => $resepsi_loc_name,
        'addr'  => $resepsi_loc_addr,
    ),
);

$inline_style_vars = '--wb-event-primary-color: ' . $primary_color . '; --wb-event-accent-color: ' . $accent_color . '; --wb-event-button-text-color: ' . $button_text_color . ';';

if (! empty($text_color)) {
    $inline_style_vars .= ' --wb-event-ink: ' . $text_color . ';';
}

$wrapper_attributes = function_exists('get_block_wrapper_attributes')
    ? get_block_wrapper_attributes(
        array(
            'class' => 'weddingblocks-event-info weddingblocks-event-info--' . $layout_variation,
            'style' => $inline_style_vars,
        )
    )
    : 'class="weddingblocks-event-info weddingblocks-event-info--' . esc_attr($layout_variation) . '" style="' . esc_attr($inline_style_vars) . '"';
?>

<div <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
        ?>>
    <div class="weddingblocks-event-info__inner">
        <?php if ('timeline' === $layout_variation) : ?>
            <div class="weddingblocks-event-columns weddingblocks-event-columns--timeline">
                <div class="weddingblocks-timeline">
                    <?php foreach ($events as $index => $event) : ?>
                        <div class="weddingblocks-timeline-item<?php echo 0 === $index ? ' weddingblocks-timeline-item--start' : ' weddingblocks-timeline-item--end'; ?>">
                            <span class="weddingblocks-timeline-marker" aria-hidden="true"></span>
                            <div class="weddingblocks-timeline-content">
                                <div class="weddingblocks-event-card">
                                    <h3><?php echo esc_html($event['title']); ?></h3>
                                    <p>
                                        <strong><?php echo esc_html($event['time']); ?></strong>
                                        <span class="weddingblocks-event-card__meta">
                                            <?php echo esc_html($event['name']); ?>
                                        </span>
                                        <span class="weddingblocks-event-card__meta">
                                            <?php echo esc_html($event['addr']); ?>
                                        </span>
                                    </p>

                                    <?php if ($show_maps_button && ! empty($maps_coords)) : ?>
                                        <div class="weddingblocks-event-actions">
                                            <a href="<?php echo esc_url($maps_coords); ?>" target="_blank" rel="noopener noreferrer" class="weddingblocks-btn-gold weddingblocks-event-map-link">
                                                <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor" aria-hidden="true">
                                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" />
                                                </svg>
                                                <?php esc_html_e('Google Maps', 'weddingblocks'); ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else : ?>
            <div class="weddingblocks-event-columns weddingblocks-event-columns--<?php echo esc_attr($layout_variation); ?>">
                <?php foreach ($events as $event) : ?>
                    <div class="weddingblocks-event-card">
                        <h3><?php echo esc_html($event['title']); ?></h3>
                        <p>
                            <strong><?php echo esc_html($event['time']); ?></strong>
                            <span class="weddingblocks-event-card__meta">
                                <?php echo esc_html($event['name']); ?>
                            </span>
                            <span class="weddingblocks-event-card__meta">
                                <?php echo esc_html($event['addr']); ?>
                            </span>
                        </p>

                        <?php if ($show_maps_button && ! empty($maps_coords)) : ?>
                            <div class="weddingblocks-event-actions">
                                <a href="<?php echo esc_url($maps_coords); ?>" target="_blank" rel="noopener noreferrer" class="weddingblocks-btn-gold weddingblocks-event-map-link">
                                    <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor" aria-hidden="true">
                                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" />
                                    </svg>
                                    <?php esc_html_e('Google Maps', 'weddingblocks'); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($show_whatsapp_button && ! empty($whatsapp)) : ?>
            <div class="weddingblocks-wa-action">
                <a href="<?php echo esc_url('https://wa.me/' . preg_replace('/[^0-9]/', '', $whatsapp)); ?>" target="_blank" rel="noopener noreferrer" class="weddingblocks-btn-gold weddingblocks-wa-button">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true">
                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.517 2.266 2.27 3.51 5.276 3.508 8.48-.005 6.66-5.342 11.997-11.953 11.997-2.005-.001-3.973-.503-5.733-1.458L0 24zm6.59-4.846c1.6.95 3.188 1.449 4.825 1.451 5.436 0 9.858-4.42 9.862-9.864.002-2.637-1.017-5.114-2.873-6.973C16.597 1.91 14.12 .89 11.48.887c-5.441 0-9.863 4.422-9.867 9.868-.002 1.77.464 3.498 1.353 5.031L1.93 21.09l5.059-1.328c1.558.85 3.238 1.293 4.908 1.292h.001zM17.65 14.28c-.319-.16-1.89-.933-2.21-1.049-.32-.116-.552-.174-.784.174-.232.348-.898 1.132-1.101 1.364-.203.232-.406.261-.726.101-1.821-.913-3.003-1.83-4.2-3.89-.319-.549.319-.51.913-1.7.093-.188.046-.352-.023-.512-.069-.16-5.52-1.332-.756-1.82-.2-.48-.403-.414-.552-.422-.143-.007-.308-.009-.472-.009-.165 0-.435.062-.663.31-.228.249-.871.851-.871 2.074 0 1.223.89 2.406.99 2.541.101.135 1.751 2.674 4.241 3.746.592.255 1.055.408 1.417.523.595.19 1.137.163 1.564.099.477-.072 1.89-.773 2.158-1.52.268-.747.268-1.388.188-1.52-.08-.135-.299-.214-.619-.374z" />
                    </svg>
                    <?php echo esc_html($whatsapp_button_label); ?>
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>