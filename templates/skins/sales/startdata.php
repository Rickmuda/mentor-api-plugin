<?php
/**
 * Skin: sales - Startdata-agenda ("Sales-cockpit", data-dashboard-thema)
 * Verwacht dezelfde variabelen als templates/startdata.php: $tracks, $api_url
 */
defined('ABSPATH') or die('No script kiddies please!');

$items = $tracks['results'] ?? $tracks;

if (empty($items) || !is_array($items)) {
    echo '<p>Geen startdata gevonden.</p>';
    return;
}

if (!wp_style_is('mentor-sales-fonts', 'enqueued')) {
    wp_enqueue_style(
        'mentor-sales-fonts',
        'https://fonts.googleapis.com/css2?family=Sora:wght@500;600;700;800&family=Inter:wght@400;500;600&family=IBM+Plex+Mono:wght@500;600&display=swap',
        array(),
        null
    );
}

usort($items, function ($a, $b) {
    return strcmp($a['training_start_raw'] ?? '', $b['training_start_raw'] ?? '');
});

$locations = [];
foreach ($items as $item) {
    $loc = $item['default_location']['label'] ?? ($item['traininglessons'][0]['location_label_no_room'] ?? '');
    if (!empty($loc) && !in_array($loc, $locations)) {
        $locations[] = $loc;
    }
}
sort($locations);

$grouped = [];
foreach ($items as $item) {
    $raw = $item['training_start_raw'] ?? '';
    $year = $raw ? gmdate('Y', strtotime($raw)) : gmdate('Y');
    $grouped[$year][] = $item;
}
ksort($grouped);

$instance_id = 'sc-startdata-' . wp_unique_id();
$initial_visible = 6;
?>

<style>
#<?php echo esc_attr($instance_id); ?> {
    --sc-bg: #F4F7F6;
    --sc-surface: #FFFFFF;
    --sc-ink: #0E1626;
    --sc-body: #5B6675;
    --sc-emerald: #19C37D;
    --sc-emerald-dk: #0E8F5C;
    --sc-emerald-soft: #D7F0E4;
    --sc-line: #DDE4E3;
    --sc-bad: #B6533A;
    --sc-shadow-sm: 0 4px 14px -6px rgba(14, 22, 38, 0.18);
    --sc-shadow-md: 0 18px 40px -22px rgba(14, 22, 38, 0.22);

    background: transparent;
    color: var(--sc-ink);
    font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    -webkit-font-smoothing: antialiased;
}
#<?php echo esc_attr($instance_id); ?> * { box-sizing: border-box; }
#<?php echo esc_attr($instance_id); ?> .sc-wrap { max-width: 100%; margin: 0; padding: 0; }

