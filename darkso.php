<?php
/**
 * Plugin Name: Darkso
 * Description: دارک‌مود وردپرس و المنتور با نگاشت رنگ دلخواه. سوییچ با کلاس darkbouttom.
 * Version: 5.0.0
 * Elementor tested up to: 4.25.0
 * Author: Sobhan khalili
 * Text Domain: darkso
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

if (!defined('ABSPATH')) {
    exit;
}

define('DARKSO_VERSION', '5.0.0');
define('DARKSO_FILE', __FILE__);
define('DARKSO_DIR', plugin_dir_path(__FILE__));
define('DARKSO_URL', plugin_dir_url(__FILE__));
define('DARKSO_OPTION', 'darkso_color_maps');
define('DARKSO_MESSAGES', 'darkso_messages');

require_once DARKSO_DIR . 'includes/class-darkso-admin.php';
require_once DARKSO_DIR . 'includes/class-darkso-frontend.php';
require_once DARKSO_DIR . 'includes/class-darkso-elementor.php';

add_action('plugins_loaded', static function () {
    Darkso_Admin::init();
    Darkso_Frontend::init();
    Darkso_Elementor::init();
});

register_activation_hook(__FILE__, static function () {
    if (get_option(DARKSO_OPTION, null) === null) {
        add_option(DARKSO_OPTION, Darkso_Admin::default_maps());
    }
});
