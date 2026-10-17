<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * توابع مشترک ویجت‌های پست، لوپ، پورتفولیو.
 */
class Darkso_Helpers
{
    private static $rendering = [];

    public static function is_edit()
    {
        return class_exists('\Elementor\Plugin')
            && isset(\Elementor\Plugin::$instance->editor)
            && \Elementor\Plugin::$instance->editor
            && \Elementor\Plugin::$instance->editor->is_edit_mode();
    }

    public static function post_type_options()
    {
        $out = [];
        foreach (get_post_types(['public' => true], 'objects') as $slug => $obj) {
            if ($slug === 'attachment') {
                continue;
            }
            $out[$slug] = !empty($obj->labels->singular_name) ? $obj->labels->singular_name : $slug;
        }
        return $out;
    }

    public static function taxonomy_options()
    {
        $out = ['' => '— بدون —'];
        foreach (get_taxonomies(['public' => true], 'objects') as $slug => $obj) {
            if ($slug === 'post_format') {
                continue;
            }
            $out[$slug] = $obj->labels->name . ' (' . $slug . ')';
        }
        return $out;
    }

    public static function term_options()
    {
        $out = [];
        $taxes = array_values(array_diff(get_taxonomies(['public' => true]), ['post_format']));
        if (!$taxes) {
            return $out;
        }
        $terms = get_terms([
            'taxonomy' => $taxes,
            'hide_empty' => false,
            'number' => 300,
        ]);
        if (is_wp_error($terms) || !is_array($terms)) {
            return $out;
        }
        foreach ($terms as $term) {
            $out[(string) $term->term_id] = $term->name . ' — ' . $term->taxonomy;
        }
        return $out;
    }

    public static function template_options()
    {
        $out = ['0' => '— کارت پیش‌فرض دارکسو —'];
        $posts = get_posts([
            'post_type' => 'elementor_library',
            'post_status' => 'publish',
            'posts_per_page' => 100,
            'orderby' => 'title',
            'order' => 'ASC',
            'no_found_rows' => true,
        ]);
        foreach ($posts as $p) {
            $out[(string) $p->ID] = $p->post_title !== '' ? $p->post_title : ('#' . $p->ID);
        }
        return $out;
    }

    public static function image_size_options()
    {
        $out = ['full' => 'full'];
        foreach (get_intermediate_image_sizes() as $size) {
            $out[$size] = $size;
        }
        return $out;
    }

    /* ------------------------------------------------------------------ *
     * کنترل‌ها
     * ------------------------------------------------------------------ */

    public static function add_query_section($w, $args = [])
    {
        $a = wp_parse_args($args, [
            'per_page' => 6,
            'pagination' => false,
            'post_type' => 'post',
        ]);

        $w->start_controls_section('section_query', ['label' => 'کوئری']);

        $w->add_control('post_type', [
            'label' => 'نوع نوشته',
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => self::post_type_options(),
            'default' => $a['post_type'],
        ]);

        $w->add_control('terms', [
            'label' => 'فقط این دسته‌ها / برچسب‌ها',
            'type' => \Elementor\Controls_Manager::SELECT2,
            'options' => self::term_options(),
            'multiple' => true,
            'label_block' => true,
        ]);

        $w->add_control('posts_per_page', [
            'label' => 'تعداد آیتم',
            'type' => \Elementor\Controls_Manager::NUMBER,
            'default' => $a['per_page'],
            'min' => 1,
            'max' => 60,
        ]);

        $w->add_control('offset', [
            'label' => 'رد کردن چند آیتم اول',
            'type' => \Elementor\Controls_Manager::NUMBER,
            'default' => 0,
            'min' => 0,
        ]);

        $w->add_control('orderby', [
            'label' => 'مرتب‌سازی بر اساس',
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'date',
            'options' => [
                'date' => 'تاریخ انتشار',
                'modified' => 'آخرین ویرایش',
                'title' => 'عنوان',
                'menu_order' => 'ترتیب دستی',
                'comment_count' => 'تعداد دیدگاه',
                'rand' => 'تصادفی',
            ],
        ]);

        $w->add_control('order', [
            'label' => 'ترتیب',
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'DESC',
            'options' => [
                'DESC' => 'نزولی (جدیدترین)',
                'ASC' => 'صعودی (قدیمی‌ترین)',
            ],
        ]);

        $w->add_control('exclude_current', [
            'label' => 'حذف نوشته‌ی جاری',
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => 'yes',
        ]);

        $w->add_control('only_thumb', [
            'label' => 'فقط نوشته‌های دارای تصویر شاخص',
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => '',
        ]);

        if (!empty($a['pagination'])) {
            $w->add_control('pagination', [
                'label' => 'صفحه‌بندی',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'none',
                'options' => [
                    'none' => 'بدون صفحه‌بندی',
                    'numbers' => 'شماره صفحات',
                ],
            ]);
        }

        $w->end_controls_section();
    }

