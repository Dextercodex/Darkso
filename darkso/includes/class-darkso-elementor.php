<?php

if (!defined('ABSPATH')) {
    exit;
}

class Darkso_Elementor
{
    private static $registered = false;

    public static function init()
    {
        add_action('elementor/elements/categories_registered', [self::class, 'category']);
        add_action('elementor/init', [self::class, 'category_on_init']);
        add_action('elementor/widgets/register', [self::class, 'widgets']);
        add_action('elementor/widgets/widgets_registered', [self::class, 'widgets']);
        add_action('elementor/frontend/after_register_styles', [self::class, 'register_styles']);
        add_action('elementor/frontend/after_register_scripts', [self::class, 'register_scripts']);
        add_action('elementor/editor/after_enqueue_styles', [self::class, 'enqueue_styles']);
        add_action('elementor/preview/enqueue_styles', [self::class, 'enqueue_styles']);
    }

    public static function register_styles()
    {
        wp_register_style(
            'darkso-widgets',
            DARKSO_URL . 'assets/widgets.css',
            [],
            DARKSO_VERSION
        );
        wp_register_style(
            'darkso-toggle',
            DARKSO_URL . 'assets/widgets.css',
            [],
            DARKSO_VERSION
        );
    }

    public static function register_scripts()
    {
        wp_register_script(
            'darkso-widgets',
            DARKSO_URL . 'assets/widgets.js',
            ['darkso-frontend'],
            DARKSO_VERSION,
            true
        );
    }

    public static function enqueue_styles()
    {
        self::register_styles();
        wp_enqueue_style('darkso-widgets');
    }

    public static function category($elements_manager)
    {
        if (!$elements_manager || !method_exists($elements_manager, 'add_category')) {
            return;
        }

        $elements_manager->add_category('darkso', [
            'title' => 'Darkso',
            'icon' => 'eicon-adjust',
        ]);
    }

    public static function category_on_init()
    {
        if (!class_exists('\Elementor\Plugin')) {
            return;
        }

        $manager = \Elementor\Plugin::instance()->elements_manager;
        self::category($manager);
    }

    public static function widgets($widgets_manager = null)
    {
        if (self::$registered) {
            return;
        }

        if (!class_exists('\Elementor\Widget_Base')) {
            return;
        }

        if (!$widgets_manager || !is_object($widgets_manager)) {
            if (!class_exists('\Elementor\Plugin')) {
                return;
            }
            $widgets_manager = \Elementor\Plugin::instance()->widgets_manager;
        }

        $files = [
            'class-darkso-posts-base.php',
            'class-darkso-toggle-widget.php',
            'class-darkso-image-widget.php',
            'class-darkso-text-widget.php',
            'class-darkso-heading-widget.php',
            'class-darkso-icon-widget.php',
            'class-darkso-menu-widget.php',
            'class-darkso-iconbox-widget.php',
            'class-darkso-post-widget.php',
            'class-darkso-loop-grid-widget.php',
            'class-darkso-loop-carousel-widget.php',
            'class-darkso-portfolio-widget.php',
            'class-darkso-form-widget.php',
        ];

        foreach ($files as $file) {
            require_once DARKSO_DIR . 'widgets/' . $file;
        }

        $instances = [
            new Darkso_Toggle_Widget(),
            new Darkso_Image_Widget(),
            new Darkso_Text_Widget(),
            new Darkso_Heading_Widget(),
            new Darkso_Icon_Widget(),
            new Darkso_Menu_Widget(),
            new Darkso_Iconbox_Widget(),
            new Darkso_Post_Widget(),
            new Darkso_Loop_Grid_Widget(),
            new Darkso_Loop_Carousel_Widget(),
            new Darkso_Portfolio_Widget(),
            new Darkso_Form_Widget(),
        ];

        foreach ($instances as $widget) {
            if (method_exists($widgets_manager, 'register')) {
                $widgets_manager->register($widget);
            } elseif (method_exists($widgets_manager, 'register_widget_type')) {
                $widgets_manager->register_widget_type($widget);
            }
        }

        self::$registered = true;
    }
}
