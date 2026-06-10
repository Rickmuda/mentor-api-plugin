<?php
/**
 * Skin: werving-selectie - Startdata-agenda ("Decisive hiring", executive/beslis-thema)
 * Verwacht dezelfde variabelen als templates/startdata.php: $tracks, $api_url
 */
defined('ABSPATH') or die('No script kiddies please!');

$items = $tracks['results'] ?? $tracks;

if (empty($items) || !is_array($items)) {
    echo '<p>Geen startdata gevonden.</p>';
    return;
}

if (!wp_style_is('mentor-werving-selectie-fonts', 'enqueued')) {
    wp_enqueue_style(
        'mentor-werving-selectie-fonts',
        'https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600&display=swap',
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

$instance_id = 'ws-startdata-' . wp_unique_id();
$initial_visible = 6;
?>

<style>
#<?php echo esc_attr($instance_id); ?> {
    --ws-bg: #F5F3EE;
    --ws-surface: #FFFFFF;
    --ws-ink: #14181F;
    --ws-body: #5A5E66;
    --ws-coral: #FF4F36;
    --ws-coral-dark: #E23A22;
    --ws-coral-soft: #FCE4DF;
    --ws-line: #E2DED6;
    --ws-bad: #B6533A;
    --ws-shadow-sm: 0 6px 18px -10px rgba(20, 24, 31, 0.18);
    --ws-shadow-md: 0 20px 44px -26px rgba(20, 24, 31, 0.24);

    background: transparent;
    color: var(--ws-ink);
    font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    -webkit-font-smoothing: antialiased;
}
#<?php echo esc_attr($instance_id); ?> * { box-sizing: border-box; }
#<?php echo esc_attr($instance_id); ?> .ws-wrap { max-width: 100%; margin: 0; padding: 0; }

#<?php echo esc_attr($instance_id); ?> .ws-head {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-end;
    justify-content: space-between;
    gap: 28px;
    margin-bottom: 44px;
}
#<?php echo esc_attr($instance_id); ?> .ws-title {
    font-family: "Space Grotesk", "Segoe UI", sans-serif;
    font-size: clamp(2.2rem, 1.5rem + 2.2vw, 3.2rem);
    font-weight: 700;
    letter-spacing: -0.02em;
    line-height: 1.05;
    color: var(--ws-ink);
    margin: 0;
}
#<?php echo esc_attr($instance_id); ?> .ws-title em {
    font-style: normal;
    font-weight: 700;
    color: var(--ws-coral);
}
#<?php echo esc_attr($instance_id); ?> .ws-title-mark {
    display: inline-block;
    width: 22px;
    height: 22px;
    margin-right: 6px;
    vertical-align: -2px;
}
#<?php echo esc_attr($instance_id); ?> .ws-controls {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 18px;
}
#<?php echo esc_attr($instance_id); ?> .ws-select {
    border: 1px solid var(--ws-line);
    background: var(--ws-surface);
    color: var(--ws-ink);
    border-radius: 6px;
    padding: 12px 44px 12px 18px;
    font-size: 14px;
    font-family: inherit;
    min-width: 200px;
    cursor: pointer;
    appearance: none;
    -webkit-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='none' viewBox='0 0 24 24' stroke='%23FF4F36' stroke-width='2.4'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19.5 8.25l-7.5 7.5-7.5-7.5'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 16px center;
    transition: border-color 0.2s;
}
#<?php echo esc_attr($instance_id); ?> .ws-select:focus { outline: none; border-color: var(--ws-coral); }
#<?php echo esc_attr($instance_id); ?> .ws-toggle {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    cursor: pointer;
    user-select: none;
    font-size: 14px;
    color: var(--ws-ink);
}
#<?php echo esc_attr($instance_id); ?> .ws-toggle input {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}
#<?php echo esc_attr($instance_id); ?> .ws-toggle-track {
    width: 44px;
    height: 26px;
    background: #CFCBC2;
    border-radius: 13px;
    position: relative;
    transition: background 0.2s;
    flex-shrink: 0;
}
#<?php echo esc_attr($instance_id); ?> .ws-toggle input:checked + .ws-toggle-track { background: var(--ws-coral); }
#<?php echo esc_attr($instance_id); ?> .ws-toggle-knob {
    width: 20px;
    height: 20px;
    background: #fff;
    border-radius: 50%;
    position: absolute;
    top: 3px;
    left: 3px;
    transition: transform 0.2s;
    box-shadow: 0 1px 3px rgba(20, 24, 31, 0.25);
}
#<?php echo esc_attr($instance_id); ?> .ws-toggle input:checked + .ws-toggle-track .ws-toggle-knob { transform: translateX(18px); }

