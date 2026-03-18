<?php
/**
 * Mentor Plugin - Uninstall
 *
 * Ruimt alle plugin-data op bij deïnstallatie.
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Verwijder opties
delete_option('mentor_courses_api_url');
delete_option('mentor_theme_enabled');
delete_option('mentor_cache_duration');
delete_option('mentor_cache_last_refresh');
delete_option('mentor_detail_page_id');

// Verwijder alle transients
global $wpdb;
$wpdb->query(
    "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_mentor_%' OR option_name LIKE '_transient_timeout_mentor_%'"
);