    public static function add_card_controls($w, $with_template = false)
    {
        $cond = $with_template ? ['template_id' => '0'] : [];

        $w->start_controls_section('section_card', ['label' => 'محتوای کارت']);

        if ($with_template) {
            $w->add_control('template_id', [
                'label' => 'قالب آیتم (اختیاری)',
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => self::template_options(),
                'default' => '0',
                'label_block' => true,
                'description' => 'یک قالب المنتور (Loop Item یا Section) انتخاب کنید تا برای هر نوشته تکرار شود. اگر انتخاب نکنید، کارت پیش‌فرض دارکسو نمایش داده می‌شود.',
            ]);
        }

        $w->add_control('show_image', [
            'label' => 'تصویر شاخص',
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => 'yes',
            'condition' => $cond,
        ]);

        $w->add_control('thumb_size', [
            'label' => 'سایز تصویر',
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => self::image_size_options(),
            'default' => 'large',
            'condition' => array_merge($cond, ['show_image' => 'yes']),
        ]);

        $w->add_control('show_term', [
            'label' => 'نمایش دسته‌بندی',
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => 'yes',
            'condition' => $cond,
        ]);

        $w->add_control('term_taxonomy', [
            'label' => 'طبقه‌بندی',
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => self::taxonomy_options(),
            'default' => 'category',
            'condition' => array_merge($cond, ['show_term' => 'yes']),
        ]);

        $w->add_control('show_title', [
            'label' => 'عنوان',
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => 'yes',
            'condition' => $cond,
        ]);

        $w->add_control('title_tag', [
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
            'condition' => array_merge($cond, ['show_title' => 'yes']),
        ]);

        $w->add_control('show_meta', [
            'label' => 'تاریخ',
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => 'yes',
            'condition' => $cond,
        ]);

        $w->add_control('show_author', [
            'label' => 'نویسنده',
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => '',
            'condition' => $cond,
        ]);

        $w->add_control('show_excerpt', [
            'label' => 'خلاصه',
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => 'yes',
            'condition' => $cond,
        ]);

        $w->add_control('excerpt_length', [
            'label' => 'تعداد کلمات خلاصه',
            'type' => \Elementor\Controls_Manager::NUMBER,
            'default' => 20,
            'min' => 5,
            'max' => 120,
            'condition' => array_merge($cond, ['show_excerpt' => 'yes']),
        ]);

        $w->add_control('show_button', [
            'label' => 'دکمه',
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => 'yes',
            'condition' => $cond,
        ]);

        $w->add_control('button_text', [
            'label' => 'متن دکمه',
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => 'ادامه مطلب',
            'condition' => array_merge($cond, ['show_button' => 'yes']),
        ]);

        $w->end_controls_section();
    }

