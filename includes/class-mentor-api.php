<?php
defined('ABSPATH') or die('No script kiddies please!');

class MentorApi
{
    private $api_url;

    public function __construct()
    {
        $this->api_url = get_option('mentor_courses_api_url');
    }

    public function get_api_url()
    {
        return $this->api_url;
    }

    public function check_api_url()
    {
        if (empty($this->api_url)) {
            if (current_user_can('manage_options')) {
                return '<p style="color:#dc2626;"><strong>Mentor Plugin:</strong> Stel eerst de API URL in via <a href="' . admin_url('admin.php?page=mentor_plugin') . '">Mentor Plugin → Instellingen</a>.</p>';
            }
            return '';
        }
        return false;
    }

    public function fetch_data($endpoint)
    {
        $cache_key = 'mentor_' . md5($this->api_url . $endpoint);
        $cached = get_transient($cache_key);

        if ($cached !== false) {
            return $cached;
        }

        $response = wp_remote_get($this->api_url . $endpoint, [
            'timeout' => 30,
        ]);

        if (is_wp_error($response)) {
            return [];
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (!is_array($data)) {
            return [];
        }

        if (!empty($data)) {
            $cache_minutes = (int) get_option('mentor_cache_duration', 15);
            if ($cache_minutes < 1) $cache_minutes = 15;
            set_transient($cache_key, $data, $cache_minutes * MINUTE_IN_SECONDS);
            update_option('mentor_cache_last_refresh', time());
        }

        return $data;
    }

    public function fetch_review_data($path, $module_id = 0)
    {
        $org_id = get_option('mentor_org_id');
        $api_key = get_option('mentor_review_api_key');

        if (empty($org_id) || empty($api_key) || empty($this->api_url)) {
            return [];
        }

        $params = ['org' => $org_id, 'key' => $api_key];
        if ($module_id) {
            $params['module'] = $module_id;
        }

        $endpoint = '/review/api/v1/public' . $path . '?' . http_build_query($params);
        return $this->fetch_data($endpoint);
    }

    public function get_course_review_stats()
    {
        $data = $this->fetch_review_data('/reviews/');
        $reviews = $data['results'] ?? $data;
        if (empty($reviews) || !is_array($reviews)) {
            return [];
        }

        $stats = [];
        foreach ($reviews as $review) {
            $mid = $review['module'] ?? null;
            if (!$mid) continue;
            if (!isset($stats[$mid])) {
                $stats[$mid] = ['sum' => 0, 'count' => 0];
            }
            $stats[$mid]['count']++;
            $stats[$mid]['sum'] += $review['overall_rating'] ?? 0;
        }

        foreach ($stats as $mid => &$s) {
            $s['average'] = round($s['sum'] / $s['count'], 1);
        }

        return $stats;
    }
}
