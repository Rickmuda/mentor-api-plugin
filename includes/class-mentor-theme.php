<?php
defined('ABSPATH') or die('No script kiddies please!');

class MentorTheme
{
    private $api;

    public function __construct(MentorApi $api)
    {
        $this->api = $api;

        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
    }

    public function enqueue_styles()
    {
        wp_enqueue_style(
            'mentor_plugin_styles',
            MENTOR_PLUGIN_URL . 'assets/css/mentor-plugin.css',
            [],
            MENTOR_PLUGIN_VERSION
        );

        $inline = $this->build_theme_css();
        if (!empty($inline)) {
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- All interpolated values are validated against strict allowlist regex in sanitize_css_color()/sanitize_css_font_name() and URLs via esc_url_raw(); wp_strip_all_tags() provides additional defense.
            wp_add_inline_style('mentor_plugin_styles', wp_strip_all_tags($inline));
        }
    }

    public function get_theme_css()
    {
        return $this->build_theme_css();
    }

    private function build_theme_css()
    {
        if (empty($this->api->get_api_url())) {
            return '';
        }

        if (!get_option('mentor_theme_enabled')) {
            return '';
        }

        $data = $this->api->fetch_data('/api/client_api/frontendtheme/');
        if (empty($data) || !is_array($data)) {
            return '';
        }

        $color_defaults = [
            'primary-color' => '#417AB3',
            'secondary-color' => '#F6A623',
            'secondary-color-hover' => 'rgba(246,166,35,0.8)',
            'body-text-color' => '#1D4065',
        ];
        $colors = array_merge($color_defaults, is_array($data['css'] ?? null) ? $data['css'] : []);

        $root_vars = [
            '--color-primary' => $this->sanitize_css_color($colors['primary-color']),
            '--color-secondary' => $this->sanitize_css_color($colors['secondary-color']),
            '--color-secondary-hover' => $this->sanitize_css_color($colors['secondary-color-hover']),
            '--color-body-text' => $this->sanitize_css_color($colors['body-text-color']),
        ];

        $style = ":root, .tw {\n";
        foreach ($root_vars as $key => $val) {
            if ($val !== '') {
                $style .= "  {$key}: {$val};\n";
            }
        }
        $style .= "}\n";

        $font_styles = '';
        $body_font = '';
        $heading_font = '';

        if (!empty($data['fonts']['fonts']) && is_array($data['fonts']['fonts'])) {
            foreach ($data['fonts']['fonts'] as $font) {
                $font_face = $this->build_font_face($font);
                if ($font_face !== '') {
                    $font_styles .= $font_face . "\n";
                }

                $selectors = implode(' ', array_filter(
                    $font['selectors'] ?? [],
                    'is_scalar'
                ));
                $family = $this->sanitize_css_font_name($font['fontFamily'] ?? '');

                if ($family === '') {
                    continue;
                }

                if (stripos($selectors, 'body') !== false && empty($body_font)) {
                    $body_font = $family;
                } elseif (stripos($selectors, 'h1') !== false && empty($heading_font)) {
                    $heading_font = $family;
                }
            }
        }

        if ($body_font !== '') {
            $style .= ".tw, .tw p, .tw span, .tw li, .tw a, .tw button, .tw input, .tw textarea { font-family: \"{$body_font}\", sans-serif; }\n";
        }

        if ($heading_font !== '') {
            $style .= ".tw h1, .tw h2, .tw h3, .tw h4, .tw h5, .tw h6 { font-family: \"{$heading_font}\", sans-serif; }\n";
        }

        $style .= $font_styles;

        return $style;
    }

    private function build_font_face($font)
    {
        $family = $this->sanitize_css_font_name($font['fontFamily'] ?? '');
        $srcs = [];

        foreach (['fontUrlWOFF2', 'fontUrlWOFF', 'fontUrlTTF'] as $formatKey) {
            if (!empty($font[$formatKey])) {
                $raw_url = $font[$formatKey];
                if (!is_string($raw_url)) {
                    continue;
                }
                $url = esc_url_raw($raw_url);
                if ($url === '') {
                    continue;
                }
                $format = strtolower(str_replace('fontUrl', '', $formatKey));
                $srcs[] = "url(\"{$url}\") format(\"{$format}\")";
            }
        }

        if ($family === '' || empty($srcs)) {
            return '';
        }

        return "@font-face { font-family: \"{$family}\"; src: " . implode(', ', $srcs) . "; font-display: swap; }";
    }

    private function sanitize_css_color($value)
    {
        $value = is_string($value) ? trim($value) : '';
        if ($value === '') {
            return '';
        }
        if (preg_match('/^#[0-9a-fA-F]{3,8}$/', $value)) {
            return $value;
        }
        if (preg_match('/^rgba?\(\s*\d+\s*,\s*\d+\s*,\s*\d+\s*(,\s*(?:\d+|\d*\.\d+)\s*)?\)$/', $value)) {
            return $value;
        }
        if (preg_match('/^hsla?\(\s*\d+(?:\.\d+)?\s*,\s*\d+(?:\.\d+)?%\s*,\s*\d+(?:\.\d+)?%\s*(,\s*(?:\d+|\d*\.\d+)\s*)?\)$/', $value)) {
            return $value;
        }
        if (preg_match('/^[a-zA-Z]+$/', $value)) {
            return $value;
        }
        return '';
    }

    private function sanitize_css_font_name($value)
    {
        $value = is_string($value) ? trim($value) : '';
        if ($value === '') {
            return '';
        }
        if (preg_match('/^[a-zA-Z0-9 \-_]+$/', $value)) {
            return $value;
        }
        return '';
    }
}
