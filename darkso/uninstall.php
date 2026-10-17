<?php
/**
 * پاکسازی داده‌های Darkso هنگام حذف افزونه.
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

delete_option('darkso_color_maps');
delete_option('darkso_messages');
delete_option('darkso_form_configs');

$entries = get_posts([
    'post_type' => 'darkso_entry',
    'post_status' => 'any',
    'posts_per_page' => -1,
    'fields' => 'ids',
    'no_found_rows' => true,
]);

foreach ($entries as $entry_id) {
    wp_delete_post((int) $entry_id, true);
}