#<?php echo esc_attr($instance_id); ?> .sc-head {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-end;
    justify-content: space-between;
    gap: 28px;
    margin-bottom: 44px;
}
#<?php echo esc_attr($instance_id); ?> .sc-title {
    font-family: "Sora", -apple-system, sans-serif;
    font-size: clamp(2.2rem, 1.5rem + 2.2vw, 3.2rem);
    font-weight: 800;
    letter-spacing: -0.02em;
    line-height: 1.05;
    color: var(--sc-ink);
    margin: 0;
}
#<?php echo esc_attr($instance_id); ?> .sc-title em {
    font-style: normal;
    font-weight: 700;
    color: var(--sc-emerald-dk);
}
#<?php echo esc_attr($instance_id); ?> .sc-title-leaf {
    display: inline-block;
    width: 28px;
    height: 28px;
    margin-right: 4px;
    vertical-align: -2px;
}
#<?php echo esc_attr($instance_id); ?> .sc-controls {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 18px;
}
#<?php echo esc_attr($instance_id); ?> .sc-select {
    border: 1px solid var(--sc-line);
    background: var(--sc-surface);
    color: var(--sc-ink);
    border-radius: 12px;
    padding: 12px 44px 12px 20px;
    font-size: 14px;
    font-family: inherit;
    min-width: 200px;
    cursor: pointer;
    appearance: none;
    -webkit-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='none' viewBox='0 0 24 24' stroke='%230E8F5C' stroke-width='2.4'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19.5 8.25l-7.5 7.5-7.5-7.5'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 18px center;
    transition: border-color 0.2s;
}
#<?php echo esc_attr($instance_id); ?> .sc-select:focus { outline: none; border-color: var(--sc-emerald); }
#<?php echo esc_attr($instance_id); ?> .sc-toggle {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    cursor: pointer;
    user-select: none;
    font-size: 14px;
    color: var(--sc-ink);
}
#<?php echo esc_attr($instance_id); ?> .sc-toggle input {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}
#<?php echo esc_attr($instance_id); ?> .sc-toggle-track {
    width: 44px;
    height: 26px;
    background: #C7D0CE;
    border-radius: 13px;
    position: relative;
    transition: background 0.2s;
    flex-shrink: 0;
}
#<?php echo esc_attr($instance_id); ?> .sc-toggle input:checked + .sc-toggle-track { background: var(--sc-emerald); }
#<?php echo esc_attr($instance_id); ?> .sc-toggle-knob {
    width: 20px;
    height: 20px;
    background: #fff;
    border-radius: 50%;
    position: absolute;
    top: 3px;
    left: 3px;
    transition: transform 0.2s;
    box-shadow: 0 1px 3px rgba(14, 22, 38, 0.25);
}
#<?php echo esc_attr($instance_id); ?> .sc-toggle input:checked + .sc-toggle-track .sc-toggle-knob { transform: translateX(18px); }

/* Year section header with thin emerald rule */
#<?php echo esc_attr($instance_id); ?> .sc-year {
    display: flex;
    align-items: center;
    gap: 18px;
    margin: 12px 0 28px 0;
}
#<?php echo esc_attr($instance_id); ?> .sc-year-label {
    font-family: "IBM Plex Mono", ui-monospace, monospace;
    font-size: 16px;
    font-weight: 600;
    color: var(--sc-emerald-dk);
    white-space: nowrap;
}
#<?php echo esc_attr($instance_id); ?> .sc-year-wave {
    flex-grow: 1;
    height: 4px;
    color: var(--sc-emerald);
    opacity: 0.6;
}

/* Crisp dashboard grid */
#<?php echo esc_attr($instance_id); ?> .sc-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    align-items: start;
    gap: 28px;
    margin-bottom: 44px;
}
@media (max-width: 1024px) { #<?php echo esc_attr($instance_id); ?> .sc-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 640px)  { #<?php echo esc_attr($instance_id); ?> .sc-grid { grid-template-columns: 1fr; } }

#<?php echo esc_attr($instance_id); ?> .sc-card {
    position: relative;
    background: var(--sc-surface);
    border: 1px solid var(--sc-line);
    border-top: 3px solid var(--sc-emerald);
    border-radius: 14px;
    box-shadow: var(--sc-shadow-sm);
    padding: 30px 30px 28px;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
}
@media (hover: hover) and (pointer: fine) {
    #<?php echo esc_attr($instance_id); ?> .sc-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--sc-shadow-md);
        border-color: var(--sc-line);
        border-top-color: var(--sc-emerald);
    }
}

#<?php echo esc_attr($instance_id); ?> .sc-card > * { position: relative; z-index: 1; }

#<?php echo esc_attr($instance_id); ?> .sc-date {
    font-family: "IBM Plex Mono", ui-monospace, monospace;
    font-size: 1.6rem;
    font-weight: 600;
    color: var(--sc-ink);
    margin: 0 0 22px 0;
    line-height: 1.05;
    letter-spacing: -0.01em;
}
#<?php echo esc_attr($instance_id); ?> .sc-meta {
    display: flex;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 22px;
}
#<?php echo esc_attr($instance_id); ?> .sc-meta-label {
    font-family: "IBM Plex Mono", ui-monospace, monospace;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--sc-body);
    margin-bottom: 5px;
}
#<?php echo esc_attr($instance_id); ?> .sc-meta-value {
    font-size: 14px;
    font-weight: 600;
    color: var(--sc-ink);
}
#<?php echo esc_attr($instance_id); ?> .sc-avail { white-space: nowrap; display: inline-flex; align-items: center; gap: 9px; }
#<?php echo esc_attr($instance_id); ?> .sc-sprout {
    width: 14px;
    height: 14px;
    flex-shrink: 0;
}

