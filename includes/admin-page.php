<?php

/**
 * Admin Dashboard RSVP List for WeddingBlocks.
 */

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Add RSVP List sub-menu.
 */
function weddingblocks_add_admin_menu()
{
    add_submenu_page(
        'edit.php?post_type=wdbl_undangan',
        __('Daftar RSVP', 'weddingblocks'),
        __('Daftar RSVP', 'weddingblocks'),
        'manage_options',
        'weddingblocks-rsvp',
        'weddingblocks_render_admin_rsvp_page'
    );

    /**
     * Extension point: an add-on plugin (e.g. WeddingBlocks Pro) can add
     * its own submenu pages here — Import Tamu, Broadcast WhatsApp, QR
     * Check-in, etc. — right after the core RSVP list. Free doesn't know
     * or care what (if anything) gets added.
     */
    do_action('weddingblocks_admin_menu_items');
}
add_action('admin_menu', 'weddingblocks_add_admin_menu');

/**
 * Render RSVP Page.
 */
function weddingblocks_render_admin_rsvp_page()
{
    // 1. Handle deletion.
    if (isset($_GET['action']) && $_GET['action'] === 'delete') {
        $rsvp_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        if (! current_user_can('manage_options')) {
            wp_die(esc_html__('Anda tidak memiliki izin untuk melakukan ini.', 'weddingblocks'));
        }

        check_admin_referer('delete_rsvp_' . $rsvp_id);

        if (weddingblocks_delete_rsvp($rsvp_id)) {
            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Data RSVP berhasil dihapus.', 'weddingblocks') . '</p></div>';
        } else {
            echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__('Gagal menghapus data RSVP.', 'weddingblocks') . '</p></div>';
        }
    }

    // 2. Setup pagination & filtering variables.
    $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $per_page     = 20;
    $offset       = ($current_page - 1) * $per_page;
    $post_filter  = isset($_GET['post_filter']) ? intval($_GET['post_filter']) : 0;

    // Fetch invitations for filter dropdown.
    $invitations = get_posts(array(
        'post_type'      => 'wdbl_undangan',
        'posts_per_page' => -1,
        'post_status'    => 'any',
    ));

    // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only status filter parameter.
    $status_filter = '';
    if (isset($_GET['status'])) {
        $status_filter = sanitize_text_field(wp_unslash($_GET['status']));
    } elseif (isset($_GET['wbpro_status'])) {
        $status_filter = sanitize_text_field(wp_unslash($_GET['wbpro_status']));
    }
    $allowed_status = (array) apply_filters('weddingblocks_allowed_rsvp_statuses', array('pending', 'approved', 'hidden'));
    if (! in_array($status_filter, $allowed_status, true)) {
        $status_filter = '';
    }

    // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only search parameter.
    $search = '';
    if (isset($_GET['s'])) {
        $search = sanitize_text_field(wp_unslash($_GET['s']));
    } elseif (isset($_GET['wbpro_search'])) {
        $search = sanitize_text_field(wp_unslash($_GET['wbpro_search']));
    }

    // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only attendance filter parameter.
    $attendance_filter = '';
    if (isset($_GET['attendance'])) {
        $attendance_filter = sanitize_text_field(wp_unslash($_GET['attendance']));
    } elseif (isset($_GET['wbpro_attendance'])) {
        $attendance_filter = sanitize_text_field(wp_unslash($_GET['wbpro_attendance']));
    }
    $allowed_attendance = array('hadir', 'tidak_hadir', 'ragu_ragu');
    if (! in_array($attendance_filter, $allowed_attendance, true)) {
        $attendance_filter = '';
    }

    // Fetch RSVP records.
    $rsvps       = weddingblocks_get_rsvps($post_filter, $per_page, $offset, $status_filter, $search, $attendance_filter);
    $total_items = weddingblocks_get_rsvps_count($post_filter, $status_filter, $search, $attendance_filter);
    $total_pages = ceil($total_items / $per_page);

?>
    <div class="wrap">
        <h1 class="wp-heading-inline"><?php esc_html_e('Daftar RSVP Tamu', 'weddingblocks'); ?></h1>
        <hr class="wp-header-end">

        <!-- Filter Form -->
        <form method="get" action="">
            <input type="hidden" name="post_type" value="wdbl_undangan" />
            <input type="hidden" name="page" value="weddingblocks-rsvp" />

            <div class="tablenav top">
                <div class="alignleft actions">
                    <select name="post_filter">
                        <option value="0"><?php esc_html_e('Semua Undangan', 'weddingblocks'); ?></option>
                        <?php foreach ($invitations as $invitation) : ?>
                            <option value="<?php echo esc_attr($invitation->ID); ?>" <?php selected($post_filter, $invitation->ID); ?>>
                                <?php echo esc_html($invitation->post_title); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="submit" class="button action" value="<?php esc_attr_e('Filter', 'weddingblocks'); ?>">
                </div>

                <?php if ($total_pages > 1) : ?>
                    <div class="tablenav-pages">
                        <span class="displaying-num"><?php
                                                        /* translators: %s: number of items */
                                                        printf(esc_html(_n('%s item', '%s items', $total_items, 'weddingblocks')), esc_html(number_format_i18n($total_items)));
                                                        ?></span>
                        <span class="pagination-links">
                            <?php
                            echo wp_kses_post(paginate_links(array(
                                'base'      => add_query_arg('paged', '%#%'),
                                'format'    => '',
                                'prev_text' => '&laquo;',
                                'next_text' => '&raquo;',
                                'total'     => $total_pages,
                                'current'   => $current_page,
                            )));
                            ?>
                        </span>
                    </div>
                <?php endif; ?>
                <br class="clear">
            </div>
        </form>

        <?php
        /**
         * Extension point: Pro (or other add-ons) can inject extra filter
         * controls — e.g. a status dropdown for moderation — right above
         * the RSVP table without touching Free's core filter form.
         */
        do_action('weddingblocks_rsvp_before_table');
        ?>

        <!-- RSVP Table -->
        <table class="wp-list-table widefat fixed striped table-view-list rsvps">
            <thead>
                <tr>
                    <?php do_action('weddingblocks_rsvp_head_begin'); ?>
                    <th scope="col" class="manage-column column-name" style="width: 15%;"><?php esc_html_e('Nama Tamu', 'weddingblocks'); ?></th>
                    <th scope="col" class="manage-column column-invitation" style="width: 20%;"><?php esc_html_e('Undangan', 'weddingblocks'); ?></th>
                    <th scope="col" class="manage-column column-attendance" style="width: 12%;"><?php esc_html_e('Kehadiran', 'weddingblocks'); ?></th>
                    <th scope="col" class="manage-column column-guests" style="width: 10%;"><?php esc_html_e('Pax', 'weddingblocks'); ?></th>
                    <th scope="col" class="manage-column column-message" style="width: 28%;"><?php esc_html_e('Ucapan & Doa Restu', 'weddingblocks'); ?></th>
                    <th scope="col" class="manage-column column-date" style="width: 15%;"><?php esc_html_e('Tanggal Kirim', 'weddingblocks'); ?></th>
                    <?php
                    /**
                     * Extension point: Pro can add extra <th> columns here
                     * (e.g. "Check-in", "Sumber Undangan").
                     */
                    do_action('weddingblocks_rsvp_table_head');
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php if (! empty($rsvps)) : ?>
                    <?php foreach ($rsvps as $rsvp) :
                        $invitation_title = get_the_title($rsvp->post_id);
                        $invitation_link  = get_edit_post_link($rsvp->post_id);

                        // Attendance badge styling.
                        $attendance_style = '';
                        $attendance_label = '';
                        switch ($rsvp->attendance) {
                            case 'hadir':
                                $attendance_style = 'background: #edfaef; color: #008a20; border: 1px solid #c2ecc6;';
                                $attendance_label = __('Hadir', 'weddingblocks');
                                break;
                            case 'tidak_hadir':
                                $attendance_style = 'background: #fcf0f1; color: #d63638; border: 1px solid #f8d7da;';
                                $attendance_label = __('Tidak Hadir', 'weddingblocks');
                                break;
                            case 'ragu_ragu':
                                $attendance_style = 'background: #fcf9e8; color: #996800; border: 1px solid #faeab9;';
                                $attendance_label = __('Ragu-ragu', 'weddingblocks');
                                break;
                        }
                    ?>
                        <tr>
                            <?php do_action('weddingblocks_rsvp_row_begin', $rsvp); ?>
                            <td class="column-name">
                                <strong><?php echo esc_html($rsvp->guest_name); ?></strong>
                                <?php if (apply_filters('weddingblocks_show_rsvp_row_actions', true, $rsvp)) : ?>
                                    <div class="row-actions">
                                        <span class="delete">
                                            <a href="<?php echo esc_url(wp_nonce_url(add_query_arg(array('action' => 'delete', 'id' => $rsvp->id)), 'delete_rsvp_' . $rsvp->id)); ?>" class="submitdelete" onclick="return confirm('<?php echo esc_js(__('Apakah Anda yakin ingin menghapus data RSVP ini?', 'weddingblocks')); ?>');">
                                                <?php esc_html_e('Hapus', 'weddingblocks'); ?>
                                            </a>
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="column-invitation">
                                <?php if ($invitation_title) : ?>
                                    <a href="<?php echo esc_url($invitation_link); ?>">
                                        <?php echo esc_html($invitation_title); ?>
                                    </a>
                                <?php else : ?>
                                    <span class="description"><?php esc_html_e('(Tidak ada/Dihapus)', 'weddingblocks'); ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="column-attendance">
                                <?php if ($attendance_label) : ?>
                                    <span class="wb-rsvp-attendance" style="display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 12px; font-weight: 600; <?php echo esc_attr($attendance_style); ?>">
                                        <?php echo esc_html($attendance_label); ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="column-guests">
                                <?php echo intval($rsvp->guests_count); ?>
                            </td>
                            <td class="column-message">
                                <?php echo nl2br(esc_html($rsvp->message)); ?>
                            </td>
                            <td class="column-date">
                                <?php echo esc_html(date_i18n(get_option('date_format') . ' H:i', strtotime($rsvp->created_at))); ?>
                            </td>
                            <?php
                            /**
                             * Extension point: Pro adds the matching <td>
                             * cells here for any <th> it added above via
                             * 'weddingblocks_rsvp_table_head'. Must stay in
                             * sync with that hook, column-for-column.
                             *
                             * @param object $rsvp The current RSVP row.
                             */
                            do_action('weddingblocks_rsvp_table_row', $rsvp);
                            ?>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <?php
                    // Base columns: Nama, Undangan, Kehadiran, Pax, Ucapan, Tanggal.
                    // Add-ons that inject extra <th>/<td> pairs above (via
                    // weddingblocks_rsvp_head_begin / _table_head) should report
                    // how many columns they added here, so the empty-state row
                    // always spans the table's actual current width instead of
                    // a number that goes stale the next time a column is added.
                    $wbdl_rsvp_extra_columns = (int) apply_filters('weddingblocks_rsvp_extra_columns', 0);
                    ?>
                    <tr>
                        <td colspan="<?php echo esc_attr(6 + $wbdl_rsvp_extra_columns); ?>" class="colspanchange">
                            <?php esc_html_e('Tidak ada data RSVP ditemukan.', 'weddingblocks'); ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Bottom Pagination -->
        <?php if ($total_pages > 1) : ?>
            <div class="tablenav bottom">
                <div class="tablenav-pages">
                    <span class="pagination-links">
                        <?php
                        echo wp_kses_post(paginate_links(array(
                            'base'      => add_query_arg('paged', '%#%'),
                            'format'    => '',
                            'prev_text' => '&laquo;',
                            'next_text' => '&raquo;',
                            'total'     => $total_pages,
                            'current'   => $current_page,
                        )));
                        ?>
                    </span>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php
}
