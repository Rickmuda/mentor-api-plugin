<?php
defined('ABSPATH') or die('No script kiddies please!');

class MentorTheme
{
    private $api;

    public function __construct(MentorApi $api)
    {
        $this->api = $api;

        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
        add_action('wp_head', array($this, 'inject_theme_css'));
    }

    public function enqueue_styles()
    {
        wp_enqueue_style(
            'mentor_plugin_styles',
            MENTOR_PLUGIN_URL . 'assets/css/mentor-plugin.css',
            [],
            MENTOR_PLUGIN_VERSION
        );
    }

    public function inject_theme_css()
    {
        if (empty($this->api->get_api_url())) {
            return;
        }

        if (!get_option('mentor_theme_enabled')) {
            return;
        }

        $data = $this->api->fetch_data('/api/client_api/frontendtheme/');
        if (empty($data) || !is_array($data)) {
            return;
        }

        $color_defaults = [
            'primary-color' => '#417AB3',
            'secondary-color' => '#F6A623',
            'secondary-color-hover' => 'rgba(246,166,35,0.8)',
            'body-text-color' => '#1D4065',
        ];
        $colors = array_merge($color_defaults, $data['css'] ?? []);

        $root_vars = [
            '--color-primary' => sanitize_text_field($colors['primary-color']),
            '--color-secondary' => sanitize_text_field($colors['secondary-color']),
            '--color-secondary-hover' => sanitize_text_field($colors['secondary-color-hover']),
            '--color-body-text' => sanitize_text_field($colors['body-text-color']),
        ];

        $style = ":root, .tw {\n";
        foreach ($root_vars as $key => $val) {
            $style .= "  {$key}: {$val};\n";
        }
        $style .= "}\n";

        $font_styles = '';
        $body_font = '';
        $heading_font = '';

        if (!empty($data['fonts']['fonts']) && is_array($data['fonts']['fonts'])) {
            foreach ($data['fonts']['fonts'] as $font) {
                $font_styles .= $this->build_font_face($font) . "\n";

                $selectors = implode(' ', $font['selectors'] ?? []);
                $family = sanitize_text_field($font['fontFamily'] ?? '');

                if (stripos($selectors, 'body') !== false && empty($body_font)) {
                    $body_font = $family;
                } elseif (stripos($selectors, 'h1') !== false && empty($heading_font)) {
                    $heading_font = $family;
                }
            }
        }

        if ($body_font) {
            $style .= ".tw, .tw p, .tw span, .tw li, .tw a, .tw button, .tw input, .tw textarea { font-family: '{$body_font}', sans-serif; }\n";
        }

        if ($heading_font) {
            $style .= ".tw h1, .tw h2, .tw h3, .tw h4, .tw h5, .tw h6 { font-family: '{$heading_font}', sans-serif; }\n";
        }

        $style .= $font_styles;

        echo "<style id='mentor-theme-css'>\n{$style}\n</style>";
    }

    private function build_font_face($font)
    {
        $family = sanitize_text_field($font['fontFamily'] ?? '');
        $srcs = [];

        foreach (['fontUrlWOFF2', 'fontUrlWOFF', 'fontUrlTTF'] as $formatKey) {
            if (!empty($font[$formatKey])) {
                $url = esc_url_raw($font[$formatKey]);
                $format = str_replace('fontUrl', '', strtolower($formatKey));
                $srcs[] = "url('{$url}') format('{$format}')";
            }
        }

        if (empty($family) || empty($srcs)) {
            return '';
        }

        return "@font-face {
            font-family: '{$family}';
            src: " . implode(', ', $srcs) . ";
            font-display: swap;
        }";
    }
}