    /**
     * تب‌های رنگ روشن / تاریک.
     * $items: id => ['label' => '', 'sel' => 'selector' | [selectors], 'prop' => 'color']
     */
    public static function style_tabs($w, $tabs_id, array $items)
    {
        $w->start_controls_tabs($tabs_id);

        foreach (['light' => 'روشن', 'dark' => 'تاریک'] as $mode => $label) {
            $w->start_controls_tab($tabs_id . '_' . $mode, ['label' => $label]);

            foreach ($items as $id => $item) {
                $selectors = [];
                foreach ((array) $item['sel'] as $sel) {
                    $full = ($mode === 'dark' ? 'html.darkso-dark ' : '') . '{{WRAPPER}} ' . $sel;
                    $selectors[$full] = $item['prop'] . ': {{VALUE}};';
                }
                $w->add_control($id . '_' . $mode, [
                    'label' => $item['label'],
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'selectors' => $selectors,
                ]);
            }

            $w->end_controls_tab();
        }

        $w->end_controls_tabs();
    }

    public static function add_card_style($w)
    {
        $w->start_controls_section('section_card_style', [
            'label' => 'کارت',
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $w->add_responsive_control('media_ratio', [
            'label' => 'نسبت تصویر',
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => '',
            'options' => [
                '' => 'پیش‌فرض',
                '1 / 1' => '1:1',
                '4 / 3' => '4:3',
                '3 / 2' => '3:2',
                '16 / 9' => '16:9',
                '3 / 4' => '3:4',
            ],
            'selectors' => [
                '{{WRAPPER}} .darkso-card__media' => 'aspect-ratio: {{VALUE}};',
            ],
        ]);

        $w->add_responsive_control('card_padding', [
            'label' => 'پدینگ محتوا',
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em'],
            'selectors' => [
                '{{WRAPPER}} .darkso-card__body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $w->add_control('card_radius', [
            'label' => 'گردی گوشه',
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px', '%'],
            'range' => ['px' => ['min' => 0, 'max' => 60]],
            'selectors' => [
                '{{WRAPPER}} .darkso-card' => 'border-radius: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $w->add_group_control(\Elementor\Group_Control_Border::get_type(), [
            'name' => 'card_border',
            'selector' => '{{WRAPPER}} .darkso-card',
        ]);

        $w->add_group_control(\Elementor\Group_Control_Box_Shadow::get_type(), [
            'name' => 'card_shadow',
            'selector' => '{{WRAPPER}} .darkso-card',
        ]);

        $w->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name' => 'title_typo',
            'label' => 'تایپوگرافی عنوان',
            'selector' => '{{WRAPPER}} .darkso-card__title',
        ]);

        $w->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name' => 'excerpt_typo',
            'label' => 'تایپوگرافی خلاصه',
            'selector' => '{{WRAPPER}} .darkso-card__excerpt',
        ]);

        $w->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name' => 'meta_typo',
            'label' => 'تایپوگرافی متا',
            'selector' => '{{WRAPPER}} .darkso-card__meta',
        ]);

        $w->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name' => 'button_typo',
            'label' => 'تایپوگرافی دکمه',
            'selector' => '{{WRAPPER}} .darkso-card__btn',
        ]);

        self::style_tabs($w, 'tabs_card_colors', [
            'card_bg' => ['label' => 'پس‌زمینه کارت', 'sel' => '.darkso-card', 'prop' => 'background-color'],
            'card_border_color' => ['label' => 'رنگ کادر', 'sel' => '.darkso-card', 'prop' => 'border-color'],
            'title_color' => ['label' => 'رنگ عنوان', 'sel' => ['.darkso-card__title', '.darkso-card__title a'], 'prop' => 'color'],
            'title_hover' => ['label' => 'رنگ هاور عنوان', 'sel' => '.darkso-card__title a:hover', 'prop' => 'color'],
            'excerpt_color' => ['label' => 'رنگ خلاصه', 'sel' => '.darkso-card__excerpt', 'prop' => 'color'],
            'meta_color' => ['label' => 'رنگ متا', 'sel' => '.darkso-card__meta', 'prop' => 'color'],
            'term_color' => ['label' => 'رنگ متن دسته', 'sel' => '.darkso-card__term', 'prop' => 'color'],
            'term_bg' => ['label' => 'پس‌زمینه دسته', 'sel' => '.darkso-card__term', 'prop' => 'background-color'],
            'btn_color' => ['label' => 'رنگ متن دکمه', 'sel' => '.darkso-card__btn', 'prop' => 'color'],
            'btn_bg' => ['label' => 'پس‌زمینه دکمه', 'sel' => '.darkso-card__btn', 'prop' => 'background-color'],
        ]);

        $w->end_controls_section();
    }

    /* ------------------------------------------------------------------ *
     * کوئری
     * ------------------------------------------------------------------ */

    public static function query_args($s, $page = 1)
    {
        $post_type = !empty($s['post_type']) && post_type_exists($s['post_type']) ? $s['post_type'] : 'post';
        $per_page = max(1, min(60, (int) ($s['posts_per_page'] ?? 6)));
        $base_offset = max(0, (int) ($s['offset'] ?? 0));
        $page = max(1, (int) $page);

        $orderby = $s['orderby'] ?? 'date';
        if (!in_array($orderby, ['date', 'modified', 'title', 'menu_order', 'comment_count', 'rand'], true)) {
            $orderby = 'date';
        }
        $order = (($s['order'] ?? 'DESC') === 'ASC') ? 'ASC' : 'DESC';

        $args = [
            'post_type' => $post_type,
            'post_status' => 'publish',
            'posts_per_page' => $per_page,
            'offset' => $base_offset + (($page - 1) * $per_page),
            'orderby' => $orderby,
            'order' => $order,
            'ignore_sticky_posts' => true,
        ];

        $term_ids = isset($s['terms']) && is_array($s['terms']) ? $s['terms'] : [];
        if ($term_ids) {
            $by_tax = [];
            foreach ($term_ids as $term_id) {
                $term = get_term((int) $term_id);
                if ($term && !is_wp_error($term)) {
                    $by_tax[$term->taxonomy][] = (int) $term->term_id;
                }
            }
            $tax_query = [];
            foreach ($by_tax as $tax => $ids) {
                $tax_query[] = [
                    'taxonomy' => $tax,
                    'field' => 'term_id',
                    'terms' => $ids,
                ];
            }
            if (count($tax_query) > 1) {
                $tax_query['relation'] = 'OR';
            }
            if ($tax_query) {
                $args['tax_query'] = $tax_query;
            }
        }

        if (($s['exclude_current'] ?? '') === 'yes') {
            $current = (int) get_queried_object_id();
            if ($current > 0) {
                $args['post__not_in'] = [$current];
            }
        }

        if (($s['only_thumb'] ?? '') === 'yes') {
            $args['meta_query'] = [
                [
                    'key' => '_thumbnail_id',
                    'compare' => 'EXISTS',
                ],
            ];
        }

        return $args;
    }

    public static function total_pages($query, $s)
    {
        $per_page = max(1, min(60, (int) ($s['posts_per_page'] ?? 6)));
        $base_offset = max(0, (int) ($s['offset'] ?? 0));
        $found = max(0, (int) $query->found_posts - $base_offset);
        return (int) ceil($found / $per_page);
    }

    public static function current_page($param)
    {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        if (isset($_GET[$param])) {
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            return max(1, absint(wp_unslash($_GET[$param])));
        }
        return 1;
    }

    public static function render_pagination($total, $current, $param)
    {
        if ($total <= 1) {
            return;
        }

        $base = remove_query_arg($param);
        $links = paginate_links([
            'base' => add_query_arg($param, '%#%', $base),
            'format' => '',
            'current' => $current,
            'total' => $total,
            'type' => 'array',
            'prev_text' => '‹',
            'next_text' => '›',
            'mid_size' => 1,
            'end_size' => 1,
        ]);

        if (!$links) {
            return;
        }

        echo '<nav class="darkso-pagination" aria-label="pagination">';
        foreach ($links as $link) {
            echo wp_kses_post($link);
        }
        echo '</nav>';
    }

    /* ------------------------------------------------------------------ *
     * رندر آیتم
     * ------------------------------------------------------------------ */

    public static function render_item($s)
    {
        $template_id = (int) ($s['template_id'] ?? 0);

        if ($template_id > 0 && class_exists('\Elementor\Plugin') && !isset(self::$rendering[$template_id])) {
            self::$rendering[$template_id] = true;
            echo '<div class="darkso-loop-template">';
            echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display($template_id, true); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo '</div>';
            unset(self::$rendering[$template_id]);
            return;
        }

        self::render_card($s);
    }

    public static function render_card($s)
    {
        $id = get_the_ID();
        $link = get_permalink($id);
        $title = get_the_title($id);

        echo '<article class="darkso-card">';

        if (($s['show_image'] ?? 'yes') === 'yes') {
            $size = !empty($s['thumb_size']) ? $s['thumb_size'] : 'large';
            echo '<a class="darkso-card__media" href="' . esc_url($link) . '" tabindex="-1" aria-hidden="true">';
            if (has_post_thumbnail($id)) {
                echo get_the_post_thumbnail($id, $size, ['loading' => 'lazy', 'class' => 'darkso-card__img', 'alt' => esc_attr($title)]); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            }
            echo '</a>';
        }

        echo '<div class="darkso-card__body">';

        if (($s['show_term'] ?? 'yes') === 'yes') {
            $tax = !empty($s['term_taxonomy']) ? $s['term_taxonomy'] : 'category';
            if (taxonomy_exists($tax)) {
                $terms = get_the_terms($id, $tax);
                if (is_array($terms) && $terms) {
                    $first = reset($terms);
                    echo '<span class="darkso-card__term">' . esc_html($first->name) . '</span>';
                }
            }
        }

        if (($s['show_title'] ?? 'yes') === 'yes') {
            $tag = in_array(($s['title_tag'] ?? 'h3'), ['h2', 'h3', 'h4', 'h5', 'h6', 'div'], true) ? $s['title_tag'] : 'h3';
            echo '<' . $tag . ' class="darkso-card__title"><a href="' . esc_url($link) . '">' . esc_html($title) . '</a></' . $tag . '>';
        }

        $show_meta = ($s['show_meta'] ?? 'yes') === 'yes';
        $show_author = ($s['show_author'] ?? '') === 'yes';
        if ($show_meta || $show_author) {
            echo '<div class="darkso-card__meta">';
            if ($show_meta) {
                echo '<span class="darkso-card__date">' . esc_html(get_the_date('', $id)) . '</span>';
            }
            if ($show_author) {
                echo '<span class="darkso-card__author">' . esc_html(get_the_author_meta('display_name', (int) get_post_field('post_author', $id))) . '</span>';
            }
            echo '</div>';
        }

        if (($s['show_excerpt'] ?? 'yes') === 'yes') {
            $words = max(5, min(120, (int) ($s['excerpt_length'] ?? 20)));
            $raw = has_excerpt($id) ? get_the_excerpt($id) : wp_strip_all_tags(strip_shortcodes((string) get_post_field('post_content', $id)));
            echo '<p class="darkso-card__excerpt">' . esc_html(wp_trim_words($raw, $words, '…')) . '</p>';
        }

        if (($s['show_button'] ?? 'yes') === 'yes') {
            $text = isset($s['button_text']) && $s['button_text'] !== '' ? $s['button_text'] : 'ادامه مطلب';
            echo '<a class="darkso-card__btn" href="' . esc_url($link) . '">' . esc_html($text) . '</a>';
        }

        echo '</div>';
        echo '</article>';
    }
}
