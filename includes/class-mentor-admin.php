<?php
defined('ABSPATH') or die('No script kiddies please!');

class MentorAdmin
{
    private $api;

    public function __construct(MentorApi $api)
    {
        $this->api = $api;

        add_action('admin_menu', array($this, 'create_settings_page'));
        add_action('admin_init', array($this, 'setup_sections'));
        add_action('admin_init', array($this, 'setup_fields'));
        add_action('admin_init', array($this, 'handle_clear_cache'));
        add_action('wp_ajax_mentor_get_modules', array($this, 'ajax_get_modules'));
        add_action('wp_ajax_mentor_preview_shortcode', array($this, 'ajax_preview_shortcode'));
    }

    // =========================================================================
    // Menu pagina's
    // =========================================================================

    public function create_settings_page()
    {
        add_menu_page(
            'Mentor Plugin',
            'Mentor Plugin',
            'edit_pages',
            'mentor_plugin',
            array($this, 'settings_page_content'),
            'dashicons-welcome-learn-more',
            80
        );

        add_submenu_page(
            'mentor_plugin',
            'Instellingen',
            'Instellingen',
            'manage_options',
            'mentor_plugin',
            array($this, 'settings_page_content')
        );

        add_submenu_page(
            'mentor_plugin',
            'Shortcode Builder',
            'Shortcode Builder',
            'edit_pages',
            'mentor_shortcode_builder',
            array($this, 'shortcode_builder_page')
        );
    }

    // =========================================================================
    // Instellingen
    // =========================================================================

    public function settings_page_content()
    {
        $cache_minutes = (int) get_option('mentor_cache_duration', 15);
        if ($cache_minutes < 1) $cache_minutes = 15;
        $last_refresh = get_option('mentor_cache_last_refresh', 0);
        ?>
        <div class="wrap">
            <h1>Mentor Plugin — Instellingen</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('wp_mentor_courses_categories');
                do_settings_sections('wp_mentor_courses_categories');
                submit_button();
                ?>
            </form>

            <hr>
            <h2>Cache</h2>
            <p>
                API-responses worden <?php echo esc_html($cache_minutes); ?> minuten gecached.
                <?php if ($last_refresh): ?>
                    <br>Laatst ververst: <strong><?php echo esc_html(date_i18n('j F Y, H:i', $last_refresh)); ?></strong>
                <?php endif; ?>
            </p>
            <form method="post">
                <?php wp_nonce_field('mentor_clear_cache', 'mentor_clear_cache_nonce'); ?>
                <input type="hidden" name="mentor_clear_cache" value="1">
                <?php submit_button('Cache legen', 'secondary', 'submit-clear-cache', false); ?>
            </form>
            <?php if (isset($_GET['cache_cleared']) && sanitize_text_field(wp_unslash($_GET['cache_cleared']))): // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
                <div class="notice notice-success is-dismissible"><p>Cache is geleegd.</p></div>
            <?php endif; ?>
        </div> <?php
    }

    public function setup_sections()
    {
        add_settings_section(
            'wp_mentor_courses_categories_section',
            'API Settings',
            null,
            'wp_mentor_courses_categories'
        );
    }

    public function setup_fields()
    {
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

        add_settings_field(
            'mentor_theme_enabled',
            'Klantthema overnemen (kleuren en lettertype)',
            array($this, 'checkbox_callback'),
            'wp_mentor_courses_categories',
            'wp_mentor_courses_categories_section',
            array(
                'label_for' => 'mentor_theme_enabled',
                'option_name' => 'mentor_theme_enabled'
            )
        );

        add_settings_field(
            'mentor_cache_duration',
            'Cache duur (minuten)',
            array($this, 'field_callback'),
            'wp_mentor_courses_categories',
            'wp_mentor_courses_categories_section',
            array(
                'label_for' => 'mentor_cache_duration',
                'type' => 'number',
                'option_name' => 'mentor_cache_duration'
            )
        );

        register_setting('wp_mentor_courses_categories', 'mentor_courses_api_url', [
            'sanitize_callback' => 'esc_url_raw',
        ]);
        register_setting('wp_mentor_courses_categories', 'mentor_theme_enabled', ['sanitize_callback' => 'absint']);
        register_setting('wp_mentor_courses_categories', 'mentor_cache_duration', [
            'type' => 'integer',
            'default' => 15,
            'sanitize_callback' => 'absint',
        ]);
    }

