<?php
/**
 * Server-side rendering for the Guestbook block.
 *
 * @package WeddingBlocks
 */

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$entries_to_show    = isset( $attributes['entriesToShow'] ) ? absint( $attributes['entriesToShow'] ) : 50;
$order              = isset( $attributes['order'] ) ? $attributes['order'] : 'newest';
$show_timestamp     = isset( $attributes['showTimestamp'] ) ? (bool) $attributes['showTimestamp'] : true;
$show_status_badge  = isset( $attributes['showStatusBadge'] ) ? (bool) $attributes['showStatusBadge'] : true;
$empty_state_text   = isset( $attributes['emptyStateText'] ) && '' !== $attributes['emptyStateText']
    ? $attributes['emptyStateText']
    : __( 'Belum ada ucapan. Jadilah yang pertama memberikan doa restu!', 'weddingblocks' );
$message_max_length = isset( $attributes['messageMaxLength'] ) ? absint( $attributes['messageMaxLength'] ) : 0;
$card_background     = isset( $attributes['cardBackgroundColor'] ) ? $attributes['cardBackgroundColor'] : '';
$card_border_color   = isset( $attributes['cardBorderColor'] ) ? $attributes['cardBorderColor'] : '';

$card_style_parts = array();
if ( '' !== $card_background ) {
    $card_style_parts[] = 'background-color:' . $card_background;
}
if ( '' !== $card_border_color ) {
    $card_style_parts[] = 'border-color:' . $card_border_color;
}
$card_style = ! empty( $card_style_parts ) ? implode( ';', $card_style_parts ) . ';' : '';

$current_post_id = get_the_ID();
$rsvps            = weddingblocks_get_rsvps( $current_post_id, $entries_to_show, 0 );

if ( 'oldest' === $order && ! empty( $rsvps ) ) {
    $rsvps = array_reverse( $rsvps );
}

$wrapper_attributes = get_block_wrapper_attributes(
    array(
        'class' => 'weddingblocks-guestbook-container',
    )
);

?>
<div <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
    <div class="guestbook-list" id="weddingblocks-guestbook-list">
        <?php if ( ! empty( $rsvps ) ) : ?>
            <?php foreach ( $rsvps as $rsvp ) :
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

                $message = $rsvp->message;
                if ( $message_max_length > 0 && mb_strlen( $message ) > $message_max_length ) {
                    $message = mb_substr( $message, 0, $message_max_length ) . '…';
                }
                ?>
                <div class="guestbook-item"<?php echo $card_style ? ' style="' . esc_attr( $card_style ) . '"' : ''; ?>>
                    <div class="guestbook-header">
                        <h5 class="guest-name"><?php echo esc_html( $rsvp->guest_name ); ?></h5>
                        <?php if ( $show_status_badge && $badge_label ) : ?>
                            <span class="guest-status <?php echo esc_attr( $badge_class ); ?>"><?php echo esc_html( $badge_label ); ?></span>
                        <?php endif; ?>
                    </div>
                    <p class="guest-message"><?php echo nl2br( esc_html( $message ) ); ?></p>
                    <?php if ( $show_timestamp ) : ?>
                        <?php $formatted_time = human_time_diff( strtotime( $rsvp->created_at ), current_time( 'timestamp' ) ) . ' ' . __( 'yang lalu', 'weddingblocks' ); ?>
                        <span class="guest-time"><?php echo esc_html( $formatted_time ); ?></span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="guestbook-empty" id="guestbook-empty-placeholder">
                <p><?php echo esc_html( $empty_state_text ); ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>