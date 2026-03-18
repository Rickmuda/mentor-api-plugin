<?php
defined('ABSPATH') or die('No script kiddies please!');

class MentorShortcodes
{
    private $api;
    private $current_course = null;
    private $current_course_id = null;

    public function __construct(MentorApi $api)
    {
        $this->api = $api;

        add_shortcode('mentor_courses', array($this, 'display_courses'));
        add_shortcode('mentor_categories', array($this, 'display_categories'));
        add_shortcode('display_coursegroup_wc', array($this, 'display_trainingtracks'));
        add_shortcode('mentor_startdata', array($this, 'display_startdata'));
        add_shortcode('mentor_cursus_detail', array($this, 'display_cursus_detail'));
        add_shortcode('mentor_cursus_titel', array($this, 'display_cursus_field'));
        add_shortcode('mentor_cursus_prijs', array($this, 'display_cursus_field'));
        add_shortcode('mentor_cursus_omschrijving', array($this, 'display_cursus_field'));
        add_shortcode('mentor_cursus_afbeelding', array($this, 'display_cursus_field'));
        add_shortcode('mentor_cursus_thema', array($this, 'display_cursus_field'));
        add_shortcode('mentor_cursus_docenten', array($this, 'display_cursus_field'));
        add_shortcode('mentor_cursus_inschrijven', array($this, 'display_cursus_field'));
        add_shortcode('mentor_cursus_reviews', array($this, 'display_cursus_field'));
        add_shortcode('mentor_reviews', array($this, 'display_reviews'));
    }

    public function display_courses()
    {
        $error = $this->api->check_api_url();
        if ($error !== false) return $error;

        $courses = $this->api->fetch_data('/api/modules_api/catalog/');
        if (empty($courses)) {
            return '<p>Geen cursussen gevonden.</p>';
        }

        $review_stats = $this->api->get_course_review_stats();

        return display_course_template($courses, $this->api->get_api_url(), $review_stats);
    }

    public function display_categories()
    {
        $error = $this->api->check_api_url();
        if ($error !== false) return $error;

        $categories = $this->api->fetch_data('/api/modules_api/subjects/');
        if (empty($categories)) {
            return '<p>Geen categorieën gevonden.</p>';
        }

        return display_category_template($categories, $this->api->get_api_url());
    }

    public function display_trainingtracks($atts = [])
    {
        $error = $this->api->check_api_url();
        if ($error !== false) return $error;

        $atts = shortcode_atts([
            'id' => '',
        ], $atts, 'display_trainingtracks');

        $endpoint = '/api/client_api/availabletrainingtracks/';
        if (!empty($atts['id'])) {
            $endpoint .= '?module_id=' . absint($atts['id']);
        }

        $tracks = $this->api->fetch_data($endpoint);

        return display_trainingtracks_template($tracks, $this->api->get_api_url());
    }

    public function display_startdata($atts = [])
    {
        $error = $this->api->check_api_url();
        if ($error !== false) return $error;

        $atts = shortcode_atts([
            'id' => '',
        ], $atts, 'mentor_startdata');

        $endpoint = '/api/client_api/availabletrainingtracks/';
        if (!empty($atts['id'])) {
            $endpoint .= '?module_id=' . absint($atts['id']);
        }

        $tracks = $this->api->fetch_data($endpoint);

        return display_startdata_template($tracks, $this->api->get_api_url());
    }

    public function display_reviews($atts = [])
    {
        $error = $this->api->check_api_url();
        if ($error !== false) return $error;

        $atts = shortcode_atts([
            'id' => '',
        ], $atts, 'mentor_reviews');

        $module_id = absint($atts['id']);
        $review_data = $this->api->fetch_review_data('/reviews/', $module_id);
        $reviews = $review_data['results'] ?? $review_data;
        $aggregate = $this->api->fetch_review_data('/reviews/aggregate/', $module_id);

        if (empty($reviews) || empty($aggregate)) {
            return '';
        }

        return display_reviews_template($reviews, $aggregate, $module_id);
    }