#<?php echo esc_attr($instance_id); ?> .sc-acc-btn {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: var(--sc-emerald-soft);
    border: none;
    border-radius: 12px;
    padding: 13px 18px;
    font-size: 14px;
    font-weight: 700;
    color: var(--sc-emerald-dk);
    cursor: pointer;
    font-family: inherit;
    transition: background 0.15s;
}
@media (hover: hover) and (pointer: fine) {
    #<?php echo esc_attr($instance_id); ?> .sc-acc-btn:hover { background: #C2E7D5; }
}
#<?php echo esc_attr($instance_id); ?> .sc-acc-icon {
    width: 18px;
    height: 18px;
    color: var(--sc-emerald-dk);
    transition: transform 0.2s;
}
#<?php echo esc_attr($instance_id); ?> .sc-lesson {
    background: var(--sc-bg);
    border-radius: 12px;
    padding: 12px 16px;
    font-size: 14px;
    margin-top: 8px;
    border-left: 3px solid var(--sc-emerald);
}
#<?php echo esc_attr($instance_id); ?> .sc-lesson-name {
    font-weight: 600;
    color: var(--sc-ink);
    margin-bottom: 4px;
}
#<?php echo esc_attr($instance_id); ?> .sc-lesson-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    font-family: "IBM Plex Mono", ui-monospace, monospace;
    font-size: 12px;
    color: var(--sc-body);
}
#<?php echo esc_attr($instance_id); ?> .sc-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-top: auto;
    padding: 11px 22px;
    background: var(--sc-emerald);
    color: var(--sc-ink);
    font-size: 14px;
    font-weight: 700;
    text-decoration: none;
    border-radius: 12px;
    box-shadow: 0 8px 20px -10px rgba(25, 195, 125, 0.55);
    transition: background 0.15s ease, transform 0.15s ease;
}
@media (hover: hover) and (pointer: fine) {
    #<?php echo esc_attr($instance_id); ?> .sc-btn:hover {
        background: var(--sc-emerald-dk);
        color: #fff;
        transform: translateY(-1px);
    }
}
#<?php echo esc_attr($instance_id); ?> .sc-btn:active { transform: translateY(0); }
#<?php echo esc_attr($instance_id); ?> .sc-btn svg { width: 14px; height: 14px; }

#<?php echo esc_attr($instance_id); ?> .sc-show-all {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 14px 34px;
    background: transparent;
    color: var(--sc-emerald-dk);
    border: 1px solid var(--sc-emerald-dk);
    border-radius: 12px;
    font-size: 14px;
    font-weight: 700;
    font-family: inherit;
    cursor: pointer;
    transition: background 0.2s, color 0.2s;
}
@media (hover: hover) and (pointer: fine) {
    #<?php echo esc_attr($instance_id); ?> .sc-show-all:hover { background: var(--sc-emerald); color: var(--sc-ink); border-color: var(--sc-emerald); }
}
#<?php echo esc_attr($instance_id); ?> .sc-show-all svg { width: 15px; height: 15px; }

@media (max-width: 640px) {
    #<?php echo esc_attr($instance_id); ?> .sc-wrap { padding: 52px 20px; }
    #<?php echo esc_attr($instance_id); ?> .sc-controls { width: 100%; gap: 14px; }
    #<?php echo esc_attr($instance_id); ?> .sc-select { width: 100%; min-width: 0; }
    #<?php echo esc_attr($instance_id); ?> .sc-toggle { width: 100%; justify-content: space-between; }
}
</style>