/* Year divider - micro-label with thin coral rule */
#<?php echo esc_attr($instance_id); ?> .ws-year {
    display: flex;
    align-items: center;
    gap: 18px;
    margin: 12px 0 28px 0;
}
#<?php echo esc_attr($instance_id); ?> .ws-year-label {
    font-family: "Space Grotesk", "Segoe UI", sans-serif;
    font-size: 14px;
    font-weight: 600;
    letter-spacing: 0.12em;
    color: var(--ws-coral);
    white-space: nowrap;
}
#<?php echo esc_attr($instance_id); ?> .ws-year-rule {
    flex-grow: 1;
    height: 0;
    border-top: 1px solid var(--ws-line);
}

/* Structured grid */
#<?php echo esc_attr($instance_id); ?> .ws-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    align-items: start;
    gap: 28px;
    margin-bottom: 44px;
}
@media (max-width: 1024px) { #<?php echo esc_attr($instance_id); ?> .ws-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 640px)  { #<?php echo esc_attr($instance_id); ?> .ws-grid { grid-template-columns: 1fr; } }

#<?php echo esc_attr($instance_id); ?> .ws-card {
    position: relative;
    background: var(--ws-surface);
    border: 1px solid var(--ws-line);
    border-radius: 8px;
    box-shadow: var(--ws-shadow-sm);
    padding: 32px 30px 28px;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
}
/* Coral top-accent bar on each card */
#<?php echo esc_attr($instance_id); ?> .ws-card::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 3px;
    background: var(--ws-coral);
    z-index: 0;
}
@media (hover: hover) and (pointer: fine) {
    #<?php echo esc_attr($instance_id); ?> .ws-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--ws-shadow-md);
        border-color: var(--ws-coral);
    }
}

#<?php echo esc_attr($instance_id); ?> .ws-card > * { position: relative; z-index: 1; }

#<?php echo esc_attr($instance_id); ?> .ws-date {
    font-family: "Space Grotesk", "Segoe UI", sans-serif;
    font-size: 1.7rem;
    font-weight: 700;
    color: var(--ws-ink);
    margin: 0 0 22px 0;
    line-height: 1.05;
    letter-spacing: -0.01em;
}
#<?php echo esc_attr($instance_id); ?> .ws-meta {
    display: flex;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 22px;
}
#<?php echo esc_attr($instance_id); ?> .ws-meta-label {
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: var(--ws-body);
    margin-bottom: 5px;
}
#<?php echo esc_attr($instance_id); ?> .ws-meta-value {
    font-size: 14px;
    font-weight: 600;
    color: var(--ws-ink);
}
#<?php echo esc_attr($instance_id); ?> .ws-avail { white-space: nowrap; display: inline-flex; align-items: center; gap: 9px; }
#<?php echo esc_attr($instance_id); ?> .ws-mark {
    width: 12px;
    height: 12px;
    flex-shrink: 0;
}

