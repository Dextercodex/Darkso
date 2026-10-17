<?php

if (!defined('ABSPATH')) {
    exit;
}

class Darkso_Icon_Widget extends \Elementor\Widget_Base
{
    public function get_name()
    {
        return 'darkso_icon';
    }

    public function get_title()
    {
        return 'آیکن لایت / دارک';
    }

    public function get_icon()
    {
        return 'eicon-star';
    }

    public function get_categories()
    {
        return ['darkso', 'basic', 'general'];
    }

    public function get_keywords()
    {
        return ['icon', 'dark', 'light', 'darkso', 'آیکن'];
    }

    public function get_style_depends()
    {
        return ['darkso-widgets'];
    }

    protected function register_controls()
    {
        $this->start_controls_section('section_icons', [
            'label' => 'آیکن‌ها',
        ]);

        $this->add_control('icon_light', [
            'label' => 'آیکن حالت روشن',
            'type' => \Elementor\Controls_Manager::ICONS,
            'fa4compatibility' => 'icon',
            'default' => [
                'value' => 'fas fa-star',
                'library' => 'fa-solid',
            ],
        ]);

        $this->add_control('icon_dark', [
            'label' => 'آیکن حالت تاریک',
            'type' => \Elementor\Controls_Manager::ICONS,
            'default' => [
                'value' => 'fas fa-moon',
                'library' => 'fa-solid',
            ],
        ]);

        $this->add_control('link', [
            'label' => 'لینک',
            'type' => \Elementor\Controls_Manager::URL,
            'dynamic' => ['active' => true],
            'placeholder' => 'https://',
        ]);

        $this->end_controls_section();

        $this->start_controls_section('section_style', [
            'label' => 'استایل',
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_responsive_control('align', [
            'label' => 'چینش',
            'type' => \Elementor\Controls_Manager::CHOOSE,
            'options' => [
                'right' => ['title' => 'راست', 'icon' => 'eicon-text-align-right'],
                'center' => ['title' => 'وسط', 'icon' => 'eicon-text-align-center'],
                'left' => ['title' => 'چپ', 'icon' => 'eicon-text-align-left'],
            ],
            'selectors' => [
                '{{WRAPPER}} .darkso-icon-wrap' => 'text-align: {{VALUE}};',
            ],
        ]);

        $this->add_responsive_control('size', [
            'label' => 'اندازه',
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px', 'em'],
            'range' => [
                'px' => ['min' => 10, 'max' => 200],
            ],
            'default' => ['size' => 36, 'unit' => 'px'],
            'selectors' => [
                '{{WRAPPER}} .darkso-icon' => 'font-size: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .darkso-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_control('rotate', [
            'label' => 'چرخش',
            'type' => \Elementor\Controls_Manager::SLIDER,
            'range' => [
                'px' => ['min' => 0, 'max' => 360],
            ],
            'selectors' => [
                '{{WRAPPER}} .darkso-icon' => 'transform: rotate({{SIZE}}deg);',
            ],
        ]);

        $this->start_controls_tabs('tabs_icon_color');

        $this->start_controls_tab('tab_icon_light', ['label' => 'روشن']);

        $this->add_control('color_light', [
            'label' => 'رنگ',
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .darkso-icon__light' => 'color: {{VALUE}}; fill: {{VALUE}};',
                '{{WRAPPER}} .darkso-icon__light svg' => 'fill: {{VALUE}};',
                '{{WRAPPER}} .darkso-icon__light i' => 'color: {{VALUE}};',
            ],
        ]);

        $this->end_controls_tab();

        $this->start_controls_tab('tab_icon_dark', ['label' => 'تاریک']);

        $this->add_control('color_dark', [
            'label' => 'رنگ',
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                'html.darkso-dark {{WRAPPER}} .darkso-icon__dark' => 'color: {{VALUE}}; fill: {{VALUE}};',
                'html.darkso-dark {{WRAPPER}} .darkso-icon__dark svg' => 'fill: {{VALUE}};',
                'html.darkso-dark {{WRAPPER}} .darkso-icon__dark i' => 'color: {{VALUE}};',
            ],
        ]);

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();
    }

    private function has_icon($icon)
    {
        return is_array($icon) && !empty($icon['value']);
    }

    private function print_icon($icon)
    {
        if (!$this->has_icon($icon) || !class_exists('\Elementor\Icons_Manager')) {
            return;
        }
        \Elementor\Icons_Manager::render_icon($icon, [
            'aria-hidden' => 'true',
            'class' => 'darkso-icon__svg',
        ]);
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $light = is_array($settings['icon_light'] ?? null) ? $settings['icon_light'] : [];
        $dark = is_array($settings['icon_dark'] ?? null) ? $settings['icon_dark'] : [];

        if (!$this->has_icon($light) && !$this->has_icon($dark)) {
            return;
        }
        if (!$this->has_icon($dark)) {
            $dark = $light;
        }
        if (!$this->has_icon($light)) {
            $light = $dark;
        }

        if (class_exists('\Elementor\Icons_Manager') && method_exists('\Elementor\Icons_Manager', 'enqueue_shim')) {
            \Elementor\Icons_Manager::enqueue_shim();
        }

        $has_link = !empty($settings['link']['url']);
        $uid = $this->get_id();
        $link_key = 'darkso_icon_link_' . $uid;

        $open = '<div class="darkso-icon-wrap">';
        $close = '</div>';
        if ($has_link && method_exists($this, 'add_link_attributes')) {
            $this->add_link_attributes($link_key, $settings['link']);
            $this->add_render_attribute($link_key, 'class', 'darkso-icon-wrap');
            $open = '<a ' . $this->get_render_attribute_string($link_key) . '>';
            $close = '</a>';
        }

        echo $open;
        echo '<span class="darkso-icon darkso-swap darkso-swap--overlay">';
        echo '<span class="darkso-icon__light darkso-swap__light">';
        $this->print_icon($light);
        echo '</span>';
        echo '<span class="darkso-icon__dark darkso-swap__dark">';
        $this->print_icon($dark);
        echo '</span>';
        echo '</span>';
        echo $close;
    }

    protected function content_template()
    {
        ?>
        <#
        var lightIcon = false;
        var darkIcon = false;
        if (settings.icon_light && settings.icon_light.value) {
            lightIcon = elementor.helpers.renderIcon(view, settings.icon_light, { 'aria-hidden': true }, 'i', 'object');
        }
        if (settings.icon_dark && settings.icon_dark.value) {
            darkIcon = elementor.helpers.renderIcon(view, settings.icon_dark, { 'aria-hidden': true }, 'i', 'object');
        }
        if (lightIcon && !darkIcon) darkIcon = lightIcon;
        if (darkIcon && !lightIcon) lightIcon = darkIcon;
        var hasLink = !!(settings.link && settings.link.url);
        var lightHtml = lightIcon && lightIcon.value ? lightIcon.value : '';
        var darkHtml = darkIcon && darkIcon.value ? darkIcon.value : '';
        #>
        <# if (hasLink) { #><a class="darkso-icon-wrap" href="{{ settings.link.url }}"><# } else { #><div class="darkso-icon-wrap"><# } #>
            <span class="darkso-icon darkso-swap darkso-swap--overlay">
                <span class="darkso-icon__light darkso-swap__light">{{{ lightHtml }}}</span>
                <span class="darkso-icon__dark darkso-swap__dark">{{{ darkHtml }}}</span>
            </span>
        <# if (hasLink) { #></a><# } else { #></div><# } #>
        <?php
    }
}
