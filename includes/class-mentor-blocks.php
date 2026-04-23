<?php
defined('ABSPATH') or die('No script kiddies please!');

class MentorBlocks
{
    public function __construct()
    {
        add_action('init', [$this, 'register_blocks']);
        add_filter('block_categories_all', [$this, 'register_category'], 10, 2);
        add_action('enqueue_block_editor_assets', [$this, 'enqueue_editor_assets']);
    }

    public function register_category($categories, $context)
    {
        return array_merge(
            [[
                'slug'  => 'mentor',
                'title' => __('Mentor', 'mentor-integration'),
            ]],
            $categories
        );
    }

    public function enqueue_editor_assets()
    {
        $js_path = MENTOR_PLUGIN_DIR . 'assets/js/blocks.js';
        $js_version = file_exists($js_path) ? filemtime($js_path) : MENTOR_PLUGIN_VERSION;
        wp_enqueue_script(
            'mentor-blocks',
            MENTOR_PLUGIN_URL . 'assets/js/blocks.js',
            ['wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-server-side-render', 'wp-i18n'],
            $js_version,
            true
        );

        $css_path = MENTOR_PLUGIN_DIR . 'assets/css/mentor-plugin.css';
        $css_version = file_exists($css_path) ? filemtime($css_path) : MENTOR_PLUGIN_VERSION;
        wp_enqueue_style(
            'mentor_plugin_styles',
            MENTOR_PLUGIN_URL . 'assets/css/mentor-plugin.css',
            [],
            $css_version
        );
    }

    public function register_blocks()
    {
        $id_attr = ['id' => ['type' => 'string', 'default' => '']];

        $blocks = [
            'courses' => [
                'attributes' => [],
                'render'     => function () {
                    return do_shortcode('[mentor_courses]');
                },
            ],
            'categories' => [
                'attributes' => [],
                'render'     => function () {
                    return do_shortcode('[mentor_categories]');
                },
            ],
            'trainingtracks' => [
                'attributes' => $id_attr,
                'render'     => function ($attrs) {
                    return do_shortcode('[display_coursegroup_wc' . $this->id_arg($attrs) . ']');
                },
            ],
            'startdata' => [
                'attributes' => $id_attr,
                'render'     => function ($attrs) {
                    return do_shortcode('[mentor_startdata' . $this->id_arg($attrs) . ']');
                },
            ],
            'reviews' => [
                'attributes' => $id_attr,
                'render'     => function ($attrs) {
                    $html = do_shortcode('[mentor_reviews' . $this->id_arg($attrs) . ']');
                    if (trim($html) === '' && defined('REST_REQUEST') && REST_REQUEST) {
                        return '<div style="padding:16px;background:#f0f0f1;border:1px dashed #c3c4c7;border-radius:4px;color:#50575e;font-style:italic;">Geen reviews voor deze cursus — op de live pagina wordt dit block automatisch verborgen.</div>';
                    }
                    return $html;
                },
            ],
            'cursus-detail' => [
                'attributes' => $id_attr,
                'render'     => function ($attrs) {
                    return do_shortcode('[mentor_cursus_detail' . $this->id_arg($attrs) . ']');
                },
            ],
            'cursus-field' => [
                'attributes' => array_merge($id_attr, [
                    'field' => ['type' => 'string', 'default' => 'titel'],
                ]),
                'render' => function ($attrs) {
                    $allowed = ['titel', 'prijs', 'omschrijving', 'afbeelding', 'thema', 'docenten', 'inschrijven', 'reviews'];
                    $field = isset($attrs['field']) && in_array($attrs['field'], $allowed, true) ? $attrs['field'] : 'titel';
                    return do_shortcode('[mentor_cursus_' . $field . $this->id_arg($attrs) . ']');
                },
            ],
        ];

        foreach ($blocks as $name => $config) {
            register_block_type('mentor/' . $name, [
                'attributes'      => $config['attributes'],
                'render_callback' => $config['render'],
            ]);
        }
    }

    private function id_arg($attrs)
    {
        if (empty($attrs['id'])) {
            return '';
        }
        return ' id="' . esc_attr($attrs['id']) . '"';
    }
}