<div id="<?php echo esc_attr($instance_id); ?>">
    <div class="sc-wrap">

        <div class="sc-head">
            <h2 class="sc-title">
                <svg class="sc-title-leaf" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M4 16l5-5 4 4 7-7" stroke="#19C37D" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M16 8h4v4" stroke="#0E8F5C" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Start<em>data</em>
            </h2>
            <div class="sc-controls">
                <select id="<?php echo esc_attr($instance_id); ?>-location" class="sc-select" aria-label="Filter op locatie">
                    <option value="">Alle locaties</option>
                    <?php foreach ($locations as $loc): ?>
                        <option value="<?php echo esc_attr($loc); ?>"><?php echo esc_html($loc); ?></option>
                    <?php endforeach; ?>
                </select>
                <label class="sc-toggle">
                    <span>Toon enkel beschikbaar</span>
                    <input type="checkbox" id="<?php echo esc_attr($instance_id); ?>-avail" checked aria-label="Toon alleen beschikbare trainingen">
                    <span class="sc-toggle-track"><span class="sc-toggle-knob"></span></span>
                </label>
            </div>
        </div>

        <?php $card_index = 0; ?>
        <?php foreach ($grouped as $year => $year_items): ?>

            <div class="sc-year sc-year-sep">
                <span class="sc-year-label"><?php echo esc_html($year); ?></span>
                <svg class="sc-year-wave" preserveAspectRatio="none" viewBox="0 0 400 4" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <line x1="0" y1="2" x2="400" y2="2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>

            <div class="sc-grid sc-year-grid">
                <?php foreach ($year_items as $index => $track):
                    $location = $track['default_location']['label'] ?? ($track['traininglessons'][0]['location_label_no_room'] ?? 'n.n.b.');

                    $show_spots = $track['show_free_spots'] ?? false;
                    $free_spots = $track['readable_free_spots_left'] ?? null;
                    $is_open = $track['is_track_open']['open'] ?? true;
                    $is_available = $is_open && ($free_spots === null || $free_spots > 0);

                    if (!$show_spots || $free_spots === null) {
                        $avail_text = $is_open ? 'Beschikbaar' : 'Gesloten';
                    } elseif ($free_spots <= 0) {
                        $avail_text = 'Vol';
                        $is_available = false;
                    } elseif ($free_spots <= 5) {
                        $avail_text = 'Nog ' . $free_spots . ($free_spots == 1 ? ' plaats' : ' plaatsen');
                    } else {
                        $avail_text = 'Beschikbaar';
                    }

                    $lessons = $track['traininglessons'] ?? [];
                    $accordion_id = $instance_id . '-acc-' . $track['id'] . '-' . $index;
                    $link = $track['link_to_mentor'] ?? '#';
                    $start_raw = $track['training_start_raw'] ?? '';
                    $start_display = $start_raw ? date_i18n('j F', strtotime($start_raw)) : ($track['training_start'] ?? '');
                    $hidden_class = $card_index >= $initial_visible ? 'sc-extra' : '';
                    $card_index++;
                ?>

                <div class="sc-card sc-sd-card <?php echo esc_attr($hidden_class); ?>"
                     <?php if ($hidden_class): ?>style="display:none;"<?php endif; ?>
                     data-location="<?php echo esc_attr($location); ?>"
                     data-available="<?php echo esc_attr($is_available ? '1' : '0'); ?>">

                    <div class="sc-date"><?php echo esc_html($start_display); ?></div>

                    <div class="sc-meta">
                        <div>
                            <div class="sc-meta-label">Locatie</div>
                            <div class="sc-meta-value"><?php echo esc_html($location); ?></div>
                        </div>
                        <div>
                            <div class="sc-meta-label">Beschikbaarheid</div>
                            <div class="sc-meta-value sc-avail">
                                <?php if ($is_available): ?>
                                    <svg class="sc-sprout" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M4 17l5-5 4 4 7-7" stroke="#0E8F5C" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M16 9h4v4" stroke="#0E8F5C" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                <?php else: ?>
                                    <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:var(--sc-bad);"></span>
                                <?php endif; ?>
                                <?php echo esc_html($avail_text); ?>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($lessons)): ?>
                    <div style="margin-bottom: 22px;">
                        <button class="sc-acc-btn sc-acc-trigger" data-target="<?php echo esc_attr($accordion_id); ?>" type="button" aria-expanded="false" aria-controls="<?php echo esc_attr($accordion_id); ?>">
                            <span>Planning modules</span>
                            <svg class="sc-acc-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                            </svg>
                        </button>
                        <div id="<?php echo esc_attr($accordion_id); ?>" style="display: none;">
                            <?php foreach ($lessons as $lesson): ?>
                                <div class="sc-lesson">
                                    <div class="sc-lesson-name"><?php echo esc_html($lesson['lesson'] ?? ''); ?></div>
                                    <div class="sc-lesson-meta">
                                        <?php if (!empty($lesson['start'])): ?>
                                            <span><?php echo esc_html(preg_replace('/\s*\d{4}$/', '', $lesson['start'])); ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($lesson['start_time']) && !empty($lesson['end_time'])): ?>
                                            <span><?php echo esc_html($lesson['start_time']); ?> - <?php echo esc_html($lesson['end_time']); ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($lesson['location_label_no_room'])): ?>
                                            <span><?php echo esc_html($lesson['location_label_no_room']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <a href="<?php echo esc_url($link); ?>" class="sc-btn">
                        Inschrijven
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                        </svg>
                    </a>
                </div>

                <?php endforeach; ?>
            </div>

        <?php endforeach; ?>

        <?php if ($card_index > $initial_visible): ?>
        <div style="text-align: center; margin-top: 8px;" id="<?php echo esc_attr($instance_id); ?>-show-all-wrap">
            <button id="<?php echo esc_attr($instance_id); ?>-show-all" class="sc-show-all" type="button">
                Toon alle data
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                </svg>
            </button>
        </div>
        <?php endif; ?>

    </div>
</div>

<script>
(function() {
    document.addEventListener("DOMContentLoaded", function() {
        var root = document.getElementById("<?php echo esc_attr($instance_id); ?>");
        if (!root) return;

        var locationFilter = root.querySelector("#<?php echo esc_attr($instance_id); ?>-location");
        var availToggle = root.querySelector("#<?php echo esc_attr($instance_id); ?>-avail");
        var showAllBtn = root.querySelector("#<?php echo esc_attr($instance_id); ?>-show-all");
        var showAllWrap = root.querySelector("#<?php echo esc_attr($instance_id); ?>-show-all-wrap");
        var cards = root.querySelectorAll(".sc-sd-card");
        var allRevealed = false;

        function applyFilters() {
            var locVal = locationFilter ? locationFilter.value : "";
            var availOnly = availToggle ? availToggle.checked : false;

            cards.forEach(function(card) {
                var matchLoc = !locVal || card.dataset.location === locVal;
                var matchAvail = !availOnly || card.dataset.available === "1";
                var isExtra = card.classList.contains("sc-extra") && !allRevealed;
                card.style.display = (matchLoc && matchAvail && !isExtra) ? "" : "none";
            });

            root.querySelectorAll(".sc-year-grid").forEach(function(grid) {
                var sep = grid.previousElementSibling;
                var hasVisible = Array.from(grid.querySelectorAll(".sc-sd-card")).some(function(c) {
                    return c.style.display !== "none";
                });
                if (sep && sep.classList.contains("sc-year-sep")) {
                    sep.style.display = hasVisible ? "" : "none";
                }
                grid.style.display = hasVisible ? "" : "none";
            });
        }

        if (locationFilter) locationFilter.addEventListener("change", applyFilters);
        if (availToggle) availToggle.addEventListener("change", applyFilters);

        if (showAllBtn) {
            showAllBtn.addEventListener("click", function() {
                allRevealed = true;
                cards.forEach(function(card) { card.classList.remove("sc-extra"); });
                if (showAllWrap) showAllWrap.style.display = "none";
                applyFilters();
            });
        }

        root.querySelectorAll(".sc-acc-trigger").forEach(function(btn) {
            btn.addEventListener("click", function() {
                var panel = document.getElementById(this.dataset.target);
                var icon = this.querySelector(".sc-acc-icon");
                if (panel) {
                    var wasHidden = panel.style.display === "none";
                    panel.style.display = wasHidden ? "block" : "none";
                    this.setAttribute("aria-expanded", wasHidden ? "true" : "false");
                    if (icon) icon.style.transform = wasHidden ? "rotate(45deg)" : "";
                }
            });
        });
    });
})();
</script>
