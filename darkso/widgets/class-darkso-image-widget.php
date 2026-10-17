<?php

if (!defined('ABSPATH')) {
    exit;
}

class Darkso_Image_Widget extends \Elementor\Widget_Base
{
    public function get_name()
    {
        return 'darkso_image';
    }

    public function get_title()
    {
        return 'تصویر لایت / دارک';
    }

    public function get_icon()
    {
        return 'eicon-image-rollover';
    }

    public function get_categories()
    {
        return ['darkso', 'basic', 'general'];
    }

    public function get_keywords()
    {
        return ['image', 'dark', 'light', 'darkso', 'عکس'];
    }

    public function get_style_depends()
    {
        return ['darkso-widgets'];
    }

    protected function register_controls()
    {
        $this->start_controls_section('section_images', [
            'label' => 'تصاویر',
        ]);

        $this->add_control('image_light', [
            'label' => 'تصویر حالت روشن',
            'type' => \Elementor\Controls_Manager::MEDIA,
            'default' => [
                'url' => \Elementor\Utils::get_placeholder_image_src(),
            ],
        ]);

        $this->add_control('image_dark', [
            'label' => 'تصویر حالت تاریک',
            'type' => \Elementor\Controls_Manager::MEDIA,
            'default' => [
                'url' => \Elementor\Utils::get_placeholder_image_src(),
            ],
        ]);

        $this->add_group_control(\Elementor\Group_Control_Image_Size::get_type(), [
            'name' => 'image',
            'default' => 'full',
        ]);

        $this->add_control('alt', [
            'label' => 'متن جایگزین',
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => '',
            'dynamic' => ['active' => true],
        ]);

        $this->add_control('link', [
            'label' => 'لینک',
            'type' => \Elementor\Controls_Manager::URL,
            'placeholder' => 'https://',
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
                '{{WRAPPER}} .darkso-image' => 'text-align: {{VALUE}};',
            ],
        ]);

        $this->add_responsive_control('width', [
            'label' => 'عرض',
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['%', 'px', 'vw'],
            'range' => [
                '%' => ['min' => 1, 'max' => 100],
                'px' => ['min' => 1, 'max' => 1600],
            ],
            'default' => ['size' => 100, 'unit' => '%'],
            'selectors' => [
                '{{WRAPPER}} .darkso-image' => 'width: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .darkso-image img' => 'width: 100%;',
            ],
        ]);

        $this->add_responsive_control('max_height', [
            'label' => 'حداکثر ارتفاع',
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px', 'vh'],
            'range' => [
                'px' => ['min' => 1, 'max' => 1200],
            ],
            'selectors' => [
                '{{WRAPPER}} .darkso-image img' => 'max-height: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_control('object_fit', [
            'label' => 'برش تصویر',
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'unset',
            'options' => [
                'unset' => 'پیش‌فرض',
                'contain' => 'Contain',
                'cover' => 'Cover',
                'fill' => 'Fill',
                'none' => 'None',
            ],
            'selectors' => [
                '{{WRAPPER}} .darkso-image img' => 'object-fit: {{VALUE}};',
            ],
        ]);

        $this->add_control('radius', [
            'label' => 'گردی گوشه',
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .darkso-image, {{WRAPPER}} .darkso-image img' => 'border-radius: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_section();
    }

    private function image_html($image, $settings, $class, $alt)
    {
        if (!is_array($image) || (empty($image['id']) && empty($image['url']))) {
            return '';
        }

        $attrs = [
            'class' => $class,
            'alt' => $alt,
            'loading' => 'eager',
            'decoding' => 'async',
        ];

        $size = $settings['image_size'] ?? 'full';
        if ($size === 'custom') {
            $dim = $settings['image_custom_dimension'] ?? [];
            $w = isset($dim['width']) ? (int) $dim['width'] : 0;
            $h = isset($dim['height']) ? (int) $dim['height'] : 0;
            $size = [$w, $h];
        }

        $html = '';
        if (!empty($image['id'])) {
            $html = wp_get_attachment_image((int) $image['id'], $size, false, $attrs);
        }

        if (!$html && !empty($image['url'])) {
            $html = sprintf(
                '<img class="%s" src="%s" alt="%s" loading="eager" decoding="async">',
                esc_attr($class),
                esc_url($image['url']),
                esc_attr($alt)
            );
        }

        return $html;
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $alt = isset($settings['alt']) ? (string) $settings['alt'] : '';
        $light = $this->image_html(is_array($settings['image_light'] ?? null) ? $settings['image_light'] : [], $settings, 'darkso-image__item darkso-image__light', $alt);
        $dark = $this->image_html(is_array($settings['image_dark'] ?? null) ? $settings['image_dark'] : [], $settings, 'darkso-image__item darkso-image__dark', $alt);

        if ($light && !$dark) {
            $dark = $light;
            $dark = str_replace('darkso-image__light', 'darkso-image__dark', $dark);
        } elseif ($dark && !$light) {
            $light = str_replace('darkso-image__dark', 'darkso-image__light', $dark);
        }

        if (!$light && !$dark) {
            return;
        }

        $has_link = !empty($settings['link']['url']);
        $uid = $this->get_id();
        $wrap_key = 'darkso_img_wrap_' . $uid;
        $link_key = 'darkso_img_link_' . $uid;

        $this->add_render_attribute($wrap_key, 'class', 'darkso-image');

        if ($has_link && method_exists($this, 'add_link_attributes')) {
            $this->add_link_attributes($link_key, $settings['link']);
            $this->add_render_attribute($link_key, 'class', 'darkso-image');
            echo '<a ' . $this->get_render_attribute_string($link_key) . '>';
            echo $light . $dark;
            echo '</a>';
            return;
        }

        echo '<div ' . $this->get_render_attribute_string($wrap_key) . '>';
        echo $light . $dark;
        echo '</div>';
    }

    protected function content_template()
    {
        ?>
        <#
        var lightUrl = settings.image_light && settings.image_light.url ? settings.image_light.url : '';
        var darkUrl = settings.image_dark && settings.image_dark.url ? settings.image_dark.url : '';
        if (lightUrl && !darkUrl) darkUrl = lightUrl;
        if (darkUrl && !lightUrl) lightUrl = darkUrl;
        var alt = settings.alt || '';
        var hasLink = !!(settings.link && settings.link.url);
        #>
        <# if (hasLink) { #>
            <a class="darkso-image" href="{{ settings.link.url }}">
                <# if (lightUrl) { #><img class="darkso-image__item darkso-image__light" src="{{ lightUrl }}" alt="{{ alt }}"><# } #>
                <# if (darkUrl) { #><img class="darkso-image__item darkso-image__dark" src="{{ darkUrl }}" alt="{{ alt }}"><# } #>
            </a>
        <# } else { #>
            <div class="darkso-image">
                <# if (lightUrl) { #><img class="darkso-image__item darkso-image__light" src="{{ lightUrl }}" alt="{{ alt }}"><# } #>
                <# if (darkUrl) { #><img class="darkso-image__item darkso-image__dark" src="{{ darkUrl }}" alt="{{ alt }}"><# } #>
            </div>
        <# } #>
        <?php
    }
}
