<?php
// Prevent direct access
defined('ABSPATH') or die('No script kiddies please!');

function mentor_render_stars($rating, $size = 16) {
    $html = '<span style="display: inline-flex; gap: 2px; vertical-align: middle;">';
    $rounded = round($rating);
    for ($i = 1; $i <= 5; $i++) {
        $fill = $i <= $rounded ? '#f59e0b' : '#d1d5db';
        $html .= '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="' . $fill . '"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
    }
    $html .= '</span>';
    return $html;
}

function mentor_display_course_template($courses, $api_url, $review_stats = []) {
    ob_start();
    include MENTOR_PLUGIN_DIR . 'templates/course.php';
    return ob_get_clean();
}

function mentor_display_category_template($categories, $api_url) {
    ob_start();
    include MENTOR_PLUGIN_DIR . 'templates/category.php';
    return ob_get_clean();
}

function mentor_display_trainingtracks_template($tracks, $api_url) {
    ob_start();
    include MENTOR_PLUGIN_DIR . 'templates/trainingtrack.php';
    return ob_get_clean();
}

function mentor_display_startdata_template($tracks, $api_url) {
    ob_start();
    include MENTOR_PLUGIN_DIR . 'templates/startdata.php';
    return ob_get_clean();
}

function mentor_display_cursus_detail_template($course, $tracks, $api_url) {
    ob_start();
    include MENTOR_PLUGIN_DIR . 'templates/cursus-detail.php';
    return ob_get_clean();
}

function mentor_display_reviews_template($reviews, $aggregate, $module_id = 0) {
    ob_start();
    include MENTOR_PLUGIN_DIR . 'templates/reviews.php';
    return ob_get_clean();
}
?>
