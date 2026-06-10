<?php
/**
 * Skin: feedback - Startdata-agenda ("Organisch & groei", feedback-thema)
 * Verwacht dezelfde variabelen als templates/startdata.php: $tracks, $api_url
 */
defined('ABSPATH') or die('No script kiddies please!');

$items = $tracks['results'] ?? $tracks;

if (empty($items) || !is_array($items)) {
    echo '<p>Geen startdata gevonden.</p>';
    return;
}

if (!wp_style_is('mentor-feedback-fonts', 'enqueued')) {
    wp_enqueue_style(
        'mentor-feedback-fonts',
        'https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700;9..144,800&family=Inter:wght@400;500;600;700&display=swap',
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

$instance_id = 'fb-startdata-' . wp_unique_id();
$initial_visible = 6;
?>

<style>
#<?php echo esc_attr($instance_id); ?> {
    --fb-bg: #FBFAF4;
    --fb-surface: #FFFFFF;
    --fb-ink: #1E2D1E;
    --fb-body: #5B6A5B;
    --fb-green: #4e7847;
    --fb-green-dark: #3A5C35;
    --fb-leaf: #91d66b;
    --fb-leaf-soft: #E5F2D6;
    --fb-line: #E5E8DE;
    --fb-bad: #B6533A;
    --fb-shadow-sm: 0 4px 14px -6px rgba(78, 120, 71, 0.18);
    --fb-shadow-md: 0 18px 40px -22px rgba(30, 45, 30, 0.22);

    background: transparent;
    color: var(--fb-ink);
    font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    -webkit-font-smoothing: antialiased;
}
#<?php echo esc_attr($instance_id); ?> * { box-sizing: border-box; }
#<?php echo esc_attr($instance_id); ?> .fb-wrap { max-width: 100%; margin: 0; padding: 0; }

#<?php echo esc_attr($instance_id); ?> .fb-head {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-end;
    justify-content: space-between;
    gap: 28px;
    margin-bottom: 44px;
}
#<?php echo esc_attr($instance_id); ?> .fb-title {
    font-family: "Fraunces", Georgia, serif;
    font-optical-sizing: auto;
    font-variation-settings: "opsz" 96;
    font-size: clamp(2.2rem, 1.5rem + 2.2vw, 3.2rem);
    font-weight: 700;
    letter-spacing: -0.02em;
    line-height: 1.05;
    color: var(--fb-ink);
    margin: 0;
}
#<?php echo esc_attr($instance_id); ?> .fb-title em {
    font-style: italic;
    font-weight: 600;
    color: var(--fb-green);
}
#<?php echo esc_attr($instance_id); ?> .fb-title-leaf {
    display: inline-block;
    width: 28px;
    height: 28px;
    margin-right: 4px;
    vertical-align: -2px;
}
#<?php echo esc_attr($instance_id); ?> .fb-controls {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 18px;
}
#<?php echo esc_attr($instance_id); ?> .fb-select {
    border: 1.5px solid var(--fb-line);
    background: var(--fb-surface);
    color: var(--fb-ink);
    border-radius: 9999px;
    padding: 12px 44px 12px 20px;
    font-size: 14px;
    font-family: inherit;
    min-width: 200px;
    cursor: pointer;
    appearance: none;
    -webkit-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='none' viewBox='0 0 24 24' stroke='%234e7847' stroke-width='2.4'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19.5 8.25l-7.5 7.5-7.5-7.5'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 18px center;
    transition: border-color 0.2s;
}
#<?php echo esc_attr($instance_id); ?> .fb-select:focus { outline: none; border-color: var(--fb-green); }
#<?php echo esc_attr($instance_id); ?> .fb-toggle {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    cursor: pointer;
    user-select: none;
    font-size: 14px;
    color: var(--fb-ink);
}
#<?php echo esc_attr($instance_id); ?> .fb-toggle input {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}
#<?php echo esc_attr($instance_id); ?> .fb-toggle-track {
    width: 44px;
    height: 26px;
    background: #D4D7CD;
    border-radius: 13px;
    position: relative;
    transition: background 0.2s;
    flex-shrink: 0;
}
#<?php echo esc_attr($instance_id); ?> .fb-toggle input:checked + .fb-toggle-track { background: var(--fb-green); }
#<?php echo esc_attr($instance_id); ?> .fb-toggle-knob {
    width: 20px;
    height: 20px;
    background: #fff;
    border-radius: 50%;
    position: absolute;
    top: 3px;
    left: 3px;
    transition: transform 0.2s;
    box-shadow: 0 1px 3px rgba(30, 45, 30, 0.25);
}
#<?php echo esc_attr($instance_id); ?> .fb-toggle input:checked + .fb-toggle-track .fb-toggle-knob { transform: translateX(18px); }

