<?php

if (!defined('ABSPATH')) {
    exit;
}

class Darkso_Loop_Grid_Widget extends Darkso_Posts_Base
{
    public function get_name()
    {
        return 'darkso_loop_grid';
    }

    public function get_title()
    {
        return 'Loop Grid لایت / دارک';
    }

    public function get_icon()
    {
        return 'eicon-posts-grid';
    }

    public function get_keywords()
    {
        return ['loop', 'grid', 'posts', 'dark', 'light', 'darkso', 'گرید', 'لوپ'];
    }

    protected function use_template()
    {
        return true;
    }

    protected function use_pagination()
    {
        return true;
    }

    protected function default_per_page()
    {
        return 6;
    }

    protected function register_controls()
    {
        $this->start_controls_section('section_layout', ['label' => 'چیدمان']);

        $this->add_responsive_control('columns', [
            'label' => 'تعداد ستون',
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => '3',
            'tablet_default' => '2',
            'mobile_default' => '1',
            'options' => [
                '1' => '1',
                '2' => '2',
                '3' => '3',
                '4' => '4',
                '5' => '5',
                '6' => '6',
            ],
            'selectors' => [
                '{{WRAPPER}} .darkso-grid' => '--darkso-cols: {{VALUE}};',
            ],
        ]);

        $this->add_responsive_control('gap', [
            'label' => 'فاصله آیتم‌ها',
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range' => ['px' => ['min' => 0, 'max' => 80]],
            'default' => ['size' => 24, 'unit' => 'px'],
            'selectors' => [
                '{{WRAPPER}} .darkso-grid' => '--darkso-gap: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_control('equal_height', [
            'label' => 'ارتفاع برابر آیتم‌ها',
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => 'yes',
        ]);

        $this->end_controls_section();

        $this->register_common_controls();

        $this->start_controls_section('section_pagination_style', [
            'label' => 'صفحه‌بندی',
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            'condition' => ['pagination' => 'numbers'],
        ]);

        Darkso_Helpers::style_tabs($this, 'tabs_pagination', [
            'pg_color' => ['label' => 'رنگ متن', 'sel' => '.darkso-pagination .page-numbers', 'prop' => 'color'],
            'pg_bg' => ['label' => 'پس‌زمینه', 'sel' => '.darkso-pagination .page-numbers', 'prop' => 'background-color'],
            'pg_active_color' => ['label' => 'متن صفحه فعال', 'sel' => '.darkso-pagination .page-numbers.current', 'prop' => 'color'],
            'pg_active_bg' => ['label' => 'پس‌زمینه صفحه فعال', 'sel' => '.darkso-pagination .page-numbers.current', 'prop' => 'background-color'],
        ]);

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();

        ob_start();
        $info = $this->render_items($settings, 'darkso-grid__item');
        $items = ob_get_clean();

        if ($info['count'] === 0) {
            $this->render_empty_notice();
            return;
        }

        $class = 'darkso-grid darkso-loop-grid';
        if (($settings['equal_height'] ?? 'yes') === 'yes') {
            $class .= ' darkso-grid--equal';
        }

        echo '<div class="' . esc_attr($class) . '">';
        echo $items; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo '</div>';

        if ($info['use_pages']) {
            Darkso_Helpers::render_pagination($info['pages'], $info['page'], $info['param']);
        }
    }
}
