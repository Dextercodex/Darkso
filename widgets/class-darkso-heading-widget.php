<?php

if (!defined('ABSPATH')) {
    exit;
}

class Darkso_Heading_Widget extends \Elementor\Widget_Base
{
    public function get_name()
    {
        return 'darkso_heading';
    }

    public function get_title()
    {
        return 'عنوان لایت / دارک';
    }

    public function get_icon()
    {
        return 'eicon-t-letter';
    }

    public function get_categories()
    {
        return ['darkso', 'basic', 'general'];
    }

    public function get_keywords()
    {
        return ['heading', 'title', 'dark', 'light', 'darkso', 'عنوان'];
    }

    public function get_style_depends()
    {
        return ['darkso-widgets'];
    }

    protected function register_controls()
    {
        $this->start_controls_section('section_title', [
            'label' => 'عنوان',
        ]);

        $this->add_control('title_light', [
            'label' => 'عنوان حالت روشن',
            'type' => \Elementor\Controls_Manager::TEXTAREA,
            'rows' => 2,
            'default' => 'عنوان حالت روشن',
            'placeholder' => 'عنوان روشن',
            'dynamic' => ['active' => true],
        ]);

        $this->add_control('title_dark', [
            'label' => 'عنوان حالت تاریک',
            'type' => \Elementor\Controls_Manager::TEXTAREA,
            'rows' => 2,
            'default' => 'عنوان حالت تاریک',
            'placeholder' => 'عنوان تاریک',
            'dynamic' => ['active' => true],
        ]);

        $this->add_control('header_size', [
            'label' => 'تگ HTML',
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => [
                'h1' => 'H1',
                'h2' => 'H2',
                'h3' => 'H3',
                'h4' => 'H4',
                'h5' => 'H5',
                'h6' => 'H6',
                'div' => 'div',
                'span' => 'span',
                'p' => 'p',
            ],
            'default' => 'h2',
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
                '{{WRAPPER}} .darkso-heading' => 'text-align: {{VALUE}};',
            ],
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name' => 'typography',
            'selector' => '{{WRAPPER}} .darkso-heading',
        ]);

        $this->add_group_control(\Elementor\Group_Control_Text_Shadow::get_type(), [
            'name' => 'text_shadow',
            'selector' => '{{WRAPPER}} .darkso-heading',
        ]);

        $this->start_controls_tabs('tabs_title_style');

        $this->start_controls_tab('tab_title_light', ['label' => 'روشن']);

        $this->add_control('color_light', [
            'label' => 'رنگ',
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .darkso-heading__light' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('hover_light', [
            'label' => 'رنگ هاور',
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .darkso-heading:hover .darkso-heading__light' => 'color: {{VALUE}};',
            ],
        ]);

        $this->end_controls_tab();

        $this->start_controls_tab('tab_title_dark', ['label' => 'تاریک']);

        $this->add_control('color_dark', [
            'label' => 'رنگ',
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                'html.darkso-dark {{WRAPPER}} .darkso-heading__dark' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('hover_dark', [
            'label' => 'رنگ هاور',
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                'html.darkso-dark {{WRAPPER}} .darkso-heading:hover .darkso-heading__dark' => 'color: {{VALUE}};',
            ],
        ]);

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();
    }

    private function allowed_tag($tag)
    {
        $allowed = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p'];
        $tag = strtolower((string) $tag);
        return in_array($tag, $allowed, true) ? $tag : 'h2';
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $light = isset($settings['title_light']) ? (string) $settings['title_light'] : '';
        $dark = isset($settings['title_dark']) ? (string) $settings['title_dark'] : '';
        if ($light === '' && $dark === '') {
            return;
        }
        if ($dark === '') {
            $dark = $light;
        }
        if ($light === '') {
            $light = $dark;
        }

        $tag = $this->allowed_tag($settings['header_size'] ?? 'h2');
        $has_link = !empty($settings['link']['url']);
        $uid = $this->get_id();
        $link_key = 'darkso_heading_link_' . $uid;

        $open = '<' . $tag . ' class="darkso-heading darkso-swap darkso-swap--stack">';
        $close = '</' . $tag . '>';

        if ($has_link && method_exists($this, 'add_link_attributes')) {
            $this->add_link_attributes($link_key, $settings['link']);
            $this->add_render_attribute($link_key, 'class', 'darkso-heading-link');
            $open = '<a ' . $this->get_render_attribute_string($link_key) . '>' . $open;
            $close .= '</a>';
        }

        echo $open;
        echo '<span class="darkso-heading__light darkso-swap__light">' . esc_html($light) . '</span>';
        echo '<span class="darkso-heading__dark darkso-swap__dark">' . esc_html($dark) . '</span>';
        echo $close;
    }

    protected function content_template()
    {
        ?>
        <#
        var light = settings.title_light || '';
        var dark = settings.title_dark || '';
        if (!dark) dark = light;
        if (!light) light = dark;
        var hasLink = !!(settings.link && settings.link.url);
        #>
        <# if (hasLink) { #><a class="darkso-heading-link" href="{{ settings.link.url }}"><# } #>
        <div class="darkso-heading darkso-swap darkso-swap--stack">
            <span class="darkso-heading__light darkso-swap__light">{{ light }}</span>
            <span class="darkso-heading__dark darkso-swap__dark">{{ dark }}</span>
        </div>
        <# if (hasLink) { #></a><# } #>
        <?php
    }
}
