<?php

if (!defined('ABSPATH')) {
    exit;
}

class Darkso_Menu_Widget extends \Elementor\Widget_Base
{
    public function get_name()
    {
        return 'darkso_menu';
    }

    public function get_title()
    {
        return 'منوی وردپرس لایت / دارک';
    }

    public function get_icon()
    {
        return 'eicon-nav-menu';
    }

    public function get_categories()
    {
        return ['darkso', 'basic', 'general'];
    }

    public function get_keywords()
    {
        return ['menu', 'nav', 'wordpress', 'darkso', 'منو'];
    }

    public function get_style_depends()
    {
        return ['darkso-widgets'];
    }

    public function is_reload_preview_required()
    {
        return true;
    }

    private function menu_options()
    {
        $options = ['0' => '— انتخاب منو —'];
        $menus = wp_get_nav_menus();
        if (is_array($menus)) {
            foreach ($menus as $menu) {
                $options[(string) $menu->term_id] = $menu->name;
            }
        }
        return $options;
    }

    protected function register_controls()
    {
        $this->start_controls_section('section_menu', [
            'label' => 'منو',
        ]);

        $this->add_control('menu_light', [
            'label' => 'منوی حالت روشن',
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => $this->menu_options(),
            'default' => '0',
        ]);

        $this->add_control('menu_dark', [
            'label' => 'منوی حالت تاریک',
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => $this->menu_options(),
            'default' => '0',
        ]);

        $this->add_control('layout', [
            'label' => 'چینش منو',
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'horizontal',
            'options' => [
                'horizontal' => 'افقی',
                'vertical' => 'عمودی',
            ],
        ]);

        $this->add_control('pointer', [
            'label' => 'نشانگر',
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'underline',
            'options' => [
                'none' => 'هیچ',
                'underline' => 'زیرخط',
                'overline' => 'خط بالا',
                'double-line' => 'دو خط',
                'framed' => 'قاب',
                'background' => 'پس‌زمینه',
                'text' => 'متن',
            ],
        ]);

        $this->end_controls_section();

        $this->start_controls_section('section_style_main', [
            'label' => 'منوی اصلی',
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_responsive_control('align', [
            'label' => 'تراز',
            'type' => \Elementor\Controls_Manager::CHOOSE,
            'options' => [
                'flex-end' => ['title' => 'راست', 'icon' => 'eicon-h-align-right'],
                'center' => ['title' => 'وسط', 'icon' => 'eicon-h-align-center'],
                'flex-start' => ['title' => 'چپ', 'icon' => 'eicon-h-align-left'],
                'space-between' => ['title' => 'عرض کامل', 'icon' => 'eicon-h-align-stretch'],
            ],
            'selectors' => [
                '{{WRAPPER}} .darkso-menu__list' => 'justify-content: {{VALUE}};',
            ],
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name' => 'typography',
            'selector' => '{{WRAPPER}} .darkso-menu a',
        ]);

        $this->add_responsive_control('gap', [
            'label' => 'فاصله آیتم‌ها',
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range' => [
                'px' => ['min' => 0, 'max' => 80],
            ],
            'default' => ['size' => 24, 'unit' => 'px'],
            'selectors' => [
                '{{WRAPPER}} .darkso-menu__list' => 'gap: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('item_padding', [
            'label' => 'پدینگ آیتم',
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em'],
            'selectors' => [
                '{{WRAPPER}} .darkso-menu__list > li > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->start_controls_tabs('tabs_menu_style');

        $this->start_controls_tab('tab_menu_light', ['label' => 'روشن']);

        $this->add_control('color_light', [
            'label' => 'رنگ متن',
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .darkso-menu__light a' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('hover_light', [
            'label' => 'رنگ هاور',
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .darkso-menu__light a:hover' => 'color: {{VALUE}};',
                '{{WRAPPER}} .darkso-menu__light .current-menu-item > a' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('pointer_light', [
            'label' => 'رنگ نشانگر',
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .darkso-menu__light.darkso-menu--pointer-underline .darkso-menu__list > li > a:after' => 'background-color: {{VALUE}};',
                '{{WRAPPER}} .darkso-menu__light.darkso-menu--pointer-overline .darkso-menu__list > li > a:before' => 'background-color: {{VALUE}};',
                '{{WRAPPER}} .darkso-menu__light.darkso-menu--pointer-double-line .darkso-menu__list > li > a:before, {{WRAPPER}} .darkso-menu__light.darkso-menu--pointer-double-line .darkso-menu__list > li > a:after' => 'background-color: {{VALUE}};',
                '{{WRAPPER}} .darkso-menu__light.darkso-menu--pointer-framed .darkso-menu__list > li > a' => 'border-color: {{VALUE}};',
                '{{WRAPPER}} .darkso-menu__light.darkso-menu--pointer-background .darkso-menu__list > li > a:hover, {{WRAPPER}} .darkso-menu__light.darkso-menu--pointer-background .current-menu-item > a' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->end_controls_tab();

        $this->start_controls_tab('tab_menu_dark', ['label' => 'تاریک']);

        $this->add_control('color_dark', [
            'label' => 'رنگ متن',
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                'html.darkso-dark {{WRAPPER}} .darkso-menu__dark a' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('hover_dark', [
            'label' => 'رنگ هاور',
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                'html.darkso-dark {{WRAPPER}} .darkso-menu__dark a:hover' => 'color: {{VALUE}};',
                'html.darkso-dark {{WRAPPER}} .darkso-menu__dark .current-menu-item > a' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('pointer_dark', [
            'label' => 'رنگ نشانگر',
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                'html.darkso-dark {{WRAPPER}} .darkso-menu__dark.darkso-menu--pointer-underline .darkso-menu__list > li > a:after' => 'background-color: {{VALUE}};',
                'html.darkso-dark {{WRAPPER}} .darkso-menu__dark.darkso-menu--pointer-overline .darkso-menu__list > li > a:before' => 'background-color: {{VALUE}};',
                'html.darkso-dark {{WRAPPER}} .darkso-menu__dark.darkso-menu--pointer-double-line .darkso-menu__list > li > a:before, html.darkso-dark {{WRAPPER}} .darkso-menu__dark.darkso-menu--pointer-double-line .darkso-menu__list > li > a:after' => 'background-color: {{VALUE}};',
                'html.darkso-dark {{WRAPPER}} .darkso-menu__dark.darkso-menu--pointer-framed .darkso-menu__list > li > a' => 'border-color: {{VALUE}};',
                'html.darkso-dark {{WRAPPER}} .darkso-menu__dark.darkso-menu--pointer-background .darkso-menu__list > li > a:hover, html.darkso-dark {{WRAPPER}} .darkso-menu__dark.darkso-menu--pointer-background .current-menu-item > a' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section('section_style_submenu', [
            'label' => 'زیرمنو',
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_responsive_control('submenu_width', [
            'label' => 'عرض',
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range' => [
                'px' => ['min' => 160, 'max' => 400],
            ],
            'default' => ['size' => 220, 'unit' => 'px'],
            'selectors' => [
                '{{WRAPPER}} .darkso-menu .sub-menu' => 'min-width: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name' => 'submenu_typography',
            'selector' => '{{WRAPPER}} .darkso-menu .sub-menu a',
        ]);

        $this->add_control('submenu_bg_light', [
            'label' => 'پس‌زمینه روشن',
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '#ffffff',
            'selectors' => [
                '{{WRAPPER}} .darkso-menu__light .sub-menu' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('submenu_bg_dark', [
            'label' => 'پس‌زمینه تاریک',
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '#1a1a1a',
            'selectors' => [
                'html.darkso-dark {{WRAPPER}} .darkso-menu__dark .sub-menu' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('submenu_color_light', [
            'label' => 'رنگ متن روشن',
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .darkso-menu__light .sub-menu a' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('submenu_color_dark', [
            'label' => 'رنگ متن تاریک',
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                'html.darkso-dark {{WRAPPER}} .darkso-menu__dark .sub-menu a' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_group_control(\Elementor\Group_Control_Box_Shadow::get_type(), [
            'name' => 'submenu_shadow',
            'selector' => '{{WRAPPER}} .darkso-menu .sub-menu',
        ]);

        $this->add_control('submenu_radius', [
            'label' => 'گردی گوشه',
            'type' => \Elementor\Controls_Manager::SLIDER,
            'selectors' => [
                '{{WRAPPER}} .darkso-menu .sub-menu' => 'border-radius: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_section();
    }

    private function render_menu($menu_id, $extra_class)
    {
        $menu_id = (int) $menu_id;
        if ($menu_id <= 0) {
            return '';
        }

        $html = wp_nav_menu([
            'menu' => $menu_id,
            'container' => false,
            'menu_class' => 'darkso-menu__list',
            'echo' => false,
            'fallback_cb' => false,
            'depth' => 3,
        ]);

        if (!$html) {
            return '';
        }

        return '<nav class="darkso-menu ' . esc_attr($extra_class) . '" aria-label="menu">' . $html . '</nav>';
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $light_id = (int) ($settings['menu_light'] ?? 0);
        $dark_id = (int) ($settings['menu_dark'] ?? 0);

        $is_edit = class_exists('\Elementor\Plugin')
            && isset(\Elementor\Plugin::$instance->editor)
            && \Elementor\Plugin::$instance->editor
            && \Elementor\Plugin::$instance->editor->is_edit_mode();

        if ($light_id <= 0 && $dark_id <= 0) {
            if ($is_edit) {
                echo '<div class="darkso-menu-empty">یک منو برای حالت روشن و تاریک انتخاب کنید. منوها را از نمایش → فهرست‌ها بسازید.</div>';
            }
            return;
        }

        if ($dark_id <= 0) {
            $dark_id = $light_id;
        }
        if ($light_id <= 0) {
            $light_id = $dark_id;
        }

        $layout = ($settings['layout'] ?? 'horizontal') === 'vertical' ? 'vertical' : 'horizontal';
        $pointer = sanitize_html_class($settings['pointer'] ?? 'underline');
        $mods = 'darkso-menu--' . $layout . ' darkso-menu--pointer-' . $pointer;

        $light = $this->render_menu($light_id, 'darkso-menu__light darkso-swap__light ' . $mods);
        $dark = $this->render_menu($dark_id, 'darkso-menu__dark darkso-swap__dark ' . $mods);

        if (!$light && !$dark) {
            if ($is_edit) {
                echo '<div class="darkso-menu-empty">منوی انتخاب‌شده خالی است یا وجود ندارد.</div>';
            }
            return;
        }

        echo '<div class="darkso-menu-swap darkso-swap darkso-swap--stack">';
        echo $light;
        echo $dark;
        echo '</div>';
    }
}
