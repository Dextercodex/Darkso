<?php

if (!defined('ABSPATH')) {
    exit;
}

class Darkso_Frontend
{
    public static function init()
    {
        add_action('wp_enqueue_scripts', [self::class, 'assets']);
        add_action('elementor/frontend/after_enqueue_scripts', [self::class, 'assets']);
        add_action('wp_head', [self::class, 'anti_fouc'], 1);
        add_action('wp_footer', [self::class, 'footer_fallback'], 1);
    }

    public static function assets()
    {
        wp_enqueue_style(
            'darkso-widgets',
            DARKSO_URL . 'assets/widgets.css',
            [],
            DARKSO_VERSION
        );

        wp_enqueue_script(
            'darkso-frontend',
            DARKSO_URL . 'assets/frontend.js',
            [],
            DARKSO_VERSION,
            true
        );

        wp_enqueue_script(
            'darkso-widgets',
            DARKSO_URL . 'assets/widgets.js',
            ['darkso-frontend'],
            DARKSO_VERSION,
            true
        );

        wp_localize_script('darkso-frontend', 'darksoData', [
            'maps' => array_map(static function ($row) {
                return [
                    'from' => $row['from'],
                    'to' => $row['to'],
                ];
            }, Darkso_Admin::get_maps()),
            'storageKey' => 'darkso_mode',
            'toggleClass' => 'darkbouttom',
            'lightClass' => 'Lightmodebtn',
            'darkClass' => 'Darkmodebtn',
            'htmlClass' => 'darkso-dark',
            'messages' => Darkso_Admin::get_messages(),
            'ajaxUrl' => admin_url('admin-ajax.php'),
        ]);
    }

    public static function anti_fouc()
    {
        ?>
        <script>
        (function () {
            try {
                if (localStorage.getItem('darkso_mode') === 'dark') {
                    document.documentElement.classList.add('darkso-dark');
                }
            } catch (e) {}
        })();
        </script>
        <?php
    }

    public static function footer_fallback()
    {
        ?>
        <script>
        (function () {
            document.addEventListener('click', function (e) {
                if (!e.target || !e.target.closest) {
                    return;
                }
                var lightBtn = e.target.closest('.Lightmodebtn');
                var darkBtn = e.target.closest('.Darkmodebtn');
                var toggleBtn = e.target.closest('.darkbouttom');
                if (!lightBtn && !darkBtn && !toggleBtn) {
                    return;
                }
                e.preventDefault();
                e.stopPropagation();
                var isDark = document.documentElement.classList.contains('darkso-dark');
                if (lightBtn) {
                    if (typeof window.darksoForceLight === 'function') {
                        window.darksoForceLight(lightBtn);
                        return;
                    }
                    if (!isDark) {
                        return;
                    }
                    document.documentElement.classList.remove('darkso-dark');
                    if (document.body) {
                        document.body.classList.remove('darkso-dark');
                    }
                    try { localStorage.setItem('darkso_mode', 'light'); } catch (err) {}
                    return;
                }
                if (darkBtn) {
                    if (typeof window.darksoForceDark === 'function') {
                        window.darksoForceDark(darkBtn);
                        return;
                    }
                    if (isDark) {
                        return;
                    }
                    document.documentElement.classList.add('darkso-dark');
                    if (document.body) {
                        document.body.classList.add('darkso-dark');
                    }
                    try { localStorage.setItem('darkso_mode', 'dark'); } catch (err) {}
                    return;
                }
                if (typeof window.darksoToggle === 'function') {
                    window.darksoToggle();
                }
            }, true);
        })();
        </script>
        <?php
    }
}
