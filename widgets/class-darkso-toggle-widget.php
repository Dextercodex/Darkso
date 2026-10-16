<?php

if (!defined('ABSPATH')) {
    exit;
}

class Darkso_Toggle_Widget extends \Elementor\Widget_Base
{
    public function get_name()
    {
        return 'darkso_toggle';
    }

    public function get_title()
    {
        return 'سوییچ دارک‌مود';
    }

    public function get_icon()
    {
        return 'eicon-adjust';
    }

    public function get_categories()
    {
        return ['darkso', 'basic', 'general'];
    }

    public function get_keywords()
    {
        return ['dark', 'mode', 'night', 'toggle', 'darkso', 'دارک'];
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
            'default' => [
                'value' => 'fas fa-sun',
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

        $this->add_control('animation', [
            'label' => 'انیمیشن تبدیل',
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'rotate',
            'options' => [
                'fade' => 'محو شدن',
                'rotate' => 'چرخش',
                'flip' => 'چرخش سه‌بعدی',
                'scale' => 'اسکیل',
            ],
        ]);

        $this->add_control('aria_label', [
            'label' => 'متن دسترسی (مخفی)',
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => 'تغییر حالت روشن و تاریک',
        ]);

        $this->end_controls_section();

        $this->start_controls_section('section_button_style', [
            'label' => 'دکمه',
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_responsive_control('size', [
            'label' => 'اندازه دکمه',
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range' => [
                'px' => ['min' => 24, 'max' => 120],
            ],
            'default' => ['size' => 48, 'unit' => 'px'],
            'selectors' => [
                '{{WRAPPER}} .darkso-toggle' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('icon_size', [
            'label' => 'اندازه آیکن',
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px', 'em'],
            'range' => [
                'px' => ['min' => 10, 'max' => 80],
            ],
            'default' => ['size' => 20, 'unit' => 'px'],
            'selectors' => [
                '{{WRAPPER}} .darkso-toggle__icon' => 'font-size: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .darkso-toggle__icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_control('radius', [
            'label' => 'گردی گوشه',
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px', '%'],
            'range' => [
                'px' => ['min' => 0, 'max' => 100],
            ],
            'default' => ['size' => 50, 'unit' => '%'],
            'selectors' => [
                '{{WRAPPER}} .darkso-toggle' => 'border-radius: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_control('show_border', [
            'label' => 'استروک / حاشیه',
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'label_on' => 'نمایش',
            'label_off' => 'حذف',
            'return_value' => 'yes',
            'default' => '',
        ]);

        $this->add_group_control(\Elementor\Group_Control_Border::get_type(), [
            'name' => 'border',
            'selector' => '{{WRAPPER}} .darkso-toggle',
            'condition' => [
                'show_border' => 'yes',
            ],
        ]);

        $this->add_control('show_outline', [
            'label' => 'خط فوکوس',
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'label_on' => 'نمایش',
            'label_off' => 'حذف',
            'return_value' => 'yes',
            'default' => '',
        ]);

        $this->start_controls_tabs('tabs_colors');

        $this->start_controls_tab('tab_light', [
            'label' => 'حالت روشن',
        ]);

        $this->add_control('bg_light', [
            'label' => 'پس‌زمینه',
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .darkso-toggle' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('icon_color_light', [
            'label' => 'رنگ آیکن',
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '#111111',
            'selectors' => [
                '{{WRAPPER}} .darkso-toggle' => 'color: {{VALUE}};',
                '{{WRAPPER}} .darkso-toggle svg' => 'fill: {{VALUE}};',
            ],
        ]);

        $this->end_controls_tab();

        $this->start_controls_tab('tab_dark', [
            'label' => 'حالت تاریک',
        ]);

        $this->add_control('bg_dark', [
            'label' => 'پس‌زمینه',
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                'html.darkso-dark {{WRAPPER}} .darkso-toggle' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('icon_color_dark', [
            'label' => 'رنگ آیکن',
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '#f5f5f5',
            'selectors' => [
                'html.darkso-dark {{WRAPPER}} .darkso-toggle' => 'color: {{VALUE}};',
                'html.darkso-dark {{WRAPPER}} .darkso-toggle svg' => 'fill: {{VALUE}};',
            ],
        ]);

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_control('hover_heading', [
            'label' => 'هاور',
            'type' => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_control('bg_hover', [
            'label' => 'پس‌زمینه هاور',
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .darkso-toggle:hover' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_group_control(\Elementor\Group_Control_Box_Shadow::get_type(), [
            'name' => 'shadow',
            'selector' => '{{WRAPPER}} .darkso-toggle',
        ]);

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $animation = !empty($settings['animation']) ? $settings['animation'] : 'rotate';
        $label = !empty($settings['aria_label']) ? $settings['aria_label'] : 'Toggle dark mode';
        $classes = [
            'darkso-toggle',
            'darkbouttom',
            'darkso-toggle--' . $animation,
        ];
        if (($settings['show_outline'] ?? '') !== 'yes') {
            $classes[] = 'darkso-toggle--no-outline';
        }
        if (($settings['show_border'] ?? '') !== 'yes') {
            $classes[] = 'darkso-toggle--no-stroke';
        }
        ?>
        <button type="button" class="<?php echo esc_attr(implode(' ', $classes)); ?>" aria-label="<?php echo esc_attr($label); ?>">
            <span class="darkso-toggle__icon darkso-toggle__icon--light">
                <?php \Elementor\Icons_Manager::render_icon($settings['icon_light'], ['aria-hidden' => 'true']); ?>
            </span>
            <span class="darkso-toggle__icon darkso-toggle__icon--dark">
                <?php \Elementor\Icons_Manager::render_icon($settings['icon_dark'], ['aria-hidden' => 'true']); ?>
            </span>
        </button>
        <?php
    }

    protected function content_template()
    {
        ?>
        <#
        var lightIcon = elementor.helpers.renderIcon( view, settings.icon_light, { 'aria-hidden': true }, 'i' , 'object' );
        var darkIcon = elementor.helpers.renderIcon( view, settings.icon_dark, { 'aria-hidden': true }, 'i' , 'object' );
        var animation = settings.animation || 'rotate';
        var extra = '';
        if (settings.show_outline !== 'yes') extra += ' darkso-toggle--no-outline';
        if (settings.show_border !== 'yes') extra += ' darkso-toggle--no-stroke';
        #>
        <button type="button" class="darkso-toggle darkbouttom darkso-toggle--{{ animation }}{{ extra }}" aria-label="{{ settings.aria_label }}">
            <span class="darkso-toggle__icon darkso-toggle__icon--light">{{{ lightIcon && lightIcon.value }}}</span>
            <span class="darkso-toggle__icon darkso-toggle__icon--dark">{{{ darkIcon && darkIcon.value }}}</span>
        </button>
        <?php
    }
}
