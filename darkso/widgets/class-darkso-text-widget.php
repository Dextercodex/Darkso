<?php

if (!defined('ABSPATH')) {
    exit;
}

class Darkso_Text_Widget extends \Elementor\Widget_Base
{
    public function get_name()
    {
        return 'darkso_text';
    }

    public function get_title()
    {
        return 'متن لایت / دارک';
    }

    public function get_icon()
    {
        return 'eicon-text';
    }

    public function get_categories()
    {
        return ['darkso', 'basic', 'general'];
    }

    public function get_keywords()
    {
        return ['text', 'dark', 'light', 'darkso', 'متن'];
    }

    public function get_style_depends()
    {
        return ['darkso-widgets'];
    }

    protected function register_controls()
    {
        $this->start_controls_section('section_content', [
            'label' => 'محتوا',
        ]);

        $this->add_control('text_light', [
            'label' => 'متن حالت روشن',
            'type' => \Elementor\Controls_Manager::WYSIWYG,
            'default' => 'متن حالت روشن',
            'dynamic' => ['active' => true],
        ]);

        $this->add_control('text_dark', [
            'label' => 'متن حالت تاریک',
            'type' => \Elementor\Controls_Manager::WYSIWYG,
            'default' => 'متن حالت تاریک',
            'dynamic' => ['active' => true],
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
                '{{WRAPPER}} .darkso-text' => 'text-align: {{VALUE}};',
            ],
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name' => 'typography',
            'selector' => '{{WRAPPER}} .darkso-text',
        ]);

        $this->add_group_control(\Elementor\Group_Control_Text_Shadow::get_type(), [
            'name' => 'text_shadow',
            'selector' => '{{WRAPPER}} .darkso-text',
        ]);

        $this->start_controls_tabs('tabs_text_color');

        $this->start_controls_tab('tab_text_light', ['label' => 'روشن']);

        $this->add_control('color_light', [
            'label' => 'رنگ متن',
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .darkso-text__light' => 'color: {{VALUE}};',
            ],
        ]);

        $this->end_controls_tab();

        $this->start_controls_tab('tab_text_dark', ['label' => 'تاریک']);

        $this->add_control('color_dark', [
            'label' => 'رنگ متن',
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                'html.darkso-dark {{WRAPPER}} .darkso-text__dark' => 'color: {{VALUE}};',
            ],
        ]);

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $light = isset($settings['text_light']) ? (string) $settings['text_light'] : '';
        $dark = isset($settings['text_dark']) ? (string) $settings['text_dark'] : '';
        if ($light === '' && $dark === '') {
            return;
        }
        if ($dark === '') {
            $dark = $light;
        }
        if ($light === '') {
            $light = $dark;
        }
        ?>
        <div class="darkso-text darkso-swap darkso-swap--stack">
            <div class="darkso-text__light darkso-swap__light"><?php echo wp_kses_post($light); ?></div>
            <div class="darkso-text__dark darkso-swap__dark"><?php echo wp_kses_post($dark); ?></div>
        </div>
        <?php
    }

    protected function content_template()
    {
        ?>
        <#
        var light = settings.text_light || '';
        var dark = settings.text_dark || '';
        if (!dark) dark = light;
        if (!light) light = dark;
        #>
        <div class="darkso-text darkso-swap darkso-swap--stack">
            <div class="darkso-text__light darkso-swap__light">{{{ light }}}</div>
            <div class="darkso-text__dark darkso-swap__dark">{{{ dark }}}</div>
        </div>
        <?php
    }
}