    public function display_cursus_detail($atts = [])
    {
        $error = $this->api->check_api_url();
        if ($error !== false) return $error;

        $atts = shortcode_atts([
            'id' => '',
        ], $atts, 'mentor_cursus_detail');

        $module_id = $atts['id'];
        if (empty($module_id)) {
            $module_id = isset($_GET['cursus_id']) ? absint($_GET['cursus_id']) : '';
        }

        if (empty($module_id)) {
            return '<p>Geen cursus geselecteerd.</p>';
        }

        $course = $this->api->fetch_data('/api/modules_api/catalog/' . $module_id . '/');
        if (empty($course)) {
            return '<p>Cursus niet gevonden.</p>';
        }

        $tracks = $this->api->fetch_data('/api/client_api/availabletrainingtracks/?module_id=' . $module_id);
        $track_items = $tracks['results'] ?? $tracks ?? [];

        $teachers = [];
        $teacher_ids = [];
        if (is_array($track_items)) {
            foreach ($track_items as $track) {
                foreach ($track['teachers'] ?? [] as $teacher) {
                    $tid = $teacher['id'] ?? 0;
                    if ($tid && !in_array($tid, $teacher_ids)) {
                        $teacher_ids[] = $tid;
                        $teachers[] = $teacher;
                    }
                }
            }
        }
        $course['teachers'] = $teachers;

        $api_url = $this->api->get_api_url();
        $output = display_cursus_detail_template($course, $tracks, $api_url);

        if (!empty($track_items)) {
            $output .= display_startdata_template($tracks, $api_url);
        }

        $review_data = $this->api->fetch_review_data('/reviews/', $module_id);
        $reviews = $review_data['results'] ?? $review_data;
        $aggregate = $this->api->fetch_review_data('/reviews/aggregate/', $module_id);
        if (!empty($reviews) && !empty($aggregate)) {
            $output .= display_reviews_template($reviews, $aggregate, $module_id);
        }

        return $output;
    }

    // =========================================================================
    // Losse cursus-veld shortcodes
    // =========================================================================

    private function get_current_course($atts)
    {
        $atts = shortcode_atts(['id' => ''], $atts);

        $module_id = $atts['id'];
        if (empty($module_id)) {
            $module_id = isset($_GET['cursus_id']) ? absint($_GET['cursus_id']) : '';
        }

        if (empty($module_id)) {
            return null;
        }

        if ($this->current_course_id === $module_id && $this->current_course !== null) {
            return $this->current_course;
        }

        $course = $this->api->fetch_data('/api/modules_api/catalog/' . $module_id . '/');
        if (empty($course)) {
            return null;
        }

        $tracks = $this->api->fetch_data('/api/client_api/availabletrainingtracks/?module_id=' . $module_id);
        $track_items = $tracks['results'] ?? $tracks ?? [];
        $teachers = [];
        $teacher_ids = [];
        if (is_array($track_items)) {
            foreach ($track_items as $track) {
                foreach ($track['teachers'] ?? [] as $teacher) {
                    $tid = $teacher['id'] ?? 0;
                    if ($tid && !in_array($tid, $teacher_ids)) {
                        $teacher_ids[] = $tid;
                        $teachers[] = $teacher;
                    }
                }
            }
        }
        $course['teachers'] = $teachers;
        $course['_tracks'] = $tracks;

        $this->current_course = $course;
        $this->current_course_id = $module_id;

        return $course;
    }

