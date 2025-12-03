<?php
// Prevent direct access
defined('ABSPATH') or die('No script kiddies please!');

function display_course_template($courses, $api_url) {
    ob_start();
    include plugin_dir_path(__FILE__) . 'course-template.php';
    return ob_get_clean();
}

function display_template($template_name, $context) {
    ob_start();
    include plugin_dir_path(__FILE__) . $template_name;
    return ob_get_clean();
}

function display_category_template($categories, $api_url) {
    ob_start();
    include plugin_dir_path(__FILE__) . 'category-template.php';
    return ob_get_clean();
}

function display_trainingtracks_template($tracks, $api_url) {
    ob_start();
    include plugin_dir_path(__FILE__) . 'trainingtrack-template.php';
    return ob_get_clean();
}
?>
