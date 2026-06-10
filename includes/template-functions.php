<?php
// Prevent direct access
defined('ABSPATH') or die('No script kiddies please!');

function mentor_get_cta_label() {
    $label = trim((string) get_option('mentor_cta_label', ''));
    return $label !== '' ? $label : 'Meer info';
}

function mentor_prices_hidden() {
    return (bool) (int) get_option('mentor_hide_prices', 0);
}

function mentor_get_course_price($course) {
    if (mentor_prices_hidden()) {
        return null;
    }
    return [
        'display' => $course['total_price'] ?? $course['price'] ?? '',
        'vat_label' => !empty($course['show_prices_including_vat']) ? 'incl. BTW' : 'excl. BTW',
    ];
}

function mentor_resolve_course_link($course) {
    $course_id = (int) ($course['id'] ?? 0);

    $overrides = get_option('mentor_course_link_overrides', []);
    if (is_array($overrides) && $course_id && !empty($overrides[$course_id])) {
        return $overrides[$course_id];
    }

    $detail_page_id = (int) get_option('mentor_detail_page_id', 0);
    if ($detail_page_id && $course_id) {
        return add_query_arg('cursus_id', $course_id, get_permalink($detail_page_id));
    }

    return $course['link_to_mentor'] ?? '';
}

/**
 * Geeft de per-cursus website-URL terug (de eigen site/skin van de cursus),
 * of een lege string wanneer er nog geen is ingesteld.
 *
 * Volgorde: override-optie mentor_course_website_overrides (per cursus-id) ->
 * eventueel een 'website'-veld uit de API-data -> leeg.
 */
function mentor_get_course_website($course) {
    $course_id = (int) ($course['id'] ?? 0);

    $overrides = get_option('mentor_course_website_overrides', []);
    if (is_array($overrides) && $course_id && !empty($overrides[$course_id])) {
        return $overrides[$course_id];
    }

    if (!empty($course['website'])) {
        return $course['website'];
    }

    return '';
}

function mentor_render_stars($rating, $size = 16, $color = '#f59e0b') {
    $fill_active = preg_match('/^#[0-9a-fA-F]{3,8}$/', (string) $color) ? $color : '#f59e0b';
    $html = '<span style="display: inline-flex; gap: 2px; vertical-align: middle;">';
    $rounded = round($rating);
    for ($i = 1; $i <= 5; $i++) {
        $fill = $i <= $rounded ? $fill_active : '#d1d5db';
        $html .= '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="' . esc_attr($fill) . '"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
    }
    $html .= '</span>';
    return $html;
}

/**
 * Bepaalt welk template-bestand geladen wordt op basis van de actieve skin.
 *
 * Volgorde: expliciet $style-argument (shortcode-attribuut) -> globale optie
 * mentor_active_style -> standaard-template. Valt terug op het standaard-bestand
 * wanneer de skin geen eigen versie van dit template heeft.
 */
function mentor_resolve_template($name, $style = '') {
    $style = $style !== '' ? $style : (string) get_option('mentor_active_style', '');
    $style = preg_replace('/[^a-z0-9_-]/', '', strtolower((string) $style)); // path-traversal-veilig
    if ($style !== '') {
        $skin = MENTOR_PLUGIN_DIR . 'templates/skins/' . $style . '/' . $name . '.php';
        if (file_exists($skin)) {
            return $skin;
        }
    }
    return MENTOR_PLUGIN_DIR . 'templates/' . $name . '.php';
}

/**
 * Lijst van beschikbare skins (mapnamen onder templates/skins/), voor de admin-dropdown.
 */
function mentor_available_styles() {
    $out = [];
    foreach (glob(MENTOR_PLUGIN_DIR . 'templates/skins/*', GLOB_ONLYDIR) ?: [] as $dir) {
        $out[] = basename($dir);
    }
    return $out;
}

/**
 * Sanitizer voor de mentor_active_style-optie: alleen bestaande skin-slugs toegestaan.
 */
function mentor_sanitize_style($value) {
    $value = preg_replace('/[^a-z0-9_-]/', '', strtolower((string) $value));
    return in_array($value, mentor_available_styles(), true) ? $value : '';
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

function mentor_display_startdata_template($tracks, $api_url, $style = '') {
    ob_start();
    include mentor_resolve_template('startdata', $style);
    return ob_get_clean();
}

function mentor_display_cursus_detail_template($course, $tracks, $api_url, $style = '') {
    ob_start();
    include mentor_resolve_template('cursus-detail', $style);
    return ob_get_clean();
}

function mentor_display_reviews_template($reviews, $aggregate, $module_id = 0, $style = '') {
    ob_start();
    include mentor_resolve_template('reviews', $style);
    return ob_get_clean();
}
?>