#<?php echo esc_attr($instance_id); ?> .ws-acc-btn {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: var(--ws-coral-soft);
    border: none;
    border-radius: 6px;
    padding: 13px 18px;
    font-size: 14px;
    font-weight: 600;
    color: var(--ws-coral-dark);
    cursor: pointer;
    font-family: inherit;
    transition: background 0.15s;
}
@media (hover: hover) and (pointer: fine) {
    #<?php echo esc_attr($instance_id); ?> .ws-acc-btn:hover { background: #FBD6CE; }
}
#<?php echo esc_attr($instance_id); ?> .ws-acc-icon {
    width: 18px;
    height: 18px;
    color: var(--ws-coral-dark);
    transition: transform 0.2s;
}
#<?php echo esc_attr($instance_id); ?> .ws-lesson {
    background: var(--ws-bg);
    border-radius: 6px;
    padding: 12px 16px;
    font-size: 14px;
    margin-top: 8px;
    border-left: 3px solid var(--ws-coral);
}
#<?php echo esc_attr($instance_id); ?> .ws-lesson-name {
    font-weight: 600;
    color: var(--ws-ink);
    margin-bottom: 4px;
}
#<?php echo esc_attr($instance_id); ?> .ws-lesson-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    font-size: 12px;
    color: var(--ws-body);
}
#<?php echo esc_attr($instance_id); ?> .ws-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-top: auto;
    padding: 11px 22px;
    background: var(--ws-coral);
    color: #fff;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    border-radius: 6px;
    box-shadow: 0 10px 22px -12px rgba(255, 79, 54, 0.65);
    transition: background 0.15s ease, transform 0.15s ease;
}
@media (hover: hover) and (pointer: fine) {
    #<?php echo esc_attr($instance_id); ?> .ws-btn:hover {
        background: var(--ws-coral-dark);
        transform: translateY(-1px);
    }
}
#<?php echo esc_attr($instance_id); ?> .ws-btn:active { transform: translateY(0); }
#<?php echo esc_attr($instance_id); ?> .ws-btn svg { width: 14px; height: 14px; }

#<?php echo esc_attr($instance_id); ?> .ws-show-all {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 14px 34px;
    background: transparent;
    color: var(--ws-coral-dark);
    border: 1px solid var(--ws-coral);
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    font-family: inherit;
    cursor: pointer;
    transition: background 0.2s, color 0.2s;
}
@media (hover: hover) and (pointer: fine) {
    #<?php echo esc_attr($instance_id); ?> .ws-show-all:hover { background: var(--ws-coral); color: #fff; }
}
#<?php echo esc_attr($instance_id); ?> .ws-show-all svg { width: 15px; height: 15px; }

@media (max-width: 640px) {
    #<?php echo esc_attr($instance_id); ?> .ws-wrap { padding: 52px 20px; }
    #<?php echo esc_attr($instance_id); ?> .ws-controls { width: 100%; gap: 14px; }
    #<?php echo esc_attr($instance_id); ?> .ws-select { width: 100%; min-width: 0; }
    #<?php echo esc_attr($instance_id); ?> .ws-toggle { width: 100%; justify-content: space-between; }
}
</style>

