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

// Onderin je class WPMentorCoursesCategories (vlak voor sluitende accolade):

public function inject_theme_css() {
    // Haal stylinggegevens van jouw frontend API op
    $theme_url = $this->api_url . '/api/client_api/frontendtheme/';
    $response = wp_remote_get($theme_url);
    if (is_wp_error($response)) return;
    $body = wp_remote_retrieve_body($response);
    if (empty($body)) return;

    $data = json_decode($body, true);
    if (!is_array($data) || !isset($data['css'])) return;

    $css = $data['css'];
    echo '<style id="mentor-theme-css">
      .tw {
        --color-primary: ' . esc_attr($css['primary-color'] ?? '#417AB3') . ';
        --color-secondary: ' . esc_attr($css['secondary-color'] ?? '#F6A623') . ';
        --color-secondary-hover: ' . esc_attr($css['secondary-color-hover'] ?? 'rgba(246,166,35,0.8)') . ';
        --color-body-text: ' . esc_attr($css['body-text-color'] ?? '#1D4065') . ';
      }
      </style>';
}
}

new WPMentorCoursesCategories();
?>