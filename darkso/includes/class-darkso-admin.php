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
        add_filter('plugin_action_links_' . plugin_basename(DARKSO_FILE), [self::class, 'action_links']);
    }

    public static function action_links($links)
    {
        array_unshift($links, '<a href="' . esc_url(admin_url('options-general.php?page=darkso')) . '">تنظیمات</a>');
        return $links;
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

    private static function copy_code($text)
    {
        return '<span class="darkso-code"><code>' . esc_html($text) . '</code><button type="button" class="darkso-copy" data-copy="' . esc_attr($text) . '" aria-label="کپی">کپی</button></span>';
    }

    private static function widgets_list()
    {
        return [
            ['سوییچ دارک‌مود', 'دکمه آیکنی با ۴ انیمیشن تبدیل (چرخش، محو، سه‌بعدی، اسکیل).', 'dashicons-admin-appearance'],
            ['تصویر لایت / دارک', 'برای هر حالت یک تصویر جدا؛ تعویض نرم هنگام تغییر حالت.', 'dashicons-format-image'],
            ['متن لایت / دارک', 'دو ویرایشگر متن؛ هر حالت متن و رنگ مخصوص خود را دارد.', 'dashicons-editor-paragraph'],
            ['عنوان لایت / دارک', 'عنوان جدا برای هر حالت با تگ و لینک دلخواه.', 'dashicons-heading'],
            ['آیکن لایت / دارک', 'دو آیکن مجزا با رنگ، اندازه و چرخش.', 'dashicons-star-filled'],
            ['منوی وردپرس لایت / دارک', 'دو منوی جداگانه برای حالت روشن و تاریک با زیرمنو.', 'dashicons-menu'],
            ['باکس آیکن', 'آیکن + عنوان + توضیح + دکمه؛ آیکن جدا برای دارک‌مود.', 'dashicons-info-outline'],
            ['نوشته‌ها (Post)', '۴ چیدمان: کلاسیک، روی تصویر، لیستی، مینیمال + صفحه‌بندی.', 'dashicons-admin-post'],
            ['Loop Grid', 'گرید نوشته‌ها با قالب المنتور دلخواه برای هر آیتم و صفحه‌بندی.', 'dashicons-grid-view'],
            ['Loop Carousel', 'کروسل نوشته‌ها با فلش، نقطه، پخش خودکار و پشتیبانی RTL.', 'dashicons-images-alt2'],
            ['پورتفولیو', 'گرید/ماسونری با فیلتر دسته، افکت هاور و لایت‌باکس.', 'dashicons-portfolio'],
            ['فرم', 'فرم تماس AJAX با فیلدهای دلخواه، ایمیل و ذخیره در پیشخوان.', 'dashicons-feedback'],
        ];
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
        $msgs = self::get_messages();
        $name = DARKSO_OPTION;
        ?>
        <div class="wrap darkso-app" dir="rtl">
            <h1 class="screen-reader-text">Darkso</h1>

            <header class="darkso-hero">
                <div class="darkso-hero__logo" aria-hidden="true">
                    <svg viewBox="0 0 24 24" width="30" height="30" fill="currentColor"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"/></svg>
                </div>
                <div class="darkso-hero__text">
                    <h2>Darkso <span class="darkso-badge">v<?php echo esc_html(DARKSO_VERSION); ?></span></h2>
                    <p>دارک‌مود هوشمند برای وردپرس و المنتور با نگاشت رنگ دلخواه</p>
                </div>
                <div class="darkso-hero__stats">
                    <span><strong><?php echo (int) count($maps); ?></strong> رنگ نگاشت‌شده</span>
                    <span><strong><?php echo (int) count(self::widgets_list()); ?></strong> ویجت المنتور</span>
                </div>
            </header>

            <?php if (isset($_GET['settings-updated']) && $_GET['settings-updated'] === 'true') : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
                <div class="darkso-toast" role="status">✓ تنظیمات با موفقیت ذخیره شد.</div>
            <?php endif; ?>

            <nav class="darkso-tabs" role="tablist" aria-label="Darkso">
                <button type="button" class="darkso-tab is-active" data-tab="colors" role="tab">🎨 رنگ‌ها</button>
                <button type="button" class="darkso-tab" data-tab="messages" role="tab">💬 پیام‌ها</button>
                <button type="button" class="darkso-tab" data-tab="widgets" role="tab">🧩 ویجت‌ها</button>
                <button type="button" class="darkso-tab" data-tab="guide" role="tab">📘 راهنما</button>
            </nav>

            <form method="post" action="options.php" class="darkso-form-admin">
                <?php settings_fields('darkso'); ?>

                <section class="darkso-panel is-active" data-panel="colors">
                    <div class="darkso-card-admin">
                        <div class="darkso-card-admin__head">
                            <h3>نگاشت رنگ‌ها</h3>
                            <p>هر رنگی که در سایت (در حالت روشن) استفاده شده را به رنگ دارک‌مود تبدیل کنید. برای هر ردیف یک توضیح بنویسید تا قاطی نشود (مثلاً «پس‌زمینه هدر»، «رنگ دکمه»).</p>
                        </div>

                        <div class="darkso-tablewrap">
                            <table class="darkso-table" id="darkso-table">
                                <thead>
                                    <tr>
                                        <th>توضیح</th>
                                        <th>از رنگ (حالت روشن)</th>
                                        <th>به رنگ (دارک‌مود)</th>
                                        <th>پیش‌نمایش</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($maps as $i => $row) : ?>
                                        <tr>
                                            <td data-label="توضیح">
                                                <input type="text" class="darkso-note regular-text" name="<?php echo esc_attr($name); ?>[<?php echo (int) $i; ?>][note]" value="<?php echo esc_attr($row['note'] ?? ''); ?>" placeholder="مثلاً پس‌زمینه هدر">
                                            </td>
                                            <td data-label="از رنگ">
                                                <input type="text" class="darkso-color" name="<?php echo esc_attr($name); ?>[<?php echo (int) $i; ?>][from]" value="<?php echo esc_attr($row['from']); ?>">
                                            </td>
                                            <td data-label="به رنگ">
                                                <input type="text" class="darkso-color" name="<?php echo esc_attr($name); ?>[<?php echo (int) $i; ?>][to]" value="<?php echo esc_attr($row['to']); ?>">
                                            </td>
                                            <td data-label="پیش‌نمایش">
                                                <span class="darkso-preview">
                                                    <span class="darkso-chip darkso-chip--from" style="background:<?php echo esc_attr($row['from']); ?>;color:<?php echo esc_attr($row['to']); ?>">Aa</span>
                                                    <span class="darkso-arrow">←</span>
                                                    <span class="darkso-chip darkso-chip--to" style="background:<?php echo esc_attr($row['to']); ?>;color:<?php echo esc_attr($row['from']); ?>">Aa</span>
                                                </span>
                                            </td>
                                            <td>
                                                <button type="button" class="button darkso-remove" aria-label="حذف ردیف">حذف</button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <p class="darkso-actions">
                            <button type="button" class="button button-secondary" id="darkso-add">＋ افزودن رنگ</button>
                        </p>

                        <div class="darkso-tip">
                            <strong>نکته:</strong> کد رنگ را دقیقاً همان‌طور که در المنتور/قالب استفاده کرده‌اید وارد کنید. <code>#fff</code> و <code>#ffffff</code> یکسان حساب می‌شوند و <code>rgb()</code> / <code>rgba()</code> هم تشخیص داده می‌شوند.
                        </div>
                    </div>
                </section>

                <section class="darkso-panel" data-panel="messages">
                    <div class="darkso-card-admin">
                        <div class="darkso-card-admin__head">
                            <h3>پیام دکمه‌های یک‌طرفه</h3>
                            <p>کلاس <?php echo self::copy_code('Lightmodebtn'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> فقط سایت را روشن می‌کند و کلاس <?php echo self::copy_code('Darkmodebtn'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> فقط دارک. اگر همان حالت از قبل فعال باشد، پیام‌های زیر نمایش داده می‌شوند. روی خود دکمه هم می‌توانید <code>data-darkso-msg</code> بگذارید تا همان دکمه پیام جدا داشته باشد.</p>
                        </div>

                        <div class="darkso-fields">
                            <label class="darkso-field-admin" for="darkso-light-msg">
                                <span>پیام دکمه روشن (وقتی از قبل روشن است)</span>
                                <input type="text" class="regular-text" id="darkso-light-msg" name="<?php echo esc_attr(DARKSO_MESSAGES); ?>[light_already]" value="<?php echo esc_attr($msgs['light_already']); ?>">
                            </label>
                            <label class="darkso-field-admin" for="darkso-dark-msg">
                                <span>پیام دکمه دارک (وقتی از قبل دارک است)</span>
                                <input type="text" class="regular-text" id="darkso-dark-msg" name="<?php echo esc_attr(DARKSO_MESSAGES); ?>[dark_already]" value="<?php echo esc_attr($msgs['dark_already']); ?>">
                            </label>
                        </div>
                    </div>
                </section>

                <section class="darkso-panel" data-panel="widgets">
                    <div class="darkso-card-admin">
                        <div class="darkso-card-admin__head">
                            <h3>ویجت‌های المنتور</h3>
                            <p>همه ویجت‌ها در دسته <strong>Darkso</strong> پنل المنتور (و همچنین در دسته‌های پایه) پیدا می‌شوند. در هر ویجت، رنگ‌ها در دو تب «روشن» و «تاریک» تنظیم می‌شوند.</p>
                        </div>
                        <div class="darkso-widgets-grid">
                            <?php foreach (self::widgets_list() as $w) : ?>
                                <div class="darkso-widget-card">
                                    <span class="dashicons <?php echo esc_attr($w[2]); ?>"></span>
                                    <h4><?php echo esc_html($w[0]); ?></h4>
                                    <p><?php echo esc_html($w[1]); ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>

                <section class="darkso-panel" data-panel="guide">
                    <div class="darkso-card-admin darkso-guide">
                        <div class="darkso-card-admin__head">
                            <h3>راهنمای استفاده</h3>
                            <p>خلاصه‌ی کامل راه‌اندازی و کار با Darkso؛ از بالا به پایین بخوانید.</p>
                        </div>

                        <details open>
                            <summary>۱) شروع سریع (۳ قدم)</summary>
                            <ol>
                                <li>در تب «رنگ‌ها» رنگ‌های روشن سایت را به رنگ‌های دارک تبدیل کنید و ذخیره بزنید.</li>
                                <li>در المنتور یک دکمه یا ویجت «سوییچ دارک‌مود» بگذارید (یا به هر المنت کلاس <?php echo self::copy_code('darkbouttom'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> بدهید).</li>
                                <li>سایت را باز کنید و روی دکمه کلیک کنید. انتخاب کاربر در مرورگر ذخیره می‌شود.</li>
                            </ol>
                        </details>

                        <details>
                            <summary>۲) نحوه‌ی اضافه کردن کلاس در المنتور</summary>
                            <p>المنت را انتخاب کنید ← <strong>پیشرفته (Advanced)</strong> ← فیلد <strong>CSS Classes</strong> ← نام کلاس را <strong>بدون نقطه</strong> بنویسید (مثلاً <code>Darkshow</code>). برای دکمه‌ها می‌توانید لینک را روی <code>#</code> بگذارید.</p>
                        </details>

                        <details>
                            <summary>۳) کلاس‌های آماده</summary>
                            <table class="darkso-classes">
                                <tbody>
                                    <tr><td><?php echo self::copy_code('darkbouttom'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td><td>سوییچ بین روشن و تاریک (هر کلیک حالت را برعکس می‌کند).</td></tr>
                                    <tr><td><?php echo self::copy_code('Lightmodebtn'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td><td>فقط به حالت روشن می‌رود؛ اگر از قبل روشن بود پیام نشان می‌دهد.</td></tr>
                                    <tr><td><?php echo self::copy_code('Darkmodebtn'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td><td>فقط به حالت تاریک می‌رود؛ اگر از قبل تاریک بود پیام نشان می‌دهد.</td></tr>
                                    <tr><td><?php echo self::copy_code('Lightshow'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td><td><strong>جدید:</strong> المنت فقط در حالت روشن نمایش داده می‌شود و در دارک‌مود خاموش (مخفی) است.</td></tr>
                                    <tr><td><?php echo self::copy_code('Darkshow'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td><td><strong>جدید:</strong> المنت فقط در حالت تاریک نمایش داده می‌شود و در حالت روشن مخفی است.</td></tr>
                                </tbody>
                            </table>
                            <p class="darkso-muted">نمونه: لوگوی روشن را با کلاس <code>Lightshow</code> و لوگوی سفید را با کلاس <code>Darkshow</code> بگذارید. در ویرایشگر المنتور هر دو دیده می‌شوند (با کادر خط‌چین) تا بتوانید ویرایششان کنید.</p>
                        </details>

                        <details>
                            <summary>۴) ویجت‌های لایت / دارک</summary>
                            <p>ویجت‌های «تصویر»، «متن»، «عنوان»، «آیکن»، «منو» و «باکس آیکن» برای هر حالت محتوای جدا دارند. اگر یکی از دو حالت را خالی بگذارید، محتوای حالت دیگر استفاده می‌شود.</p>
                        </details>

                        <details>
                            <summary>۵) نوشته‌ها، Loop Grid و Loop Carousel</summary>
                            <ul>
                                <li><strong>کوئری:</strong> نوع نوشته، دسته/برچسب، تعداد، رد کردن چند آیتم اول، مرتب‌سازی و حذف نوشته‌ی جاری.</li>
                                <li><strong>قالب آیتم (Loop Grid و Loop Carousel):</strong> یک قالب ذخیره‌شده المنتور را انتخاب کنید تا برای هر نوشته تکرار شود؛ اگر انتخاب نکنید کارت پیش‌فرض نمایش داده می‌شود.</li>
                                <li><strong>صفحه‌بندی:</strong> در Post و Loop Grid گزینه «شماره صفحات» را فعال کنید.</li>
                                <li><strong>کروسل:</strong> تعداد اسلاید در دسکتاپ/تبلت/موبایل، فلش‌ها، نقطه‌ها و پخش خودکار قابل تنظیم است.</li>
                            </ul>
                        </details>

                        <details>
                            <summary>۶) پورتفولیو</summary>
                            <p>نوع نوشته (مثلاً نوشته‌ی سفارشی «نمونه کار») و طبقه‌بندی فیلتر را انتخاب کنید. دکمه‌های فیلتر خودکار از دسته‌ها ساخته می‌شوند. با کلیک می‌توانید به صفحه نوشته بروید یا تصویر را در لایت‌باکس ببینید.</p>
                        </details>

                        <details>
                            <summary>۷) فرم</summary>
                            <ul>
                                <li>فیلدها را اضافه/حذف کنید (متن، ایمیل، تلفن، عدد، متن بلند، لیست کشویی، چک‌باکس) و عرض هر فیلد را ۱۰۰٪، ۵۰٪ یا ۳۳٪ بگذارید.</li>
                                <li>ایمیل گیرنده و موضوع را تنظیم کنید. اگر خالی بماند، ایمیل مدیر سایت استفاده می‌شود.</li>
                                <li>با فعال بودن «ذخیره پیام در پیشخوان»، همه پیام‌ها در منوی <strong>پیام‌های فرم</strong> دیده می‌شوند (حتی اگر ایمیل ارسال نشود).</li>
                                <li>محافظت از اسپم: فیلد مخفی (Honeypot)، محدودیت ۵ ارسال در دقیقه برای هر IP و Nonce تازه برای هر ارسال.</li>
                                <li>اگر ایمیل نمی‌رسد، یک افزونه SMTP نصب کنید.</li>
                            </ul>
                        </details>

                        <details>
                            <summary>۸) عیب‌یابی</summary>
                            <ul>
                                <li><strong>تغییرات رنگ دیده نمی‌شود:</strong> کش سایت/افزونه کش و کش المنتور (Regenerate CSS) را پاک کنید.</li>
                                <li><strong>رنگی تبدیل نمی‌شود:</strong> کد رنگ را دقیقاً مطابق رنگ واقعی المنت اضافه کنید (اگر شفافیت دارد، رنگ پایه را بنویسید).</li>
                                <li><strong>Darkshow / Lightshow کار نمی‌کند:</strong> کلاس را بدون نقطه و با حرف اول بزرگ بنویسید.</li>
                                <li><strong>ویجت‌ها در المنتور نیستند:</strong> افزونه المنتور باید فعال باشد؛ سپس صفحه ویرایشگر را یک بار رفرش کنید.</li>
                            </ul>
                        </details>
                    </div>
                </section>

                <div class="darkso-savebar">
                    <span class="darkso-savebar__hint">پس از تغییر رنگ‌ها، ذخیره را بزنید و کش سایت را پاک کنید.</span>
                    <?php submit_button('ذخیره تنظیمات', 'primary large', 'submit', false); ?>
                </div>
            </form>
        </div>
        <?php
    }
}