<div id="<?php echo esc_attr($instance_id); ?>">
    <div class="ws-wrap">

        <div class="ws-head">
            <h2 class="ws-title">
                <svg class="ws-title-mark" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <rect x="4" y="4" width="16" height="16" rx="2" fill="#FF4F36"/>
                    <path d="M8 12.5l2.5 2.5L16 9" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Start<em>data</em>
            </h2>
            <div class="ws-controls">
                <select id="<?php echo esc_attr($instance_id); ?>-location" class="ws-select" aria-label="Filter op locatie">
                    <option value="">Alle locaties</option>
                    <?php foreach ($locations as $loc): ?>
                        <option value="<?php echo esc_attr($loc); ?>"><?php echo esc_html($loc); ?></option>
                    <?php endforeach; ?>
                </select>
                <label class="ws-toggle">
                    <span>Toon enkel beschikbaar</span>
                    <input type="checkbox" id="<?php echo esc_attr($instance_id); ?>-avail" checked aria-label="Toon alleen beschikbare trainingen">
                    <span class="ws-toggle-track"><span class="ws-toggle-knob"></span></span>
                </label>
            </div>
        </div>

        <?php $card_index = 0; ?>
        <?php foreach ($grouped as $year => $year_items): ?>

            <div class="ws-year ws-year-sep">
                <span class="ws-year-label"><?php echo esc_html($year); ?></span>
                <span class="ws-year-rule" aria-hidden="true"></span>
            </div>

            <div class="ws-grid ws-year-grid">
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
                    $hidden_class = $card_index >= $initial_visible ? 'ws-extra' : '';
                    $card_index++;
                ?>

                <div class="ws-card ws-sd-card <?php echo esc_attr($hidden_class); ?>"
                     <?php if ($hidden_class): ?>style="display:none;"<?php endif; ?>
                     data-location="<?php echo esc_attr($location); ?>"
                     data-available="<?php echo esc_attr($is_available ? '1' : '0'); ?>">

                    <div class="ws-date"><?php echo esc_html($start_display); ?></div>

                    <div class="ws-meta">
                        <div>
                            <div class="ws-meta-label">Locatie</div>
                            <div class="ws-meta-value"><?php echo esc_html($location); ?></div>
                        </div>
                        <div>
                            <div class="ws-meta-label">Beschikbaarheid</div>
                            <div class="ws-meta-value ws-avail">
                                <?php if ($is_available): ?>
                                    <svg class="ws-mark" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <rect x="3" y="3" width="18" height="18" rx="2" fill="#FF4F36"/>
                                        <path d="M7 12.5l3 3L17 8.5" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                <?php else: ?>
                                    <span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:var(--ws-bad);"></span>
                                <?php endif; ?>
                                <?php echo esc_html($avail_text); ?>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($lessons)): ?>
                    <div style="margin-bottom: 22px;">
                        <button class="ws-acc-btn ws-acc-trigger" data-target="<?php echo esc_attr($accordion_id); ?>" type="button" aria-expanded="false" aria-controls="<?php echo esc_attr($accordion_id); ?>">
                            <span>Planning modules</span>
                            <svg class="ws-acc-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                            </svg>
                        </button>
                        <div id="<?php echo esc_attr($accordion_id); ?>" style="display: none;">
                            <?php foreach ($lessons as $lesson): ?>
                                <div class="ws-lesson">
                                    <div class="ws-lesson-name"><?php echo esc_html($lesson['lesson'] ?? ''); ?></div>
                                    <div class="ws-lesson-meta">
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

                    <a href="<?php echo esc_url($link); ?>" class="ws-btn">
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
            <button id="<?php echo esc_attr($instance_id); ?>-show-all" class="ws-show-all" type="button">
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
        var cards = root.querySelectorAll(".ws-sd-card");
        var allRevealed = false;

        function applyFilters() {
            var locVal = locationFilter ? locationFilter.value : "";
            var availOnly = availToggle ? availToggle.checked : false;

            cards.forEach(function(card) {
                var matchLoc = !locVal || card.dataset.location === locVal;
                var matchAvail = !availOnly || card.dataset.available === "1";
                var isExtra = card.classList.contains("ws-extra") && !allRevealed;
                card.style.display = (matchLoc && matchAvail && !isExtra) ? "" : "none";
            });

            root.querySelectorAll(".ws-year-grid").forEach(function(grid) {
                var sep = grid.previousElementSibling;
                var hasVisible = Array.from(grid.querySelectorAll(".ws-sd-card")).some(function(c) {
                    return c.style.display !== "none";
                });
                if (sep && sep.classList.contains("ws-year-sep")) {
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
                cards.forEach(function(card) { card.classList.remove("ws-extra"); });
                if (showAllWrap) showAllWrap.style.display = "none";
                applyFilters();
            });
        }

        root.querySelectorAll(".ws-acc-trigger").forEach(function(btn) {
            btn.addEventListener("click", function() {
                var panel = document.getElementById(this.dataset.target);
                var icon = this.querySelector(".ws-acc-icon");
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
