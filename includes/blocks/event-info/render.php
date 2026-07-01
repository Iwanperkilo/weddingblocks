<?php
/**
 * Server-side rendering for the Event Info block.
 *
 * @package WeddingBlocks
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$akad_time = get_post_meta( get_the_ID(), 'weddingblocks_akad_time_label', true );
$akad_loc_name = get_post_meta( get_the_ID(), 'weddingblocks_akad_location_name', true );
$akad_loc_addr = get_post_meta( get_the_ID(), 'weddingblocks_akad_location_address', true );

$resepsi_time = get_post_meta( get_the_ID(), 'weddingblocks_resepsi_time_label', true );
$resepsi_loc_name = get_post_meta( get_the_ID(), 'weddingblocks_resepsi_location_name', true );
$resepsi_loc_addr = get_post_meta( get_the_ID(), 'weddingblocks_resepsi_location_address', true );

$maps_coords = get_post_meta( get_the_ID(), 'weddingblocks_maps_coords', true );
$whatsapp = get_post_meta( get_the_ID(), 'weddingblocks_whatsapp_number', true );

// Fallbacks
if ( empty( $akad_time ) ) $akad_time = 'Pukul 08:00 - 10:00 WIB';
if ( empty( $akad_loc_name ) ) $akad_loc_name = 'Masjid Agung Kota';
if ( empty( $akad_loc_addr ) ) $akad_loc_addr = 'Jl. Cempaka No. 12';

if ( empty( $resepsi_time ) ) $resepsi_time = 'Pukul 11:00 - Selesai';
if ( empty( $resepsi_loc_name ) ) $resepsi_loc_name = 'Gedung Serbaguna Indah';
if ( empty( $resepsi_loc_addr ) ) $resepsi_loc_addr = 'Jl. Melati No. 45';

?>
<div class="weddingblocks-event-columns">
    <div class="weddingblocks-event-card">
        <h3><?php _e( 'Akad Nikah', 'weddingblocks' ); ?></h3>
        <p>
            <strong><?php echo esc_html( $akad_time ); ?></strong><br>
            <?php echo esc_html( $akad_loc_name ); ?><br>
            <?php echo esc_html( $akad_loc_addr ); ?>
        </p>
        <?php if ( ! empty( $maps_coords ) ) : ?>
            <div class="weddingblocks-event-actions" style="margin-top: 15px;">
                <a href="<?php echo esc_url( $maps_coords ); ?>" target="_blank" rel="noopener noreferrer" class="weddingblocks-btn-gold" style="padding: 8px 16px; font-size: 11px; border-radius: 20px;">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor" style="vertical-align: middle; margin-right: 4px;">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                    </svg>
                    <?php _e( 'Google Maps', 'weddingblocks' ); ?>
                </a>
            </div>
        <?php endif; ?>
    </div>
    <div class="weddingblocks-event-card">
        <h3><?php _e( 'Resepsi', 'weddingblocks' ); ?></h3>
        <p>
            <strong><?php echo esc_html( $resepsi_time ); ?></strong><br>
            <?php echo esc_html( $resepsi_loc_name ); ?><br>
            <?php echo esc_html( $resepsi_loc_addr ); ?>
        </p>
        <?php if ( ! empty( $maps_coords ) ) : ?>
            <div class="weddingblocks-event-actions" style="margin-top: 15px;">
                <a href="<?php echo esc_url( $maps_coords ); ?>" target="_blank" rel="noopener noreferrer" class="weddingblocks-btn-gold" style="padding: 8px 16px; font-size: 11px; border-radius: 20px;">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor" style="vertical-align: middle; margin-right: 4px;">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                    </svg>
                    <?php _e( 'Google Maps', 'weddingblocks' ); ?>
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php if ( ! empty( $whatsapp ) ) :
    // Format whatsapp link
    $wa_clean = preg_replace( '/[^0-9]/', '', $whatsapp );
    if ( strpos( $wa_clean, '0' ) === 0 ) {
        $wa_clean = '62' . substr( $wa_clean, 1 );
    }
    $wa_url = 'https://wa.me/' . $wa_clean;
    ?>
    <div class="weddingblocks-wa-action" style="margin: 25px auto 0; text-align: center;">
        <a href="<?php echo esc_url( $wa_url ); ?>" target="_blank" rel="noopener noreferrer" class="weddingblocks-btn-gold" style="background-color: #25D366; border-color: #25D366; color: #ffffff !important; box-shadow: 0 4px 15px rgba(37, 211, 102, 0.2);">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" style="vertical-align: middle; margin-right: 6px;">
                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.517 2.266 2.27 3.51 5.276 3.508 8.48-.005 6.66-5.342 11.997-11.953 11.997-2.005-.001-3.973-.503-5.733-1.458L0 24zm6.59-4.846c1.6.95 3.188 1.449 4.825 1.451 5.436 0 9.858-4.42 9.862-9.864.002-2.637-1.017-5.114-2.873-6.973C16.597 1.91 14.12 .89 11.48.887c-5.441 0-9.863 4.422-9.867 9.868-.002 1.77.464 3.498 1.353 5.031L1.93 21.09l5.059-1.328c1.558.85 3.238 1.293 4.908 1.292h.001zM17.65 14.28c-.319-.16-1.89-.933-2.21-1.049-.32-.116-.552-.174-.784.174-.232.348-.898 1.132-1.101 1.364-.203.232-.406.261-.726.101-1.821-.913-3.003-1.83-4.2-3.89-.319-.549.319-.51.913-1.7.093-.188.046-.352-.023-.512-.069-.16-.552-1.332-.756-1.82-.2-.48-.403-.414-.552-.422-.143-.007-.308-.009-.472-.009-.165 0-.435.062-.663.31-.228.249-.871.851-.871 2.074 0 1.223.89 2.406.99 2.541.101.135 1.751 2.674 4.241 3.746.592.255 1.055.408 1.417.523.595.19 1.137.163 1.564.099.477-.072 1.89-.773 2.158-1.52.268-.747.268-1.388.188-1.52-.08-.135-.299-.214-.619-.374z"/>
            </svg>
            <?php _e( 'Hubungi Admin (WhatsApp)', 'weddingblocks' ); ?>
        </a>
    </div>
<?php endif; ?>
