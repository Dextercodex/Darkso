<?php

if (!defined('ABSPATH')) {
    exit;
}

class Darkso_Portfolio_Widget extends \Elementor\Widget_Base
{
    public function get_name()
    {
        return 'darkso_portfolio';
    }

    public function get_title()
    {
        return 'پورتفولیو لایت / دارک';
    }

    public function get_icon()
    {
        return 'eicon-gallery-justified';
    }

    public function get_categories()
    {
        return ['darkso', 'basic', 'general'];
    }

    public function get_keywords()
    {
        return ['portfolio', 'gallery', 'filter', 'projects', 'dark', 'light', 'darkso', 'پورتفولیو', 'نمونه کار'];
    }

    public function get_style_depends()
    {
        return ['darkso-widgets'];
    }

    public function get_script_depends()
    {
        return ['darkso-widgets'];
    }

    protected function register_controls()
    {
        $default_type = post_type_exists('portfolio') ? 'portfolio' : 'post';
        $default_tax = taxonomy_exists('portfolio_category') ? 'portfolio_category' : 'category';

        Darkso_Helpers::add_query_section($this, [
            'per_page' => 12,
            'pagination' => false,
            'post_type' => $default_type,
        ]);

        $this->start_controls_section('section_layout', ['label' => 'چیدمان و فیلتر']);

        $this->add_control('layout', [
            'label' => 'چیدمان',
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'grid',
            'options' => [
                'grid' => 'گرید یکدست',
                'masonry' => 'ماسونری',
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
            'selectors' => [
                '{{WRAPPER}} .darkso-portfolio' => '--darkso-cols: {{VALUE}};',
            ],
        ]);

        $this->add_responsive_control('gap', [
            'label' => 'فاصله آیتم‌ها',
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range' => ['px' => ['min' => 0, 'max' => 60]],
            'default' => ['size' => 20, 'unit' => 'px'],
            'selectors' => [
                '{{WRAPPER}} .darkso-portfolio' => '--darkso-gap: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('ratio', [
            'label' => 'نسبت تصویر',
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => '1 / 1',
            'options' => [
                '1 / 1' => '1:1',
                '4 / 3' => '4:3',
                '3 / 4' => '3:4',
                '16 / 9' => '16:9',
                '3 / 2' => '3:2',
            ],
            'condition' => ['layout' => 'grid'],
            'selectors' => [
                '{{WRAPPER}} .darkso-portfolio--grid .darkso-portfolio__thumb' => 'aspect-ratio: {{VALUE}};',
            ],
        ]);

        $this->add_control('show_filter', [
            'label' => 'نمایش فیلتر',
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => 'yes',
            'separator' => 'before',
        ]);

        $this->add_control('filter_taxonomy', [
            'label' => 'فیلتر بر اساس',
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => Darkso_Helpers::taxonomy_options(),
            'default' => $default_tax,
            'condition' => ['show_filter' => 'yes'],
        ]);

        $this->add_control('filter_all_text', [
            'label' => 'متن دکمه «همه»',
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => 'همه',
            'condition' => ['show_filter' => 'yes'],
        ]);

        $this->add_control('link_mode', [
            'label' => 'با کلیک',
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'post',
            'separator' => 'before',
            'options' => [
                'post' => 'رفتن به صفحه نوشته',
                'lightbox' => 'باز شدن تصویر بزرگ (لایت‌باکس)',
                'none' => 'بدون لینک',
            ],
        ]);

        $this->add_control('hover_fx', [
            'label' => 'افکت هاور',
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'zoom',
            'options' => [
                'zoom' => 'زوم + نمایش متن',
                'fade' => 'فقط نمایش متن',
                'always' => 'متن همیشه نمایان',
            ],
        ]);

        $this->add_control('show_title', [
            'label' => 'عنوان',
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => 'yes',
            'separator' => 'before',
        ]);

        $this->add_control('show_term', [
            'label' => 'دسته‌بندی',
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => 'yes',
        ]);

        $this->add_control('thumb_size', [
            'label' => 'سایز تصویر',
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => Darkso_Helpers::image_size_options(),
            'default' => 'large',
        ]);

        $this->end_controls_section();

        $this->start_controls_section('section_portfolio_style', [
            'label' => 'استایل',
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('item_radius', [
            'label' => 'گردی گوشه تصاویر',
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px', '%'],
            'range' => ['px' => ['min' => 0, 'max' => 60]],
            'selectors' => [
                '{{WRAPPER}} .darkso-portfolio__item' => 'border-radius: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name' => 'pf_title_typo',
            'label' => 'تایپوگرافی عنوان',
            'selector' => '{{WRAPPER}} .darkso-portfolio__title',
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name' => 'pf_filter_typo',
            'label' => 'تایپوگرافی فیلتر',
            'selector' => '{{WRAPPER}} .darkso-portfolio__filter',
        ]);

        $this->add_responsive_control('filter_align', [
            'label' => 'چینش فیلتر',
            'type' => \Elementor\Controls_Manager::CHOOSE,
            'options' => [
                'flex-start' => ['title' => 'ابتدا', 'icon' => 'eicon-h-align-right'],
                'center' => ['title' => 'وسط', 'icon' => 'eicon-h-align-center'],
                'flex-end' => ['title' => 'انتها', 'icon' => 'eicon-h-align-left'],
            ],
            'selectors' => [
                '{{WRAPPER}} .darkso-portfolio__filters' => 'justify-content: {{VALUE}};',
            ],
        ]);

        Darkso_Helpers::style_tabs($this, 'tabs_portfolio_colors', [
            'pf_overlay' => ['label' => 'پس‌زمینه روی تصویر', 'sel' => '.darkso-portfolio__overlay', 'prop' => 'background'],
            'pf_title' => ['label' => 'رنگ عنوان', 'sel' => '.darkso-portfolio__title', 'prop' => 'color'],
            'pf_term' => ['label' => 'رنگ دسته', 'sel' => '.darkso-portfolio__terms', 'prop' => 'color'],
            'pf_filter' => ['label' => 'رنگ دکمه فیلتر', 'sel' => '.darkso-portfolio__filter', 'prop' => 'color'],
            'pf_filter_border' => ['label' => 'کادر دکمه فیلتر', 'sel' => '.darkso-portfolio__filter', 'prop' => 'border-color'],
            'pf_filter_active' => ['label' => 'متن فیلتر فعال', 'sel' => '.darkso-portfolio__filter.is-active', 'prop' => 'color'],
            'pf_filter_active_bg' => ['label' => 'پس‌زمینه فیلتر فعال', 'sel' => '.darkso-portfolio__filter.is-active', 'prop' => 'background-color'],
        ]);

        $this->end_controls_section();
    }

    protected function render()
    {
        $s = $this->get_settings_for_display();

        $args = Darkso_Helpers::query_args($s, 1);
        $query = new \WP_Query($args);

        if (!$query->have_posts()) {
            wp_reset_postdata();
            if (Darkso_Helpers::is_edit()) {
                echo '<div class="darkso-menu-empty">هیچ نمونه‌کاری با این تنظیمات پیدا نشد.</div>';
            }
            return;
        }

        $layout = ($s['layout'] ?? 'grid') === 'masonry' ? 'masonry' : 'grid';
        $fx = in_array(($s['hover_fx'] ?? 'zoom'), ['zoom', 'fade', 'always'], true) ? $s['hover_fx'] : 'zoom';
        $mode = in_array(($s['link_mode'] ?? 'post'), ['post', 'lightbox', 'none'], true) ? $s['link_mode'] : 'post';
        $size = !empty($s['thumb_size']) ? $s['thumb_size'] : 'large';
        $tax = !empty($s['filter_taxonomy']) && taxonomy_exists($s['filter_taxonomy']) ? $s['filter_taxonomy'] : '';
        $show_filter = ($s['show_filter'] ?? 'yes') === 'yes' && $tax !== '';
        $show_title = ($s['show_title'] ?? 'yes') === 'yes';
        $show_term = ($s['show_term'] ?? 'yes') === 'yes';

        $items = '';
        $filters = [];

        while ($query->have_posts()) {
            $query->the_post();
            $id = get_the_ID();
            $title = get_the_title($id);

            $slugs = [];
            $names = [];
            if ($tax !== '') {
                $terms = get_the_terms($id, $tax);
                if (is_array($terms)) {
                    foreach ($terms as $term) {
                        $slugs[] = sanitize_html_class($term->slug !== '' ? $term->slug : ('t' . $term->term_id));
                        $names[] = $term->name;
                        $filters[$term->slug !== '' ? sanitize_html_class($term->slug) : ('t' . $term->term_id)] = $term->name;
                    }
                }
            }

            $img = has_post_thumbnail($id)
                ? get_the_post_thumbnail($id, $size, ['loading' => 'lazy', 'class' => 'darkso-portfolio__img', 'alt' => esc_attr($title)])
                : '<span class="darkso-portfolio__noimg"></span>';

            $overlay = '';
            if ($show_title || ($show_term && $names)) {
                $overlay .= '<span class="darkso-portfolio__overlay">';
                if ($show_term && $names) {
                    $overlay .= '<span class="darkso-portfolio__terms">' . esc_html(implode('، ', $names)) . '</span>';
                }
                if ($show_title) {
                    $overlay .= '<span class="darkso-portfolio__title">' . esc_html($title) . '</span>';
                }
                $overlay .= '</span>';
            } else {
                $overlay = '<span class="darkso-portfolio__overlay darkso-portfolio__overlay--empty"></span>';
            }

            $inner = '<span class="darkso-portfolio__thumb">' . $img . '</span>' . $overlay;

            if ($mode === 'lightbox' && has_post_thumbnail($id)) {
                $full = wp_get_attachment_image_url((int) get_post_thumbnail_id($id), 'full');
                $link = '<a class="darkso-portfolio__link" href="' . esc_url($full) . '" data-darkso-lightbox data-full="' . esc_url($full) . '" data-title="' . esc_attr($title) . '">' . $inner . '</a>';
            } elseif ($mode === 'post') {
                $link = '<a class="darkso-portfolio__link" href="' . esc_url(get_permalink($id)) . '">' . $inner . '</a>';
            } else {
                $link = '<span class="darkso-portfolio__link">' . $inner . '</span>';
            }

            $items .= '<article class="darkso-portfolio__item" data-cats="' . esc_attr(implode(' ', array_unique($slugs))) . '">' . $link . '</article>';
        }
        wp_reset_postdata();

        echo '<div class="darkso-portfolio darkso-portfolio--' . esc_attr($layout) . ' darkso-portfolio--fx-' . esc_attr($fx) . '" data-darkso-portfolio>';

        if ($show_filter && $filters) {
            $all_text = !empty($s['filter_all_text']) ? $s['filter_all_text'] : 'همه';
            echo '<div class="darkso-portfolio__filters" role="tablist">';
            echo '<button type="button" class="darkso-portfolio__filter is-active" data-filter="*">' . esc_html($all_text) . '</button>';
            foreach ($filters as $slug => $name) {
                echo '<button type="button" class="darkso-portfolio__filter" data-filter="' . esc_attr($slug) . '">' . esc_html($name) . '</button>';
            }
            echo '</div>';
        }

        echo '<div class="darkso-portfolio__grid">';
        echo $items; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo '</div>';
        echo '</div>';
    }
}
