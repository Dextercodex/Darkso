<?php

if (!defined('ABSPATH')) {
    exit;
}

class Darkso_Iconbox_Widget extends \Elementor\Widget_Base
{
    public function get_name()
    {
        return 'darkso_iconbox';
    }

    public function get_title()
    {
        return 'باکس آیکن لایت / دارک';
    }

    public function get_icon()
    {
        return 'eicon-icon-box';
    }

    public function get_categories()
    {
        return ['darkso', 'basic', 'general'];
    }

    public function get_keywords()
    {
        return ['icon', 'box', 'feature', 'dark', 'light', 'darkso', 'باکس', 'آیکن'];
    }

    public function get_style_depends()
    {
        return ['darkso-widgets'];
    }

    protected function register_controls()
    {
        $this->start_controls_section('section_content', ['label' => 'محتوا']);

        $this->add_control('icon_light', [
            'label' => 'آیکن حالت روشن',
            'type' => \Elementor\Controls_Manager::ICONS,
            'default' => [
                'value' => 'fas fa-bolt',
                'library' => 'fa-solid',
            ],
        ]);

        $this->add_control('icon_dark', [
            'label' => 'آیکن حالت تاریک (اختیاری)',
            'type' => \Elementor\Controls_Manager::ICONS,
            'description' => 'اگر خالی بماند، همان آیکن حالت روشن استفاده می‌شود.',
        ]);

        $this->add_control('title', [
            'label' => 'عنوان',
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => 'عنوان باکس',
            'label_block' => true,
            'dynamic' => ['active' => true],
        ]);

        $this->add_control('title_tag', [
            'label' => 'تگ عنوان',
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => [
                'h2' => 'H2',
                'h3' => 'H3',
                'h4' => 'H4',
                'h5' => 'H5',
                'h6' => 'H6',
                'div' => 'div',
            ],
            'default' => 'h3',
        ]);

        $this->add_control('description', [
            'label' => 'توضیحات',
            'type' => \Elementor\Controls_Manager::TEXTAREA,
            'default' => 'توضیح کوتاه درباره‌ی این ویژگی را اینجا بنویسید.',
            'rows' => 4,
            'dynamic' => ['active' => true],
        ]);

        $this->add_control('link', [
            'label' => 'لینک',
            'type' => \Elementor\Controls_Manager::URL,
            'dynamic' => ['active' => true],
            'placeholder' => 'https://',
        ]);

        $this->add_control('button_text', [
            'label' => 'متن دکمه (اختیاری)',
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => '',
            'placeholder' => 'بیشتر بدانید',
        ]);

        $this->add_control('position', [
            'label' => 'جایگاه آیکن',
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'top',
            'options' => [
                'top' => 'بالا',
                'start' => 'ابتدا (راست در فارسی)',
                'end' => 'انتها (چپ در فارسی)',
            ],
        ]);

        $this->end_controls_section();

        $this->start_controls_section('section_box_style', [
            'label' => 'باکس',
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_responsive_control('align', [
            'label' => 'چینش',
            'type' => \Elementor\Controls_Manager::CHOOSE,
            'options' => [
                'start' => ['title' => 'ابتدا', 'icon' => 'eicon-text-align-right'],
                'center' => ['title' => 'وسط', 'icon' => 'eicon-text-align-center'],
                'end' => ['title' => 'انتها', 'icon' => 'eicon-text-align-left'],
            ],
            'selectors' => [
                '{{WRAPPER}} .darkso-iconbox' => 'text-align: {{VALUE}};',
                '{{WRAPPER}} .darkso-iconbox--top' => 'align-items: {{VALUE}};',
            ],
        ]);

        $this->add_responsive_control('box_padding', [
            'label' => 'پدینگ',
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em'],
            'selectors' => [
                '{{WRAPPER}} .darkso-iconbox' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('box_gap', [
            'label' => 'فاصله آیکن تا متن',
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range' => ['px' => ['min' => 0, 'max' => 60]],
            'default' => ['size' => 18, 'unit' => 'px'],
            'selectors' => [
                '{{WRAPPER}} .darkso-iconbox' => 'gap: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_control('box_radius', [
            'label' => 'گردی گوشه',
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px', '%'],
            'range' => ['px' => ['min' => 0, 'max' => 60]],
            'selectors' => [
                '{{WRAPPER}} .darkso-iconbox' => 'border-radius: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_group_control(\Elementor\Group_Control_Border::get_type(), [
            'name' => 'box_border',
            'selector' => '{{WRAPPER}} .darkso-iconbox',
        ]);

        $this->add_group_control(\Elementor\Group_Control_Box_Shadow::get_type(), [
            'name' => 'box_shadow',
            'selector' => '{{WRAPPER}} .darkso-iconbox',
        ]);

        $this->add_control('hover_lift', [
            'label' => 'حرکت به بالا با هاور',
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => 'yes',
        ]);

        $this->end_controls_section();

        $this->start_controls_section('section_icon_style', [
            'label' => 'آیکن',
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_responsive_control('icon_size', [
            'label' => 'اندازه آیکن',
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range' => ['px' => ['min' => 12, 'max' => 120]],
            'default' => ['size' => 28, 'unit' => 'px'],
            'selectors' => [
                '{{WRAPPER}} .darkso-iconbox__icon' => 'font-size: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('icon_box_size', [
            'label' => 'اندازه قاب آیکن',
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range' => ['px' => ['min' => 24, 'max' => 200]],
            'default' => ['size' => 64, 'unit' => 'px'],
            'selectors' => [
                '{{WRAPPER}} .darkso-iconbox__icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_control('icon_radius', [
            'label' => 'گردی قاب آیکن',
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px', '%'],
            'range' => ['px' => ['min' => 0, 'max' => 100]],
            'default' => ['size' => 16, 'unit' => 'px'],
            'selectors' => [
                '{{WRAPPER}} .darkso-iconbox__icon' => 'border-radius: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_section();

        $this->start_controls_section('section_text_style', [
            'label' => 'متن',
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name' => 'title_typo',
            'label' => 'تایپوگرافی عنوان',
            'selector' => '{{WRAPPER}} .darkso-iconbox__title',
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name' => 'desc_typo',
            'label' => 'تایپوگرافی توضیحات',
            'selector' => '{{WRAPPER}} .darkso-iconbox__desc',
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name' => 'btn_typo',
            'label' => 'تایپوگرافی دکمه',
            'selector' => '{{WRAPPER}} .darkso-iconbox__btn',
        ]);

        Darkso_Helpers::style_tabs($this, 'tabs_iconbox_colors', [
            'ib_bg' => ['label' => 'پس‌زمینه باکس', 'sel' => '.darkso-iconbox', 'prop' => 'background-color'],
            'ib_border' => ['label' => 'رنگ کادر', 'sel' => '.darkso-iconbox', 'prop' => 'border-color'],
            'ib_border_hover' => ['label' => 'رنگ کادر هاور', 'sel' => '.darkso-iconbox:hover', 'prop' => 'border-color'],
            'ib_icon' => ['label' => 'رنگ آیکن', 'sel' => ['.darkso-iconbox__icon', '.darkso-iconbox__icon svg', '.darkso-iconbox__icon i'], 'prop' => 'color'],
            'ib_icon_bg' => ['label' => 'پس‌زمینه آیکن', 'sel' => '.darkso-iconbox__icon', 'prop' => 'background-color'],
            'ib_title' => ['label' => 'رنگ عنوان', 'sel' => '.darkso-iconbox__title', 'prop' => 'color'],
            'ib_desc' => ['label' => 'رنگ توضیحات', 'sel' => '.darkso-iconbox__desc', 'prop' => 'color'],
            'ib_btn' => ['label' => 'رنگ دکمه', 'sel' => '.darkso-iconbox__btn', 'prop' => 'color'],
        ]);

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
        \Elementor\Icons_Manager::render_icon($icon, ['aria-hidden' => 'true']);
    }

    protected function render()
    {
        $s = $this->get_settings_for_display();

        $light = is_array($s['icon_light'] ?? null) ? $s['icon_light'] : [];
        $dark = is_array($s['icon_dark'] ?? null) ? $s['icon_dark'] : [];
        if (!$this->has_icon($dark)) {
            $dark = $light;
        }
        if (!$this->has_icon($light)) {
            $light = $dark;
        }

        $position = in_array(($s['position'] ?? 'top'), ['top', 'start', 'end'], true) ? $s['position'] : 'top';
        $tag = in_array(($s['title_tag'] ?? 'h3'), ['h2', 'h3', 'h4', 'h5', 'h6', 'div'], true) ? $s['title_tag'] : 'h3';
        $has_link = !empty($s['link']['url']);
        $btn_text = isset($s['button_text']) ? trim((string) $s['button_text']) : '';
        $uid = $this->get_id();

        $class = 'darkso-iconbox darkso-iconbox--' . $position;
        if (($s['hover_lift'] ?? 'yes') === 'yes') {
            $class .= ' darkso-iconbox--lift';
        }

        $link_key = 'darkso_ib_link_' . $uid;
        $wrapper_open = '<div class="' . esc_attr($class) . '">';
        $wrapper_close = '</div>';

        // اگر دکمه ندارد، کل باکس لینک می‌شود.
        if ($has_link && $btn_text === '' && method_exists($this, 'add_link_attributes')) {
            $this->add_link_attributes($link_key, $s['link']);
            $this->add_render_attribute($link_key, 'class', $class . ' darkso-iconbox--link');
            $wrapper_open = '<a ' . $this->get_render_attribute_string($link_key) . '>';
            $wrapper_close = '</a>';
        }

        echo $wrapper_open; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

        if ($this->has_icon($light) || $this->has_icon($dark)) {
            echo '<span class="darkso-iconbox__icon darkso-swap darkso-swap--overlay">';
            echo '<span class="darkso-swap__light">';
            $this->print_icon($light);
            echo '</span>';
            echo '<span class="darkso-swap__dark">';
            $this->print_icon($dark);
            echo '</span>';
            echo '</span>';
        }

        echo '<div class="darkso-iconbox__content">';

        if (!empty($s['title'])) {
            echo '<' . $tag . ' class="darkso-iconbox__title">' . esc_html($s['title']) . '</' . $tag . '>';
        }

        if (!empty($s['description'])) {
            echo '<p class="darkso-iconbox__desc">' . esc_html($s['description']) . '</p>';
        }

        if ($has_link && $btn_text !== '') {
            $btn_key = 'darkso_ib_btn_' . $uid;
            if (method_exists($this, 'add_link_attributes')) {
                $this->add_link_attributes($btn_key, $s['link']);
            }
            $this->add_render_attribute($btn_key, 'class', 'darkso-iconbox__btn');
            echo '<a ' . $this->get_render_attribute_string($btn_key) . '>' . esc_html($btn_text) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }

        echo '</div>';
        echo $wrapper_close; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
}
