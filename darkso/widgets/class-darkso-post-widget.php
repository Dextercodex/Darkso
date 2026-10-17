<?php

if (!defined('ABSPATH')) {
    exit;
}

class Darkso_Post_Widget extends Darkso_Posts_Base
{
    public function get_name()
    {
        return 'darkso_post';
    }

    public function get_title()
    {
        return 'نوشته‌ها لایت / دارک';
    }

    public function get_icon()
    {
        return 'eicon-post-list';
    }

    public function get_keywords()
    {
        return ['post', 'posts', 'blog', 'dark', 'light', 'darkso', 'نوشته', 'مقاله', 'پست'];
    }

    protected function use_pagination()
    {
        return true;
    }

    protected function register_controls()
    {
        $this->start_controls_section('section_layout', ['label' => 'چیدمان']);

        $this->add_control('layout', [
            'label' => 'نوع نمایش',
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'classic',
            'options' => [
                'classic' => 'کلاسیک (کارت)',
                'overlay' => 'روی تصویر (Overlay)',
                'list' => 'لیستی (افقی)',
                'minimal' => 'مینیمال',
            ],
        ]);

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
            'condition' => ['layout!' => 'list'],
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

        $this->end_controls_section();

        $this->register_common_controls();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $layout = in_array(($settings['layout'] ?? 'classic'), ['classic', 'overlay', 'list', 'minimal'], true) ? $settings['layout'] : 'classic';

        ob_start();
        $info = $this->render_items($settings, 'darkso-grid__item');
        $items = ob_get_clean();

        if ($info['count'] === 0) {
            $this->render_empty_notice();
            return;
        }

        echo '<div class="darkso-grid darkso-posts darkso-posts--' . esc_attr($layout) . '">';
        echo $items; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo '</div>';

        if ($info['use_pages']) {
            Darkso_Helpers::render_pagination($info['pages'], $info['page'], $info['param']);
        }
    }
}
