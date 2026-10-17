<?php

if (!defined('ABSPATH')) {
    exit;
}

class Darkso_Loop_Carousel_Widget extends Darkso_Posts_Base
{
    public function get_name()
    {
        return 'darkso_loop_carousel';
    }

    public function get_title()
    {
        return 'Loop Carousel لایت / دارک';
    }

    public function get_icon()
    {
        return 'eicon-slider-push';
    }

    public function get_keywords()
    {
        return ['loop', 'carousel', 'slider', 'posts', 'dark', 'light', 'darkso', 'کروسل', 'اسلایدر'];
    }

    protected function use_template()
    {
        return true;
    }

    protected function default_per_page()
    {
        return 8;
    }

    protected function register_controls()
    {
        $this->start_controls_section('section_carousel', ['label' => 'کروسل']);

        $this->add_responsive_control('slides_to_show', [
            'label' => 'تعداد اسلاید در نما',
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
                '{{WRAPPER}} .darkso-carousel' => '--darkso-cols: {{VALUE}};',
            ],
        ]);

        $this->add_responsive_control('gap', [
            'label' => 'فاصله اسلایدها',
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range' => ['px' => ['min' => 0, 'max' => 80]],
            'default' => ['size' => 24, 'unit' => 'px'],
            'selectors' => [
                '{{WRAPPER}} .darkso-carousel' => '--darkso-gap: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_control('show_arrows', [
            'label' => 'فلش‌ها',
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => 'yes',
        ]);

        $this->add_control('show_dots', [
            'label' => 'نقطه‌های پیمایش',
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => 'yes',
        ]);

        $this->add_control('autoplay', [
            'label' => 'پخش خودکار',
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => '',
        ]);

        $this->add_control('autoplay_delay', [
            'label' => 'فاصله پخش خودکار (میلی‌ثانیه)',
            'type' => \Elementor\Controls_Manager::NUMBER,
            'default' => 4000,
            'min' => 1500,
            'max' => 20000,
            'step' => 100,
            'condition' => ['autoplay' => 'yes'],
        ]);

        $this->add_control('pause_on_hover', [
            'label' => 'توقف با هاور',
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => 'yes',
            'condition' => ['autoplay' => 'yes'],
        ]);

        $this->end_controls_section();

        $this->register_common_controls();

        $this->start_controls_section('section_nav_style', [
            'label' => 'فلش‌ها و نقطه‌ها',
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_responsive_control('nav_size', [
            'label' => 'اندازه فلش',
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range' => ['px' => ['min' => 28, 'max' => 80]],
            'default' => ['size' => 44, 'unit' => 'px'],
            'selectors' => [
                '{{WRAPPER}} .darkso-carousel__nav' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
            ],
        ]);

        Darkso_Helpers::style_tabs($this, 'tabs_nav', [
            'nav_color' => ['label' => 'رنگ فلش', 'sel' => '.darkso-carousel__nav', 'prop' => 'color'],
            'nav_bg' => ['label' => 'پس‌زمینه فلش', 'sel' => '.darkso-carousel__nav', 'prop' => 'background-color'],
            'dot_color' => ['label' => 'رنگ نقطه', 'sel' => '.darkso-carousel__dot', 'prop' => 'background-color'],
            'dot_active' => ['label' => 'نقطه فعال', 'sel' => '.darkso-carousel__dot.is-active', 'prop' => 'background-color'],
        ]);

        $this->end_controls_section();
    }

    private function arrow_svg($dir)
    {
        $path = $dir === 'prev' ? 'M15 5l-7 7 7 7' : 'M9 5l7 7-7 7';
        return '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="' . $path . '"/></svg>';
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();

        ob_start();
        $info = $this->render_items($settings, 'darkso-carousel__slide');
        $slides = ob_get_clean();

        if ($info['count'] === 0) {
            $this->render_empty_notice();
            return;
        }

        $autoplay = ($settings['autoplay'] ?? '') === 'yes' ? '1' : '0';
        $delay = max(1500, min(20000, (int) ($settings['autoplay_delay'] ?? 4000)));
        $pause = ($settings['pause_on_hover'] ?? 'yes') === 'yes' ? '1' : '0';

        echo '<div class="darkso-carousel" data-darkso-carousel data-autoplay="' . esc_attr($autoplay) . '" data-delay="' . esc_attr((string) $delay) . '" data-pause="' . esc_attr($pause) . '">';
        echo '<div class="darkso-carousel__viewport" tabindex="0"><div class="darkso-carousel__track">';
        echo $slides; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo '</div></div>';

        if (($settings['show_arrows'] ?? 'yes') === 'yes') {
            echo '<button type="button" class="darkso-carousel__nav darkso-carousel__prev" aria-label="قبلی">' . $this->arrow_svg('prev') . '</button>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo '<button type="button" class="darkso-carousel__nav darkso-carousel__next" aria-label="بعدی">' . $this->arrow_svg('next') . '</button>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }

        if (($settings['show_dots'] ?? 'yes') === 'yes') {
            echo '<div class="darkso-carousel__dots"></div>';
        }

        echo '</div>';
    }
}
