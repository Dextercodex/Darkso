<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * کلاس پایه برای ویجت‌های Post / Loop Grid / Loop Carousel.
 */
abstract class Darkso_Posts_Base extends \Elementor\Widget_Base
{
    public function get_categories()
    {
        return ['darkso', 'basic', 'general'];
    }

    public function get_style_depends()
    {
        return ['darkso-widgets'];
    }

    public function get_script_depends()
    {
        return ['darkso-widgets'];
    }

    protected function use_template()
    {
        return false;
    }

    protected function use_pagination()
    {
        return false;
    }

    protected function default_per_page()
    {
        return 6;
    }

    protected function register_common_controls()
    {
        Darkso_Helpers::add_query_section($this, [
            'per_page' => $this->default_per_page(),
            'pagination' => $this->use_pagination(),
            'post_type' => 'post',
        ]);
        Darkso_Helpers::add_card_controls($this, $this->use_template());
        Darkso_Helpers::add_card_style($this);
    }

    /**
     * آیتم‌ها را با کلاس داده‌شده چاپ می‌کند و اطلاعات صفحه‌بندی را برمی‌گرداند.
     */
    protected function render_items($settings, $item_class)
    {
        $param = 'dp_' . sanitize_key($this->get_id());
        $use_pages = $this->use_pagination() && (($settings['pagination'] ?? 'none') === 'numbers');
        $page = $use_pages ? Darkso_Helpers::current_page($param) : 1;

        $query = new \WP_Query(Darkso_Helpers::query_args($settings, $page));

        $info = [
            'param' => $param,
            'page' => $page,
            'pages' => 0,
            'count' => 0,
            'use_pages' => $use_pages,
        ];

        if (!$query->have_posts()) {
            wp_reset_postdata();
            return $info;
        }

        while ($query->have_posts()) {
            $query->the_post();
            $info['count']++;
            echo '<div class="' . esc_attr($item_class) . '">';
            Darkso_Helpers::render_item($settings);
            echo '</div>';
        }

        $info['pages'] = Darkso_Helpers::total_pages($query, $settings);
        wp_reset_postdata();

        return $info;
    }

    protected function render_empty_notice()
    {
        if (Darkso_Helpers::is_edit()) {
            echo '<div class="darkso-menu-empty">هیچ نوشته‌ای با این تنظیمات پیدا نشد.</div>';
        }
    }
}
