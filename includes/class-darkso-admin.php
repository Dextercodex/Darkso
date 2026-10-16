<?php

if (!defined('ABSPATH')) {
    exit;
}

class Darkso_Admin
{
    public static function init()
    {
        add_action('admin_menu', [self::class, 'menu']);
        add_action('admin_init', [self::class, 'register']);
        add_action('admin_enqueue_scripts', [self::class, 'assets']);
    }

    public static function default_maps()
    {
        return [
            ['from' => '#ffffff', 'to' => '#121212', 'note' => 'پس‌زمینه اصلی'],
            ['from' => '#f5f5f5', 'to' => '#1e1e1e', 'note' => 'پس‌زمینه روشن‌تر / سکشن'],
            ['from' => '#000000', 'to' => '#f2f2f2', 'note' => 'متن اصلی'],
            ['from' => '#111111', 'to' => '#eeeeee', 'note' => 'متن تیتر'],
        ];
    }

    public static function get_maps()
    {
        $maps = get_option(DARKSO_OPTION, self::default_maps());
        if (!is_array($maps)) {
            return [];
        }

        $clean = [];
        foreach ($maps as $row) {
            $from = self::normalize_hex($row['from'] ?? '');
            $to = self::normalize_hex($row['to'] ?? '');
            if ($from && $to) {
                $clean[] = [
                    'from' => $from,
                    'to' => $to,
                    'note' => sanitize_text_field($row['note'] ?? ''),
                ];
            }
        }

        return $clean;
    }

    public static function normalize_hex($value)
    {
        $value = trim((string) $value);
        if (!preg_match('/^#([0-9a-f]{3}|[0-9a-f]{6})$/i', $value)) {
            return '';
        }

        if (strlen($value) === 4) {
            $value = sprintf('#%s%s%s%s%s%s', $value[1], $value[1], $value[2], $value[2], $value[3], $value[3]);
        }

        return strtolower($value);
    }

    public static function menu()
    {
        add_options_page(
            'Darkso',
            'Darkso',
            'manage_options',
            'darkso',
            [self::class, 'render']
        );
    }

    public static function register()
    {
        register_setting('darkso', DARKSO_OPTION, [
            'type' => 'array',
            'sanitize_callback' => [self::class, 'sanitize'],
            'default' => self::default_maps(),
        ]);

        register_setting('darkso', DARKSO_MESSAGES, [
            'type' => 'array',
            'sanitize_callback' => [self::class, 'sanitize_messages'],
            'default' => self::default_messages(),
        ]);
    }

    public static function default_messages()
    {
        return [
            'light_already' => 'شما در حالت روشن هستید',
            'dark_already' => 'شما در حالت تاریک هستید',
        ];
    }

    public static function get_messages()
    {
        $saved = get_option(DARKSO_MESSAGES, self::default_messages());
        if (!is_array($saved)) {
            $saved = [];
        }
        return array_merge(self::default_messages(), $saved);
    }

    public static function sanitize_messages($input)
    {
        $defaults = self::default_messages();
        if (!is_array($input)) {
            return $defaults;
        }

        return [
            'light_already' => sanitize_text_field($input['light_already'] ?? $defaults['light_already']),
            'dark_already' => sanitize_text_field($input['dark_already'] ?? $defaults['dark_already']),
        ];
    }

    public static function sanitize($input)
    {
        if (!is_array($input)) {
            return [];
        }

        $out = [];
        foreach ($input as $row) {
            $from = self::normalize_hex($row['from'] ?? '');
            $to = self::normalize_hex($row['to'] ?? '');
            if ($from && $to) {
                $out[] = [
                    'from' => $from,
                    'to' => $to,
                    'note' => sanitize_text_field($row['note'] ?? ''),
                ];
            }
        }

        return $out;
    }

    public static function assets($hook)
    {
        if ($hook !== 'settings_page_darkso') {
            return;
        }

        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_style('darkso-admin', DARKSO_URL . 'assets/admin.css', [], DARKSO_VERSION);
        wp_enqueue_script('darkso-admin', DARKSO_URL . 'assets/admin.js', ['jquery', 'wp-color-picker'], DARKSO_VERSION, true);
    }

    public static function render()
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        $maps = self::get_maps();
        if (!$maps) {
            $maps = [['from' => '#ffffff', 'to' => '#121212', 'note' => '']];
        }
        ?>
        <div class="wrap darkso-wrap">
            <h1>Darkso</h1>
            <p>برای هر رنگ یک توضیح بنویس تا قاطی نشود (مثلاً پس‌زمینه هدر، رنگ دکمه، متن فوتر). ویجت‌های المنتور در دسته <strong>Darkso</strong> و همچنین ویجت‌های پایه هستند.</p>
            <form method="post" action="options.php">
                <?php settings_fields('darkso'); ?>
                <table class="widefat striped darkso-table" id="darkso-table">
                    <thead>
                        <tr>
                            <th>توضیح</th>
                            <th>از رنگ (حالت روشن)</th>
                            <th>به رنگ (دارک‌مود)</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($maps as $i => $row) : ?>
                            <tr>
                                <td>
                                    <input type="text" class="darkso-note regular-text" name="<?php echo esc_attr(DARKSO_OPTION); ?>[<?php echo (int) $i; ?>][note]" value="<?php echo esc_attr($row['note'] ?? ''); ?>" placeholder="مثلاً پس‌زمینه هدر">
                                </td>
                                <td>
                                    <input type="text" class="darkso-color" name="<?php echo esc_attr(DARKSO_OPTION); ?>[<?php echo (int) $i; ?>][from]" value="<?php echo esc_attr($row['from']); ?>">
                                </td>
                                <td>
                                    <input type="text" class="darkso-color" name="<?php echo esc_attr(DARKSO_OPTION); ?>[<?php echo (int) $i; ?>][to]" value="<?php echo esc_attr($row['to']); ?>">
                                </td>
                                <td>
                                    <button type="button" class="button darkso-remove">حذف</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <p>
                    <button type="button" class="button" id="darkso-add">افزودن رنگ</button>
                </p>
                <h2>پیام دکمه‌های یک‌طرفه</h2>
                <p>کلاس <code>Lightmodebtn</code> فقط سایت را روشن می‌کند. کلاس <code>Darkmodebtn</code> فقط دارک می‌کند. اگر همان حالت فعال باشد، این پیام‌ها نشان داده می‌شوند. روی خود دکمه هم می‌توانی <code>data-darkso-msg</code> بگذاری تا همان دکمه پیام جدا داشته باشد.</p>
                <?php $msgs = self::get_messages(); ?>
                <table class="form-table">
                    <tr>
                        <th><label for="darkso-light-msg">پیام دکمه روشن (وقتی از قبل روشن است)</label></th>
                        <td>
                            <input type="text" class="regular-text" id="darkso-light-msg" name="<?php echo esc_attr(DARKSO_MESSAGES); ?>[light_already]" value="<?php echo esc_attr($msgs['light_already']); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th><label for="darkso-dark-msg">پیام دکمه دارک (وقتی از قبل دارک است)</label></th>
                        <td>
                            <input type="text" class="regular-text" id="darkso-dark-msg" name="<?php echo esc_attr(DARKSO_MESSAGES); ?>[dark_already]" value="<?php echo esc_attr($msgs['dark_already']); ?>">
                        </td>
                    </tr>
                </table>
                <?php submit_button('ذخیره تنظیمات'); ?>
            </form>
        </div>
        <?php
    }
}
