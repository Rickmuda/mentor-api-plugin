<?php
/*
Plugin Name: Mentor Plugin
Description: Fetch and display categories and courses from a mentor application Software for educators
Version: 0.4
Author: Mark Vergunst
*/

defined('ABSPATH') or die('No script kiddies please!');

// Include the templates file
if (file_exists(plugin_dir_path(__FILE__) . 'templates.php')) {
    require_once plugin_dir_path(__FILE__) . 'templates.php';
}

class WPMentorCoursesCategories {
    private $api_url;

    public function __construct() {
        add_shortcode('mentor_courses', array($this, 'display_courses'));
        add_shortcode('mentor_categories', array($this, 'display_categories'));
        add_shortcode('display_coursegroup_wc', array($this, 'display_trainingtracks'));
        add_action('admin_menu', array($this, 'create_settings_page'));
        add_action('admin_init', array($this, 'setup_sections'));
        add_action('admin_init', array($this, 'setup_fields'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
        add_action('wp_head', array($this, 'inject_theme_css'));

        $this->api_url = get_option('mentor_courses_api_url');
    }

    private function fetch_data($endpoint) {
        $response = wp_remote_get($this->api_url . $endpoint);

        if (is_wp_error($response)) {
            return [];
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        return $data;
    }

    public function display_courses() {
        $courses = $this->fetch_data('/api/modules_api/catalog/');
        if (empty($courses)) {
            return '<p>No courses found.</p>';
        }
        $output = display_course_template($courses, $this->api_url);

        return $output;
    }

    public function display_categories() {
        $categories = $this->fetch_data('/api/modules_api/subjects/');
        $output = display_category_template($categories, $this->api_url);
        return $output;
    }

    public function create_settings_page() {
        add_options_page(
            'Mentor Courses and Categories Settings',
            'Mentor Courses and Categories',
            'manage_options',
            'wp_mentor_courses_categories',
            array($this, 'settings_page_content')
        );
    }

    public function settings_page_content() { ?>
        <div class="wrap">
            <h1>Mentor Courses and Categories Settings</h1>
            <form method="post" action="options.php">
                <?php
                    settings_fields('wp_mentor_courses_categories');
                    do_settings_sections('wp_mentor_courses_categories');
                    submit_button();
                ?>
            </form>
        </div> <?php
    }

    public function setup_sections() {
        add_settings_section(
            'wp_mentor_courses_categories_section',
            'API Settings',
            null,
            'wp_mentor_courses_categories'
        );
    }

    public function setup_fields() {
        add_settings_field(
            'mentor_courses_api_url',
            'API URL',
            array($this, 'field_callback'),
            'wp_mentor_courses_categories',
            'wp_mentor_courses_categories_section',
            array(
                'label_for' => 'mentor_courses_api_url',
                'type' => 'text',
                'option_name' => 'mentor_courses_api_url'
            )
        );

        register_setting('wp_mentor_courses_categories', 'mentor_courses_api_url');
    }

    public function field_callback($arguments) {
        $option_name = $arguments['option_name'];
        $value = get_option($option_name);
        echo '<input name="' . esc_attr($option_name) . '" id="' . esc_attr($option_name) . '" type="' . esc_attr($arguments['type']) . '" value="' . esc_attr($value) . '" />';
    }

    public function enqueue_styles() {
        wp_enqueue_style('wp_mentor_courses_categories_styles', plugin_dir_url(__FILE__) . 'output.css');
    }

    private function prepare_font_assets($fonts_payload) {
        $result = [
            'links' => [],
            'css_chunks' => [],
            'body_font' => '',
            'heading_font' => '',
        ];

        if (empty($fonts_payload)) {
            return $result;
        }

        if (is_string($fonts_payload)) {
            $this->assign_font_source_from_string($fonts_payload, $result);
            return $result;
        }

        if (is_object($fonts_payload)) {
            $fonts_payload = (array) $fonts_payload;
        }

        if (!is_array($fonts_payload)) {
            return $result;
        }

        foreach ($fonts_payload as $key => $entry) {
            $this->ingest_font_entry($entry, $result, $key);
        }

        $result['links'] = array_values(array_unique(array_filter($result['links'])));
        $result['css_chunks'] = array_values(array_unique(array_filter($result['css_chunks'])));

        return $result;
    }

    private function ingest_font_entry($entry, array &$result, $key = '') {
        if (is_null($entry)) {
            return;
        }

        if (is_object($entry)) {
            $entry = (array) $entry;
        }

        if (is_string($entry)) {
            $this->assign_font_source_from_string($entry, $result);
            return;
        }

        if (!is_array($entry)) {
            return;
        }

        if (isset($entry['url'])) {
            $this->assign_font_source_from_string($entry['url'], $result);
        }

        if (isset($entry['href'])) {
            $this->assign_font_source_from_string($entry['href'], $result);
        }

        if (isset($entry['src'])) {
            $this->ingest_font_entry($entry['src'], $result, $key);
        }

        if (isset($entry['css'])) {
            if (is_array($entry['css'])) {
                foreach ($entry['css'] as $snippet) {
                    $this->ingest_font_entry($snippet, $result, $key);
                }
            } else {
                $result['css_chunks'][] = (string) $entry['css'];
            }
        }

        if (isset($entry['fonts'])) {
            $this->ingest_font_entry($entry['fonts'], $result, $key);
        }

        if (isset($entry['family']) && is_string($entry['family'])) {
            $this->assign_font_family($result, $entry['family'], $key, $entry);
        } elseif (isset($entry['name']) && is_string($entry['name'])) {
            $this->assign_font_family($result, $entry['name'], $key, $entry);
        }
    }

    private function assign_font_source_from_string($font_string, array &$result) {
        if (!is_string($font_string)) {
            return;
        }

        $font_string = trim($font_string);
        if ($font_string === '') {
            return;
        }

        if (filter_var($font_string, FILTER_VALIDATE_URL)) {
            $result['links'][] = esc_url_raw($font_string);
        } else {
            $result['css_chunks'][] = $font_string;
        }
    }

    private function assign_font_family(array &$result, $family, $key = '', array $entry = []) {
        $family = sanitize_text_field($family);
        if ($family === '') {
            return;
        }

        $role_candidates = [];

        if (!empty($key) && is_string($key)) {
            $role_candidates[] = strtolower($key);
        }

        foreach (['role', 'type', 'usage', 'target'] as $role_key) {
            if (isset($entry[$role_key]) && is_string($entry[$role_key])) {
                $role_candidates[] = strtolower($entry[$role_key]);
            }
        }

        $role_candidates = array_unique($role_candidates);

        $body_roles = ['body', 'base', 'paragraph', 'default', 'text'];
        $heading_roles = ['heading', 'header', 'title', 'display'];

        $is_body = count(array_intersect($role_candidates, $body_roles)) > 0;
        $is_heading = count(array_intersect($role_candidates, $heading_roles)) > 0;

        if ($is_body && empty($result['body_font'])) {
            $result['body_font'] = $family;
        }

        if ($is_heading && empty($result['heading_font'])) {
            $result['heading_font'] = $family;
        }

        if (empty($result['body_font']) && !$is_heading) {
            $result['body_font'] = $family;
        } elseif (empty($result['heading_font']) && !$is_body) {
            $result['heading_font'] = $family;
        } elseif (empty($result['heading_font']) && $is_body) {
            $result['heading_font'] = $family;
        }
    }

    private function format_css_vars(array $vars) {
        if (empty($vars)) {
            return '';
        }

        $lines = [];
        foreach ($vars as $name => $value) {
            $lines[] = '  ' . $name . ': ' . $value . ';';
        }

        return implode("\n", $lines);
    }

    // Onderin je class WPMentorCoursesCategories (vlak voor sluitende accolade):

    private function build_font_face($font) {
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

    public function inject_theme_css() {
        if (empty($this->api_url)) {
            return;
        }

        $theme_url = trailingslashit($this->api_url) . 'api/client_api/frontendtheme/';
        $response = wp_remote_get($theme_url);
        if (is_wp_error($response)) {
            return;
        }

        $body = wp_remote_retrieve_body($response);
        if (empty($body)) {
            return;
        }

        $data = json_decode($body, true);
        if (!is_array($data)) {
            return;
        }

        // --- kleuren ---
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

        // --- fonts ---
        $font_styles = '';
        $body_font = '';
        $heading_font = '';

        if (!empty($data['fonts']['fonts']) && is_array($data['fonts']['fonts'])) {
            foreach ($data['fonts']['fonts'] as $font) {
                $font_styles .= $this->build_font_face($font) . "\n";

                // Body/heading detectie
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
public function display_trainingtracks($atts = []) {
    // shortcode-attributen
    $atts = shortcode_atts([
        'id' => '',
    ], $atts, 'display_trainingtracks');

    // basis endpoint
    $endpoint = '/api/client_api/availabletrainingtracks/';
    if (!empty($atts['id'])) {
        $endpoint .= '?module_id=' . urlencode($atts['id']);
    }

    // data ophalen
    $tracks = $this->fetch_data($endpoint);

    // als er geen geldige data is
    if (empty($tracks)) {
        return '<p>Geen trainingen gevonden.</p>';
    }

    return display_trainingtracks_template($tracks, $this->api_url);

    // fallback (als template niet bestaat)
    ob_start();
    foreach ($track_items as $track) {
        echo '<div>' . esc_html($track['title'] ?? 'Zonder titel') . '</div>';
    }
    return ob_get_clean();
}

    
}

new WPMentorCoursesCategories();
?>