    public function checkbox_callback($arguments)
    {
        $option_name = $arguments['option_name'];
        $checked = checked(1, get_option($option_name), false);
        echo '<input type="checkbox"
             name="' . esc_attr($option_name) . '"
             id="' . esc_attr($option_name) . '"
             value="1" ' . $checked . ' />'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    public function field_callback($arguments)
    {
        $option_name = $arguments['option_name'];
        $value = get_option($option_name);
        echo '<input name="' . esc_attr($option_name) . '" id="' . esc_attr($option_name) . '" type="' . esc_attr($arguments['type']) . '" value="' . esc_attr($value) . '" />';
    }

    // =========================================================================
    // Cache beheer
    // =========================================================================

    public function handle_clear_cache()
    {
        if (
            empty($_POST['mentor_clear_cache']) ||
            !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['mentor_clear_cache_nonce'] ?? '')), 'mentor_clear_cache') ||
            !current_user_can('manage_options')
        ) {
            return;
        }

        global $wpdb;
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $wpdb->query(
            "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_mentor_%' OR option_name LIKE '_transient_timeout_mentor_%'"
        );
        delete_option('mentor_cache_last_refresh');

        wp_safe_redirect(add_query_arg('cache_cleared', '1', wp_get_referer()));
        exit;
    }

    // =========================================================================
    // AJAX endpoints
    // =========================================================================

    public function ajax_preview_shortcode()
    {
        check_ajax_referer('mentor_preview_shortcode');

        if (!current_user_can('edit_pages')) {
            wp_send_json_error('Geen toegang', 403);
        }

        $shortcode = sanitize_text_field(wp_unslash($_POST['shortcode'] ?? ''));
        if (empty($shortcode)) {
            wp_send_json_error('Geen shortcode opgegeven');
        }

        $theme = new MentorTheme($this->api);

        ob_start();
        wp_enqueue_style('mentor_plugin_styles', MENTOR_PLUGIN_URL . 'assets/css/mentor-plugin.css', [], MENTOR_PLUGIN_VERSION);
        wp_print_styles('mentor_plugin_styles');
        $theme->inject_theme_css();
        $html = do_shortcode($shortcode);
        $styles = ob_get_clean();

        wp_send_json_success($styles . $html);
    }

    public function ajax_get_modules()
    {
        check_ajax_referer('mentor_get_modules');

        if (!current_user_can('edit_pages')) {
            wp_send_json_error('Geen toegang', 403);
        }

        $courses = $this->api->fetch_data('/api/modules_api/catalog/');
        $modules = [];

        if (!empty($courses['results'])) {
            foreach ($courses['results'] as $course) {
                $modules[] = [
                    'id' => $course['id'] ?? '',
                    'title' => $course['title'] ?? '',
                ];
            }
        }

        wp_send_json_success($modules);
    }

    // =========================================================================
    // Shortcode Builder pagina
    // =========================================================================

    public function shortcode_builder_page()
    {
        ?>
        <div class="wrap">
            <h1>Mentor Shortcode Builder</h1>
            <p>Selecteer een shortcode type en configureer de opties. Kopieer de gegenereerde shortcode naar je pagina.</p>

            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="msb-type">Shortcode type</label></th>
                    <td>
                        <select id="msb-type" style="min-width: 320px;">
                            <option value="">— Kies een type —</option>
                            <optgroup label="Overzichten">
                                <option value="mentor_courses">Cursussen (kaartjes)</option>
                                <option value="mentor_categories">Categorieën</option>
                                <option value="display_coursegroup_wc">Trainingsmomenten (modal)</option>
                                <option value="mentor_startdata">Startdata (agenda)</option>
                            </optgroup>
                            <optgroup label="Cursusdetailpagina">
                                <option value="mentor_cursus_detail">Compleet (alles in één)</option>
                                <option value="mentor_cursus_titel">Titel</option>
                                <option value="mentor_cursus_thema">Thema</option>
                                <option value="mentor_cursus_afbeelding">Afbeelding</option>
                                <option value="mentor_cursus_prijs">Prijs</option>
                                <option value="mentor_cursus_omschrijving">Omschrijving</option>
                                <option value="mentor_cursus_docenten">Docenten</option>
                                <option value="mentor_cursus_inschrijven">Inschrijfknop</option>
                                <option value="mentor_cursus_reviews">Reviews</option>
                            </optgroup>
                            <optgroup label="Reviews">
                                <option value="mentor_reviews">Reviews (standalone)</option>
                            </optgroup>
                        </select>
                        <p class="description" id="msb-type-desc"></p>
                    </td>
                </tr>
                <tr id="msb-module-row" style="display: none;">
                    <th scope="row"><label for="msb-module">Cursus / Module</label></th>
                    <td>
                        <select id="msb-module" style="min-width: 320px;">
                            <option value="">Laden...</option>
                        </select>
                        <p class="description">Selecteer de cursus waarvoor je de trainingsmomenten wilt tonen.</p>
                    </td>
                </tr>
            </table>

            <div id="msb-result" style="display: none; margin-top: 24px;">
                <h2>Gegenereerde shortcode</h2>
                <div style="display: flex; align-items: center; gap: 12px; margin-top: 8px;">
                    <code id="msb-output" style="display: inline-block; padding: 12px 20px; background: #f0f0f1; border: 1px solid #c3c4c7; border-radius: 4px; font-size: 15px; user-select: all;"></code>
                    <button type="button" id="msb-copy" class="button button-primary">
                        Kopieer naar klembord
                    </button>
                    <span id="msb-copied" style="display: none; color: #00a32a; font-weight: 600;">Gekopieerd!</span>
                </div>

                <div id="msb-preview-wrap" style="margin-top: 32px;">
                    <h2>Voorbeeld</h2>
                    <p class="description">Dit is een live voorbeeld van hoe de shortcode eruit ziet op de website.</p>
                    <div style="margin-top: 12px; border: 1px solid #c3c4c7; border-radius: 4px; padding: 20px; background: #fff; max-height: 600px; overflow: auto;">
                        <div id="msb-preview">Laden...</div>
                    </div>
                </div>
            </div>
        </div>

        <script>
        (function() {
            var typeSelect = document.getElementById('msb-type');
            var moduleRow = document.getElementById('msb-module-row');
            var moduleSelect = document.getElementById('msb-module');
            var resultDiv = document.getElementById('msb-result');
            var outputCode = document.getElementById('msb-output');
            var copyBtn = document.getElementById('msb-copy');
            var copiedMsg = document.getElementById('msb-copied');
            var typeDesc = document.getElementById('msb-type-desc');
            var previewDiv = document.getElementById('msb-preview');
            var previewWrap = document.getElementById('msb-preview-wrap');
            var modulesLoaded = false;

            var descriptions = {
                'mentor_courses': 'Toont alle cursussen als kaartjes met afbeelding, beschrijving, prijs en een link.',
                'mentor_categories': 'Toont alle categorieën als kaartjes met een link naar de categoriepagina.',
                'display_coursegroup_wc': 'Toont beschikbare trainingsmomenten voor een specifieke cursus, met een modal voor de dagplanning.',
                'mentor_startdata': 'Toont een startdata-agenda voor een specifieke cursus, met locatie- en beschikbaarheidsfilters.',
                'mentor_cursus_detail': 'Toont een complete cursusdetailpagina met beschrijving, prijs, docenten en startdata.',
                'mentor_cursus_titel': 'Toont alleen de cursustitel. Gebruik op een detailpagina.',
                'mentor_cursus_thema': 'Toont het thema/onderwerp van de cursus.',
                'mentor_cursus_afbeelding': 'Toont de cursusafbeelding.',
                'mentor_cursus_prijs': 'Toont de prijs (incl. totaalprijs en eventuele korting).',
                'mentor_cursus_omschrijving': 'Toont de volledige cursusbeschrijving.',
                'mentor_cursus_docenten': 'Toont docentenkaartjes met foto, bio en expertise.',
                'mentor_cursus_inschrijven': 'Toont een inschrijfknop die naar Mentor linkt.',
                'mentor_cursus_reviews': 'Toont reviews en beoordelingen voor deze cursus.',
                'mentor_reviews': 'Toont reviews en beoordelingen. Filter optioneel op cursus.'
            };

            var needsModule = ['display_coursegroup_wc', 'mentor_startdata', 'mentor_cursus_detail', 'mentor_cursus_titel', 'mentor_cursus_thema', 'mentor_cursus_afbeelding', 'mentor_cursus_prijs', 'mentor_cursus_omschrijving', 'mentor_cursus_docenten', 'mentor_cursus_inschrijven', 'mentor_cursus_reviews', 'mentor_reviews'];

            function loadModules() {
                if (modulesLoaded) return;
                modulesLoaded = true;

                moduleSelect.innerHTML = '<option value="">Laden...</option>';

                var xhr = new XMLHttpRequest();
                xhr.open('POST', '<?php echo esc_url(admin_url('admin-ajax.php')); ?>');
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    var resp = JSON.parse(xhr.responseText);
                    if (resp.success && resp.data.length) {
                        moduleSelect.innerHTML = '<option value="">— Kies een cursus —</option>';
                        resp.data.forEach(function(m) {
                            var opt = document.createElement('option');
                            opt.value = m.id;
                            opt.textContent = m.title + ' (ID: ' + m.id + ')';
                            moduleSelect.appendChild(opt);
                        });
                    } else {
                        moduleSelect.innerHTML = '<option value="">Geen cursussen gevonden</option>';
                    }
                };
                xhr.send('action=mentor_get_modules&_ajax_nonce=<?php echo esc_attr(wp_create_nonce('mentor_get_modules')); ?>');
            }

            function buildShortcode() {
                var type = typeSelect.value;
                if (!type) {
                    resultDiv.style.display = 'none';
                    return;
                }

                var shortcode = '[' + type;

                if (needsModule.indexOf(type) !== -1) {
                    var moduleId = moduleSelect.value;
                    if (!moduleId) {
                        resultDiv.style.display = 'none';
                        return;
                    }
                    shortcode += ' id=' + moduleId;
                }

                shortcode += ']';

                outputCode.textContent = shortcode;
                resultDiv.style.display = '';
                copiedMsg.style.display = 'none';

                previewDiv.innerHTML = '<em>Voorbeeld laden...</em>';
                previewWrap.style.display = '';

                var pxhr = new XMLHttpRequest();
                pxhr.open('POST', '<?php echo esc_url(admin_url('admin-ajax.php')); ?>');
                pxhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                pxhr.onload = function() {
                    var resp = JSON.parse(pxhr.responseText);
                    if (resp.success) {
                        previewDiv.innerHTML = resp.data;
                    } else {
                        previewDiv.innerHTML = '<em>Preview kon niet geladen worden.</em>';
                    }
                };
                pxhr.onerror = function() {
                    previewDiv.innerHTML = '<em>Preview kon niet geladen worden.</em>';
                };
                pxhr.send('action=mentor_preview_shortcode&shortcode=' + encodeURIComponent(shortcode) + '&_ajax_nonce=<?php echo esc_attr(wp_create_nonce('mentor_preview_shortcode')); ?>');
            }

            typeSelect.addEventListener('change', function() {
                var type = this.value;
                typeDesc.textContent = descriptions[type] || '';

                if (needsModule.indexOf(type) !== -1) {
                    moduleRow.style.display = '';
                    loadModules();
                } else {
                    moduleRow.style.display = 'none';
                }

                buildShortcode();
            });

            moduleSelect.addEventListener('change', buildShortcode);

            copyBtn.addEventListener('click', function() {
                navigator.clipboard.writeText(outputCode.textContent).then(function() {
                    copiedMsg.style.display = '';
                    setTimeout(function() { copiedMsg.style.display = 'none'; }, 2000);
                });
            });
        })();
        </script>
        <?php
    }
}