/* Wavy year divider */
#<?php echo esc_attr($instance_id); ?> .fb-year {
    display: flex;
    align-items: center;
    gap: 18px;
    margin: 12px 0 28px 0;
}
#<?php echo esc_attr($instance_id); ?> .fb-year-label {
    font-family: "Fraunces", Georgia, serif;
    font-variation-settings: "opsz" 24;
    font-size: 17px;
    font-weight: 600;
    font-style: italic;
    color: var(--fb-green);
    white-space: nowrap;
}
#<?php echo esc_attr($instance_id); ?> .fb-year-wave {
    flex-grow: 1;
    height: 14px;
    color: var(--fb-leaf);
    opacity: 0.6;
}

/* Asymmetric organic grid */
#<?php echo esc_attr($instance_id); ?> .fb-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    align-items: start;
    gap: 28px;
    margin-bottom: 44px;
}
@media (max-width: 1024px) { #<?php echo esc_attr($instance_id); ?> .fb-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 640px)  { #<?php echo esc_attr($instance_id); ?> .fb-grid { grid-template-columns: 1fr; } }

#<?php echo esc_attr($instance_id); ?> .fb-card {
    position: relative;
    background: var(--fb-surface);
    border: 1.5px solid var(--fb-line);
    /* Asymmetric organic shape */
    border-radius: 42px 56px 42px 56px / 56px 42px 56px 42px;
    box-shadow: var(--fb-shadow-sm);
    padding: 32px 30px 28px;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
}
#<?php echo esc_attr($instance_id); ?> .fb-card:nth-child(even) {
    border-radius: 56px 42px 56px 42px / 42px 56px 42px 56px;
}
#<?php echo esc_attr($instance_id); ?> .fb-card:nth-child(3n) {
    border-radius: 48px 48px 64px 36px / 48px 48px 36px 64px;
}
@media (hover: hover) and (pointer: fine) {
    #<?php echo esc_attr($instance_id); ?> .fb-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--fb-shadow-md);
        border-color: var(--fb-leaf);
    }
}

/* Decorative leaf in upper-right of each card */
#<?php echo esc_attr($instance_id); ?> .fb-card::before {
    content: "";
    position: absolute;
    top: -28px;
    right: -28px;
    width: 110px;
    height: 110px;
    background: var(--fb-leaf-soft);
    border-radius: 50% 30% 60% 40% / 40% 60% 30% 50%;
    opacity: 0.7;
    z-index: 0;
}

#<?php echo esc_attr($instance_id); ?> .fb-card > * { position: relative; z-index: 1; }

#<?php echo esc_attr($instance_id); ?> .fb-date {
    font-family: "Fraunces", Georgia, serif;
    font-variation-settings: "opsz" 72;
    font-size: 1.7rem;
    font-weight: 700;
    color: var(--fb-ink);
    margin: 0 0 22px 0;
    line-height: 1.05;
    letter-spacing: -0.01em;
}
#<?php echo esc_attr($instance_id); ?> .fb-meta {
    display: flex;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 22px;
}
#<?php echo esc_attr($instance_id); ?> .fb-meta-label {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--fb-body);
    margin-bottom: 5px;
}
#<?php echo esc_attr($instance_id); ?> .fb-meta-value {
    font-size: 14px;
    font-weight: 600;
    color: var(--fb-ink);
}
#<?php echo esc_attr($instance_id); ?> .fb-avail { white-space: nowrap; display: inline-flex; align-items: center; gap: 9px; }
#<?php echo esc_attr($instance_id); ?> .fb-sprout {
    width: 14px;
    height: 14px;
    flex-shrink: 0;
}