    public function display_cursus_field($atts, $content, $tag)
    {
        $error = $this->api->check_api_url();
        if ($error !== false) return $error;

        $course = $this->get_current_course($atts);
        if (!$course) {
            return '';
        }

        $api_url = $this->api->get_api_url();

        switch ($tag) {
            case 'mentor_cursus_titel':
                return '<h1 style="font-size: 2rem; font-weight: 800; color: var(--color-body-text, #1f2937); line-height: 1.2;">' . esc_html($course['title'] ?? '') . '</h1>';

            case 'mentor_cursus_prijs':
                $price = $course['price'] ?? '';
                $total = $course['total_price'] ?? '';
                $discount = $course['discount'] ?? [];
                $disc_perc = $discount['discount_perc'] ?? 0;
                $disc_name = $discount['discount_name'] ?? '';
                $incl_vat = $course['show_prices_including_vat'] ?? false;

                $price_num = (float) str_replace(['.', ','], ['', '.'], $price);
                $total_num = (float) str_replace(['.', ','], ['', '.'], $total);
                if ($price_num <= 0 && $total_num <= 0) return '';

                $html = '<div style="background: #f9fafb; border-radius: 12px; padding: 16px 20px; display: inline-block;">';
                $html .= '<div style="font-size: 12px; color: #9ca3af; margin-bottom: 4px;">Cursusprijs</div>';
                $html .= '<div style="font-size: 1.35rem; font-weight: 700; color: var(--color-body-text, #1f2937); line-height: 1.2;">&euro;' . esc_html($price) . '</div>';
                if ($incl_vat) {
                    $html .= '<div style="font-size: 13px; color: #9ca3af; margin-top: 2px;">incl. btw</div>';
                }
                if (!empty($total) && $total !== $price) {
                    $html .= '<div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #e5e7eb;">';
                    $html .= '<div style="font-size: 12px; color: #9ca3af;">Totaal incl. materiaal: <strong style="color: var(--color-body-text, #1f2937);">&euro;' . esc_html($total) . '</strong></div>';
                    $html .= '</div>';
                }
                if ($disc_perc > 0) {
                    $html .= '<div style="margin-top: 8px;"><span style="display: inline-block; background: #dcfce7; color: #166534; font-size: 12px; font-weight: 600; padding: 3px 10px; border-radius: 9999px;">' . esc_html($disc_name ?: $disc_perc . '% korting') . '</span></div>';
                }
                $html .= '</div>';
                return $html;

            case 'mentor_cursus_omschrijving':
                return '<div style="font-size: 15px; line-height: 1.7; color: #4b5563;">' . wp_kses_post($course['description'] ?? '') . '</div>';

            case 'mentor_cursus_afbeelding':
                $img = $course['image'] ?? ($course['image_card_medium'] ?? '');
                if (empty($img)) return '';
                return '<img src="' . esc_url($img) . '" alt="' . esc_attr($course['title'] ?? '') . '" style="width: 100%; border-radius: 16px; object-fit: cover; max-height: 400px;">';

            case 'mentor_cursus_thema':
                $subject = $course['subject']['title'] ?? '';
                if (empty($subject)) return '';
                return '<span style="font-size: 13px; font-weight: 600; color: var(--color-primary, #417AB3); text-transform: uppercase; letter-spacing: 0.5px;">' . esc_html($subject) . '</span>';

            case 'mentor_cursus_docenten':
                $teachers = $course['teachers'] ?? [];
                if (empty($teachers)) return '';
                ob_start();
                include MENTOR_PLUGIN_DIR . 'templates/cursus-docenten.php';
                return ob_get_clean();

            case 'mentor_cursus_inschrijven':
                $link = add_query_arg('startenrolment', '1', $course['link_to_mentor'] ?? '#');
                return '<a href="' . esc_url($link) . '" style="display: inline-flex; align-items: center; padding: 14px 32px; border-radius: 9999px; font-size: 15px; font-weight: 700; color: #fff; text-decoration: none; background-color: var(--color-primary, #417AB3); transition: opacity 0.2s;" onmouseover="this.style.opacity=\'0.9\'" onmouseout="this.style.opacity=\'1\'">Inschrijven <svg style="width:18px;height:18px;margin-left:10px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg></a>';

            case 'mentor_cursus_reviews':
                $mid = $this->current_course_id;
                if (!$mid) return '';
                $rev_data = $this->api->fetch_review_data('/reviews/', $mid);
                $rev = $rev_data['results'] ?? $rev_data;
                $agg = $this->api->fetch_review_data('/reviews/aggregate/', $mid);
                if (empty($rev) || empty($agg)) return '';
                return display_reviews_template($rev, $agg, $mid);

            default:
                return '';
        }
    }
}
