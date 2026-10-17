<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * مدیریت فرم‌ها: ذخیره تنظیمات، ثبت پیام‌ها و ارسال ایمیل (AJAX).
 */
class Darkso_Forms
{
    const CPT = 'darkso_entry';
    const OPT = 'darkso_form_configs';

    public static function init()
    {
        add_action('init', [self::class, 'register_cpt']);
        add_action('add_meta_boxes', [self::class, 'meta_boxes']);
        add_action('wp_ajax_darkso_form_nonce', [self::class, 'ajax_nonce']);
        add_action('wp_ajax_nopriv_darkso_form_nonce', [self::class, 'ajax_nonce']);
        add_action('wp_ajax_darkso_form_submit', [self::class, 'ajax_submit']);
        add_action('wp_ajax_nopriv_darkso_form_submit', [self::class, 'ajax_submit']);
    }

    public static function register_cpt()
    {
        register_post_type(self::CPT, [
            'labels' => [
                'name' => 'پیام‌های دارکسو',
                'singular_name' => 'پیام',
                'menu_name' => 'پیام‌های فرم',
                'all_items' => 'همه پیام‌ها',
                'edit_item' => 'مشاهده پیام',
                'search_items' => 'جستجوی پیام‌ها',
                'not_found' => 'هنوز پیامی دریافت نشده است.',
                'not_found_in_trash' => 'پیامی در زباله‌دان نیست.',
            ],
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_rest' => false,
            'menu_position' => 58,
            'menu_icon' => 'dashicons-email-alt',
            'supports' => ['title'],
            'capability_type' => 'post',
            'capabilities' => ['create_posts' => 'do_not_allow'],
            'map_meta_cap' => true,
        ]);
    }

    public static function meta_boxes()
    {
        add_meta_box(
            'darkso_entry_data',
            'اطلاعات ارسال‌شده',
            [self::class, 'render_entry_box'],
            self::CPT,
            'normal',
            'high'
        );
    }

    public static function render_entry_box($post)
    {
        $rows = get_post_meta($post->ID, '_darkso_fields', true);
        $form = get_post_meta($post->ID, '_darkso_form', true);

        if (!is_array($rows) || !$rows) {
            echo '<p>داده‌ای ثبت نشده است.</p>';
            return;
        }

        if ($form) {
            echo '<p><strong>فرم:</strong> ' . esc_html($form) . '</p>';
        }

        echo '<table class="widefat striped" style="direction:rtl"><tbody>';
        foreach ($rows as $row) {
            echo '<tr><th style="width:200px">' . esc_html($row['label'] ?? '') . '</th>';
            echo '<td>' . nl2br(esc_html($row['value'] ?? '')) . '</td></tr>';
        }
        echo '</tbody></table>';
    }

    /**
     * ثبت تنظیمات یک فرم و برگرداندن کلید آن.
     */
    public static function register_config(array $cfg)
    {
        $key = substr(md5(wp_json_encode($cfg)), 0, 20);
        $all = get_option(self::OPT, []);
        if (!is_array($all)) {
            $all = [];
        }

        if (!isset($all[$key])) {
            $all[$key] = $cfg;
            if (count($all) > 300) {
                $all = array_slice($all, -300, null, true);
            }
            update_option(self::OPT, $all, false);
        }

        return $key;
    }

    public static function ajax_nonce()
    {
        nocache_headers();
        wp_send_json_success(['nonce' => wp_create_nonce('darkso_form')]);
    }

    private static function fail($message, $code = 400)
    {
        wp_send_json_error(['message' => $message], $code);
    }