#<?php echo esc_attr($instance_id); ?> .fb-acc-btn {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: var(--fb-leaf-soft);
    border: none;
    border-radius: 14px 18px 14px 18px;
    padding: 13px 18px;
    font-size: 14px;
    font-weight: 700;
    color: var(--fb-green);
    cursor: pointer;
    font-family: inherit;
    transition: background 0.15s;
}
@media (hover: hover) and (pointer: fine) {
    #<?php echo esc_attr($instance_id); ?> .fb-acc-btn:hover { background: #D4E9BD; }
}
#<?php echo esc_attr($instance_id); ?> .fb-acc-icon {
    width: 18px;
    height: 18px;
    color: var(--fb-green);
    transition: transform 0.2s;
}
#<?php echo esc_attr($instance_id); ?> .fb-lesson {
    background: var(--fb-bg);
    border-radius: 14px 18px;
    padding: 12px 16px;
    font-size: 14px;
    margin-top: 8px;
    border-left: 3px solid var(--fb-leaf);
}
#<?php echo esc_attr($instance_id); ?> .fb-lesson-name {
    font-weight: 600;
    color: var(--fb-ink);
    margin-bottom: 4px;
}
#<?php echo esc_attr($instance_id); ?> .fb-lesson-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    font-size: 12px;
    color: var(--fb-body);
}
#<?php echo esc_attr($instance_id); ?> .fb-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-top: auto;
    padding: 10px 20px;
    background: var(--fb-green);
    color: #fff;
    font-size: 14px;
    font-weight: 700;
    text-decoration: none;
    border-radius: 9999px;
    box-shadow: 0 8px 20px -10px rgba(78, 120, 71, 0.55);
    transition: background 0.15s ease, transform 0.15s ease;
}
@media (hover: hover) and (pointer: fine) {
    #<?php echo esc_attr($instance_id); ?> .fb-btn:hover {
        background: var(--fb-green-dark);
        transform: translateY(-1px);
    }
}
#<?php echo esc_attr($instance_id); ?> .fb-btn:active { transform: translateY(0); }
#<?php echo esc_attr($instance_id); ?> .fb-btn svg { width: 14px; height: 14px; }

#<?php echo esc_attr($instance_id); ?> .fb-show-all {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 14px 34px;
    background: transparent;
    color: var(--fb-green);
    border: 1.5px solid var(--fb-green);
    border-radius: 9999px;
    font-size: 14px;
    font-weight: 700;
    font-family: inherit;
    cursor: pointer;
    transition: background 0.2s, color 0.2s;
}
@media (hover: hover) and (pointer: fine) {
    #<?php echo esc_attr($instance_id); ?> .fb-show-all:hover { background: var(--fb-green); color: #fff; }
}
#<?php echo esc_attr($instance_id); ?> .fb-show-all svg { width: 15px; height: 15px; }

@media (max-width: 640px) {
    #<?php echo esc_attr($instance_id); ?> .fb-wrap { padding: 52px 20px; }
    #<?php echo esc_attr($instance_id); ?> .fb-controls { width: 100%; gap: 14px; }
    #<?php echo esc_attr($instance_id); ?> .fb-select { width: 100%; min-width: 0; }
    #<?php echo esc_attr($instance_id); ?> .fb-toggle { width: 100%; justify-content: space-between; }
    #<?php echo esc_attr($instance_id); ?> .fb-card { border-radius: 36px 44px 36px 44px / 44px 36px 44px 36px; }
}
</style>

