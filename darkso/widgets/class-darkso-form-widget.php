<?php

if (!defined('ABSPATH')) {
    exit;
}

class Darkso_Form_Widget extends \Elementor\Widget_Base
{
    public function get_name()
    {
        return 'darkso_form';
    }

    public function get_title()
    {
        return 'فرم لایت / دارک';
    }

    public function get_icon()
    {
        return 'eicon-form-horizontal';
    }

    public function get_categories()
    {
        return ['darkso', 'basic', 'general'];
    }

    public function get_keywords()
    {
        return ['form', 'contact', 'email', 'dark', 'light', 'darkso', 'فرم', 'تماس'];
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
        $this->start_controls_section('section_fields', ['label' => 'فیلدها']);

        $this->add_control('form_title', [
            'label' => 'نام فرم (برای پیام‌ها)',
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => 'فرم تماس',
        ]);

        $repeater = new \Elementor\Repeater();

        $repeater->add_control('label', [
            'label' => 'عنوان فیلد',
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => 'عنوان',
        ]);

        $repeater->add_control('type', [
            'label' => 'نوع',
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'text',
            'options' => [
                'text' => 'متن کوتاه',
                'email' => 'ایمیل',
                'tel' => 'تلفن',
                'number' => 'عدد',
                'textarea' => 'متن بلند',
                'select' => 'لیست کشویی',
                'checkbox' => 'تیک (چک‌باکس)',
            ],
        ]);

        $repeater->add_control('placeholder', [
            'label' => 'پیش‌نویس (Placeholder)',
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => '',
            'condition' => ['type!' => ['select', 'checkbox']],
        ]);

        $repeater->add_control('options', [
            'label' => 'گزینه‌ها (هر خط یک گزینه)',
            'type' => \Elementor\Controls_Manager::TEXTAREA,
            'default' => "گزینه اول\nگزینه دوم",
            'condition' => ['type' => 'select'],
        ]);

        $repeater->add_control('required', [
            'label' => 'اجباری',
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => '',
        ]);

        $repeater->add_control('width', [
            'label' => 'عرض',
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => '100',
            'options' => [
                '100' => '100%',
                '50' => '50%',
                '33' => '33%',
            ],
        ]);

        $this->add_control('fields', [
            'label' => 'فیلدها',
            'type' => \Elementor\Controls_Manager::REPEATER,
            'fields' => $repeater->get_controls(),
            'title_field' => '{{{ label }}}',
            'default' => [
                ['label' => 'نام و نام خانوادگی', 'type' => 'text', 'required' => 'yes', 'width' => '50', 'placeholder' => 'نام شما'],
                ['label' => 'ایمیل', 'type' => 'email', 'required' => 'yes', 'width' => '50', 'placeholder' => 'name@example.com'],
                ['label' => 'پیام', 'type' => 'textarea', 'required' => 'yes', 'width' => '100', 'placeholder' => 'پیام خود را بنویسید...'],
            ],
        ]);

        $this->add_control('submit_text', [
            'label' => 'متن دکمه ارسال',
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => 'ارسال پیام',
            'separator' => 'before',
        ]);

        $this->add_control('show_labels', [
            'label' => 'نمایش عنوان بالای فیلدها',
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => 'yes',
        ]);

        $this->end_controls_section();

        $this->start_controls_section('section_delivery', ['label' => 'ارسال و پیام‌ها']);

        $this->add_control('send_email', [
            'label' => 'ارسال ایمیل',
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => 'yes',
        ]);

        $this->add_control('to_email', [
            'label' => 'ایمیل گیرنده',
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => get_option('admin_email'),
            'placeholder' => get_option('admin_email'),
            'condition' => ['send_email' => 'yes'],
        ]);

        $this->add_control('subject', [
            'label' => 'موضوع ایمیل',
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => 'پیام جدید از سایت',
            'condition' => ['send_email' => 'yes'],
        ]);

        $this->add_control('save_entry', [
            'label' => 'ذخیره پیام در پیشخوان',
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => 'yes',
            'description' => 'پیام‌ها در منوی «پیام‌های فرم» پیشخوان وردپرس نمایش داده می‌شوند.',
        ]);

        $this->add_control('success_message', [
            'label' => 'پیام موفقیت',
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => 'پیام شما با موفقیت ارسال شد. ممنون!',
            'label_block' => true,
            'separator' => 'before',
        ]);

        $this->add_control('error_message', [
            'label' => 'پیام خطا',
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => 'ارسال انجام نشد. لطفاً دوباره تلاش کنید.',
            'label_block' => true,
        ]);

        $this->end_controls_section();

        $this->start_controls_section('section_form_style', [
            'label' => 'فرم',
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_responsive_control('form_gap', [
            'label' => 'فاصله فیلدها',
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range' => ['px' => ['min' => 0, 'max' => 60]],
            'default' => ['size' => 16, 'unit' => 'px'],
            'selectors' => [
                '{{WRAPPER}} .darkso-form' => '--darkso-form-gap: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name' => 'label_typo',
            'label' => 'تایپوگرافی عنوان فیلد',
            'selector' => '{{WRAPPER}} .darkso-field__label',
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name' => 'input_typo',
            'label' => 'تایپوگرافی فیلدها',
            'selector' => '{{WRAPPER}} .darkso-field__input',
        ]);

        $this->add_responsive_control('input_padding', [
            'label' => 'پدینگ فیلدها',
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em'],
            'selectors' => [
                '{{WRAPPER}} .darkso-field__input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_control('input_radius', [
            'label' => 'گردی گوشه فیلدها',
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range' => ['px' => ['min' => 0, 'max' => 40]],
            'selectors' => [
                '{{WRAPPER}} .darkso-field__input' => 'border-radius: {{SIZE}}{{UNIT}};',
            ],
        ]);

        Darkso_Helpers::style_tabs($this, 'tabs_form_colors', [
            'fm_label' => ['label' => 'رنگ عنوان فیلد', 'sel' => '.darkso-field__label', 'prop' => 'color'],
            'fm_input_color' => ['label' => 'رنگ متن فیلد', 'sel' => '.darkso-field__input', 'prop' => 'color'],
            'fm_input_bg' => ['label' => 'پس‌زمینه فیلد', 'sel' => '.darkso-field__input', 'prop' => 'background-color'],
            'fm_input_border' => ['label' => 'کادر فیلد', 'sel' => '.darkso-field__input', 'prop' => 'border-color'],
            'fm_input_focus' => ['label' => 'کادر فیلد در فوکوس', 'sel' => '.darkso-field__input:focus', 'prop' => 'border-color'],
        ]);

        $this->end_controls_section();

        $this->start_controls_section('section_button_style', [
            'label' => 'دکمه و پیام‌ها',
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name' => 'submit_typo',
            'label' => 'تایپوگرافی دکمه',
            'selector' => '{{WRAPPER}} .darkso-form__submit',
        ]);

        $this->add_control('submit_radius', [
            'label' => 'گردی گوشه دکمه',
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range' => ['px' => ['min' => 0, 'max' => 60]],
            'selectors' => [
                '{{WRAPPER}} .darkso-form__submit' => 'border-radius: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('submit_padding', [
            'label' => 'پدینگ دکمه',
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em'],
            'selectors' => [
                '{{WRAPPER}} .darkso-form__submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_control('submit_full', [
            'label' => 'دکمه تمام‌عرض',
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => '',
            'selectors_dictionary' => ['yes' => 'width: 100%;'],
            'selectors' => [
                '{{WRAPPER}} .darkso-form__submit' => '{{VALUE}}',
            ],
        ]);

        Darkso_Helpers::style_tabs($this, 'tabs_submit_colors', [
            'fm_btn_color' => ['label' => 'رنگ متن دکمه', 'sel' => '.darkso-form__submit', 'prop' => 'color'],
            'fm_btn_bg' => ['label' => 'پس‌زمینه دکمه', 'sel' => '.darkso-form__submit', 'prop' => 'background-color'],
            'fm_btn_bg_hover' => ['label' => 'پس‌زمینه دکمه (هاور)', 'sel' => '.darkso-form__submit:hover', 'prop' => 'background-color'],
            'fm_ok' => ['label' => 'رنگ پیام موفقیت', 'sel' => '.darkso-form__status.is-success', 'prop' => 'color'],
            'fm_err' => ['label' => 'رنگ پیام خطا', 'sel' => '.darkso-form__status.is-error', 'prop' => 'color'],
        ]);

        $this->end_controls_section();
    }

    private function parse_fields($settings)
    {
        $out = [];
        $allowed = ['text', 'email', 'tel', 'number', 'textarea', 'select', 'checkbox'];
        $rows = isset($settings['fields']) && is_array($settings['fields']) ? $settings['fields'] : [];

        foreach ($rows as $i => $row) {
            $id = !empty($row['_id']) ? sanitize_key($row['_id']) : (string) $i;
            $type = isset($row['type']) && in_array($row['type'], $allowed, true) ? $row['type'] : 'text';
            $options = [];
            if ($type === 'select' && !empty($row['options'])) {
                foreach (preg_split('/\r\n|\r|\n/', (string) $row['options']) as $opt) {
                    $opt = sanitize_text_field($opt);
                    if ($opt !== '') {
                        $options[] = $opt;
                    }
                }
            }
            $width = isset($row['width']) && in_array((string) $row['width'], ['100', '50', '33'], true) ? (string) $row['width'] : '100';

            $out['f_' . $id] = [
                'label' => sanitize_text_field($row['label'] ?? ('فیلد ' . ($i + 1))),
                'type' => $type,
                'required' => !empty($row['required']) && $row['required'] === 'yes',
                'placeholder' => sanitize_text_field($row['placeholder'] ?? ''),
                'options' => $options,
                'width' => $width,
            ];
        }

        return $out;
    }

    protected function render()
    {
        $s = $this->get_settings_for_display();
        $fields = $this->parse_fields($s);

        if (!$fields) {
            if (Darkso_Helpers::is_edit()) {
                echo '<div class="darkso-menu-empty">حداقل یک فیلد به فرم اضافه کنید.</div>';
            }
            return;
        }

        $config_fields = [];
        foreach ($fields as $name => $f) {
            $config_fields[$name] = [
                'label' => $f['label'],
                'type' => $f['type'],
                'required' => $f['required'],
                'options' => $f['options'],
            ];
        }

        $to = isset($s['to_email']) ? sanitize_email($s['to_email']) : '';

        $key = Darkso_Forms::register_config([
            'title' => sanitize_text_field($s['form_title'] ?? 'فرم تماس'),
            'to' => $to,
            'subject' => sanitize_text_field($s['subject'] ?? ''),
            'success' => sanitize_text_field($s['success_message'] ?? ''),
            'error' => sanitize_text_field($s['error_message'] ?? ''),
            'send' => ($s['send_email'] ?? 'yes') === 'yes',
            'save' => ($s['save_entry'] ?? 'yes') === 'yes',
            'fields' => $config_fields,
        ]);

        $show_labels = ($s['show_labels'] ?? 'yes') === 'yes';
        $uid = sanitize_key($this->get_id());
        $submit = !empty($s['submit_text']) ? $s['submit_text'] : 'ارسال';

        echo '<form class="darkso-form" data-darkso-form method="post" action="#">';
        echo '<input type="hidden" name="darkso_key" value="' . esc_attr($key) . '">';
        echo '<div class="darkso-form__hp" aria-hidden="true"><label>وب‌سایت<input type="text" name="darkso_website" tabindex="-1" autocomplete="off"></label></div>';

        echo '<div class="darkso-form__fields">';
        foreach ($fields as $name => $f) {
            $input_id = 'darkso-' . $uid . '-' . $name;
            $req = $f['required'] ? ' required aria-required="true"' : '';
            $ph = $f['placeholder'] !== '' ? ' placeholder="' . esc_attr($f['placeholder']) . '"' : '';

            echo '<div class="darkso-field darkso-field--w' . esc_attr($f['width']) . ' darkso-field--' . esc_attr($f['type']) . '">';

            if ($f['type'] === 'checkbox') {
                echo '<label class="darkso-field__check" for="' . esc_attr($input_id) . '">';
                echo '<input class="darkso-field__box" type="checkbox" id="' . esc_attr($input_id) . '" name="' . esc_attr($name) . '" value="1"' . $req . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                echo '<span>' . esc_html($f['label']) . ($f['required'] ? ' <em>*</em>' : '') . '</span>';
                echo '</label>';
            } else {
                if ($show_labels) {
                    echo '<label class="darkso-field__label" for="' . esc_attr($input_id) . '">' . esc_html($f['label']) . ($f['required'] ? ' <em>*</em>' : '') . '</label>';
                }

                switch ($f['type']) {
                    case 'textarea':
                        echo '<textarea class="darkso-field__input" id="' . esc_attr($input_id) . '" name="' . esc_attr($name) . '" rows="5"' . $ph . $req . '></textarea>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        break;
                    case 'select':
                        echo '<select class="darkso-field__input" id="' . esc_attr($input_id) . '" name="' . esc_attr($name) . '"' . $req . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        echo '<option value="">' . esc_html($f['placeholder'] !== '' ? $f['placeholder'] : 'انتخاب کنید') . '</option>';
                        foreach ($f['options'] as $opt) {
                            echo '<option value="' . esc_attr($opt) . '">' . esc_html($opt) . '</option>';
                        }
                        echo '</select>';
                        break;
                    default:
                        $type = in_array($f['type'], ['email', 'tel', 'number'], true) ? $f['type'] : 'text';
                        echo '<input class="darkso-field__input" type="' . esc_attr($type) . '" id="' . esc_attr($input_id) . '" name="' . esc_attr($name) . '"' . $ph . $req . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        break;
                }
            }

            echo '</div>';
        }
        echo '</div>';

        echo '<div class="darkso-form__actions"><button type="submit" class="darkso-form__submit">' . esc_html($submit) . '</button></div>';
        echo '<div class="darkso-form__status" role="status" aria-live="polite"></div>';
        echo '</form>';
    }
}