    public static function ajax_submit()
    {
        nocache_headers();

        if (!check_ajax_referer('darkso_form', 'nonce', false)) {
            self::fail('نشست شما منقضی شده است. صفحه را دوباره بارگذاری کنید.', 403);
        }

        // ربات‌ها معمولاً فیلد مخفی را پر می‌کنند.
        if (!empty($_POST['darkso_website'])) {
            wp_send_json_success(['message' => 'ok']);
        }

        // محدودیت تعداد ارسال: ۵ بار در دقیقه برای هر IP.
        $ip = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '';
        $rl_key = 'darkso_rl_' . md5($ip);
        $hits = (int) get_transient($rl_key);
        if ($hits >= 5) {
            self::fail('تعداد ارسال‌ها زیاد است. لطفاً کمی بعد دوباره تلاش کنید.', 429);
        }
        set_transient($rl_key, $hits + 1, MINUTE_IN_SECONDS);

        $key = isset($_POST['darkso_key']) ? sanitize_key(wp_unslash($_POST['darkso_key'])) : '';
        $all = get_option(self::OPT, []);
        if (!$key || !is_array($all) || !isset($all[$key]) || !is_array($all[$key])) {
            self::fail('فرم نامعتبر است. صفحه را دوباره بارگذاری کنید.');
        }

        $cfg = $all[$key];
        $fields = isset($cfg['fields']) && is_array($cfg['fields']) ? $cfg['fields'] : [];
        $default_error = !empty($cfg['error']) ? $cfg['error'] : 'ارسال انجام نشد. لطفاً دوباره تلاش کنید.';

        $rows = [];
        $reply_to = '';

        foreach ($fields as $name => $f) {
            $type = $f['type'] ?? 'text';
            $label = $f['label'] ?? $name;
            // phpcs:ignore WordPress.Security.NonceVerification.Missing
            $raw = isset($_POST[$name]) ? wp_unslash($_POST[$name]) : '';
            if (is_array($raw)) {
                $raw = '';
            }
            $raw = (string) $raw;
            if (function_exists('mb_substr')) {
                $raw = mb_substr($raw, 0, 5000);
            }

            switch ($type) {
                case 'textarea':
                    $v = sanitize_textarea_field($raw);
                    break;
                case 'email':
                    $v = sanitize_email($raw);
                    if ($v !== '' && !is_email($v)) {
                        self::fail('ایمیل واردشده در «' . $label . '» معتبر نیست.', 422);
                    }
                    if ($v !== '' && $reply_to === '') {
                        $reply_to = $v;
                    }
                    break;
                case 'tel':
                    $v = sanitize_text_field($raw);
                    if ($v !== '' && !preg_match('/^[0-9+\-\s()\x{06F0}-\x{06F9}\x{0660}-\x{0669}]{5,25}$/u', $v)) {
                        self::fail('شماره واردشده در «' . $label . '» معتبر نیست.', 422);
                    }
                    break;
                case 'number':
                    $v = sanitize_text_field($raw);
                    if ($v !== '' && !is_numeric($v)) {
                        self::fail('مقدار «' . $label . '» باید عدد باشد.', 422);
                    }
                    break;
                case 'select':
                    $v = sanitize_text_field($raw);
                    $opts = isset($f['options']) && is_array($f['options']) ? $f['options'] : [];
                    if ($v !== '' && !in_array($v, $opts, true)) {
                        self::fail('گزینه انتخاب‌شده در «' . $label . '» معتبر نیست.', 422);
                    }
                    break;
                case 'checkbox':
                    $v = $raw !== '' ? 'بله' : '';
                    break;
                default:
                    $v = sanitize_text_field($raw);
                    break;
            }

            if (!empty($f['required']) && $v === '') {
                self::fail('لطفاً «' . $label . '» را تکمیل کنید.', 422);
            }

            $rows[] = ['label' => $label, 'value' => $v];
        }

        $site = wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES);
        $form_title = !empty($cfg['title']) ? $cfg['title'] : 'فرم تماس';
        $subject = !empty($cfg['subject']) ? $cfg['subject'] : ('پیام جدید از ' . $site);
        $saved = false;
        $sent = false;

        if (!empty($cfg['save'])) {
            $entry_id = wp_insert_post([
                'post_type' => self::CPT,
                'post_status' => 'publish',
                'post_title' => $form_title . ' — ' . current_time('Y/m/d H:i'),
            ], true);

            if (!is_wp_error($entry_id) && $entry_id) {
                update_post_meta($entry_id, '_darkso_fields', $rows);
                update_post_meta($entry_id, '_darkso_form', $form_title);
                $saved = true;
            }
        }

        if (!empty($cfg['send'])) {
            $to = !empty($cfg['to']) && is_email($cfg['to']) ? $cfg['to'] : get_option('admin_email');
            $lines = [];
            foreach ($rows as $row) {
                $lines[] = $row['label'] . ': ' . $row['value'];
            }
            $body = implode("\n", $lines) . "\n\n—\n" . $site . ' (' . home_url('/') . ')';

            $headers = ['Content-Type: text/plain; charset=UTF-8'];
            if ($reply_to !== '') {
                $headers[] = 'Reply-To: ' . $reply_to;
            }

            $sent = (bool) wp_mail($to, $subject, $body, $headers);
        }

        if (!$saved && !$sent && (!empty($cfg['save']) || !empty($cfg['send']))) {
            self::fail($default_error, 500);
        }

        $success = !empty($cfg['success']) ? $cfg['success'] : 'پیام شما با موفقیت ارسال شد.';
        wp_send_json_success(['message' => $success]);
    }
}
