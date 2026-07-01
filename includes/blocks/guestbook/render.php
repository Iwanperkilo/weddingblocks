<?php
/**
 * Server-side rendering for the Guestbook block.
 *
 * @package WeddingBlocks
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$current_post_id = get_the_ID();
$rsvps = weddingblocks_get_rsvps( $current_post_id, 50, 0 );

?>
<div class="weddingblocks-guestbook-container">
    <div class="guestbook-list" id="weddingblocks-guestbook-list">
        <?php if ( ! empty( $rsvps ) ) : ?>
            <?php foreach ( $rsvps as $rsvp ) :
                $formatted_time = human_time_diff( strtotime( $rsvp->created_at ), current_time( 'timestamp' ) ) . ' ' . __( 'yang lalu', 'weddingblocks' );

                $badge_class = '';
                $badge_label = '';
                switch ( $rsvp->attendance ) {
                    case 'hadir':
                        $badge_class = 'badge-hadir';
                        $badge_label = __( 'Hadir', 'weddingblocks' );
                        break;
                    case 'tidak_hadir':
                        $badge_class = 'badge-tidak-hadir';
                        $badge_label = __( 'Tidak Hadir', 'weddingblocks' );
                        break;
                    case 'ragu_ragu':
                        $badge_class = 'badge-ragu';
                        $badge_label = __( 'Ragu-ragu', 'weddingblocks' );
                        break;
                }
                ?>
                <div class="guestbook-item">
                    <div class="guestbook-header">
                        <h5 class="guest-name"><?php echo esc_html( $rsvp->guest_name ); ?></h5>
                        <span class="guest-status <?php echo esc_attr( $badge_class ); ?>"><?php echo esc_html( $badge_label ); ?></span>
                    </div>
                    <p class="guest-message"><?php echo nl2br( esc_html( $rsvp->message ) ); ?></p>
                    <span class="guest-time"><?php echo esc_html( $formatted_time ); ?></span>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="guestbook-empty" id="guestbook-empty-placeholder">
                <p><?php _e( 'Belum ada ucapan. Jadilah yang pertama memberikan doa restu!', 'weddingblocks' ); ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>