<div id="<?php echo esc_attr($instance_id); ?>">
    <div class="fb-wrap">

        <div class="fb-head">
            <h2 class="fb-title">
                <svg class="fb-title-leaf" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M3 21c0-9 6-15 18-18-1 12-7 18-18 18z" fill="#91d66b"/>
                    <path d="M3 21c4-4 8-8 14-12" stroke="#4e7847" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                Start<em>data</em>
            </h2>
            <div class="fb-controls">
                <select id="<?php echo esc_attr($instance_id); ?>-location" class="fb-select" aria-label="Filter op locatie">
                    <option value="">Alle locaties</option>
                    <?php foreach ($locations as $loc): ?>
                        <option value="<?php echo esc_attr($loc); ?>"><?php echo esc_html($loc); ?></option>
                    <?php endforeach; ?>
                </select>
                <label class="fb-toggle">
                    <span>Toon enkel beschikbaar</span>
                    <input type="checkbox" id="<?php echo esc_attr($instance_id); ?>-avail" checked aria-label="Toon alleen beschikbare trainingen">
                    <span class="fb-toggle-track"><span class="fb-toggle-knob"></span></span>
                </label>
            </div>
        </div>

        <?php $card_index = 0; ?>
        <?php foreach ($grouped as $year => $year_items): ?>

            <div class="fb-year fb-year-sep">
                <span class="fb-year-label"><?php echo esc_html($year); ?></span>
                <svg class="fb-year-wave" preserveAspectRatio="none" viewBox="0 0 400 14" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M0 7 Q 25 0, 50 7 T 100 7 T 150 7 T 200 7 T 250 7 T 300 7 T 350 7 T 400 7" stroke="currentColor" stroke-width="1.6" fill="none" stroke-linecap="round"/>
                </svg>
            </div>

            <div class="fb-grid fb-year-grid">
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
                    $hidden_class = $card_index >= $initial_visible ? 'fb-extra' : '';
                    $card_index++;
                ?>

                <div class="fb-card fb-sd-card <?php echo esc_attr($hidden_class); ?>"
                     <?php if ($hidden_class): ?>style="display:none;"<?php endif; ?>
                     data-location="<?php echo esc_attr($location); ?>"
                     data-available="<?php echo esc_attr($is_available ? '1' : '0'); ?>">

                    <div class="fb-date"><?php echo esc_html($start_display); ?></div>

                    <div class="fb-meta">
                        <div>
                            <div class="fb-meta-label">Locatie</div>
                            <div class="fb-meta-value"><?php echo esc_html($location); ?></div>
                        </div>
                        <div>
                            <div class="fb-meta-label">Beschikbaarheid</div>
                            <div class="fb-meta-value fb-avail">
                                <?php if ($is_available): ?>
                                    <svg class="fb-sprout" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M12 22V11" stroke="#4e7847" stroke-width="2" stroke-linecap="round"/>
                                        <path d="M12 11C12 11 6 8 6 3C11 3 12 8 12 11Z" fill="#91d66b"/>
                                        <path d="M12 13C12 13 18 10 18 5C13 5 12 10 12 13Z" fill="#91d66b"/>
                                    </svg>
                                <?php else: ?>
                                    <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:var(--fb-bad);"></span>
                                <?php endif; ?>
                                <?php echo esc_html($avail_text); ?>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($lessons)): ?>
                    <div style="margin-bottom: 22px;">
                        <button class="fb-acc-btn fb-acc-trigger" data-target="<?php echo esc_attr($accordion_id); ?>" type="button" aria-expanded="false" aria-controls="<?php echo esc_attr($accordion_id); ?>">
                            <span>Planning modules</span>
                            <svg class="fb-acc-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                            </svg>
                        </button>
                        <div id="<?php echo esc_attr($accordion_id); ?>" style="display: none;">
                            <?php foreach ($lessons as $lesson): ?>
                                <div class="fb-lesson">
                                    <div class="fb-lesson-name"><?php echo esc_html($lesson['lesson'] ?? ''); ?></div>
                                    <div class="fb-lesson-meta">
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

                    <a href="<?php echo esc_url($link); ?>" class="fb-btn">
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
            <button id="<?php echo esc_attr($instance_id); ?>-show-all" class="fb-show-all" type="button">
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
        var cards = root.querySelectorAll(".fb-sd-card");
        var allRevealed = false;

        function applyFilters() {
            var locVal = locationFilter ? locationFilter.value : "";
            var availOnly = availToggle ? availToggle.checked : false;

            cards.forEach(function(card) {
                var matchLoc = !locVal || card.dataset.location === locVal;
                var matchAvail = !availOnly || card.dataset.available === "1";
                var isExtra = card.classList.contains("fb-extra") && !allRevealed;
                card.style.display = (matchLoc && matchAvail && !isExtra) ? "" : "none";
            });

            root.querySelectorAll(".fb-year-grid").forEach(function(grid) {
                var sep = grid.previousElementSibling;
                var hasVisible = Array.from(grid.querySelectorAll(".fb-sd-card")).some(function(c) {
                    return c.style.display !== "none";
                });
                if (sep && sep.classList.contains("fb-year-sep")) {
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
                cards.forEach(function(card) { card.classList.remove("fb-extra"); });
                if (showAllWrap) showAllWrap.style.display = "none";
                applyFilters();
            });
        }

        root.querySelectorAll(".fb-acc-trigger").forEach(function(btn) {
            btn.addEventListener("click", function() {
                var panel = document.getElementById(this.dataset.target);
                var icon = this.querySelector(".fb-acc-icon");
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
