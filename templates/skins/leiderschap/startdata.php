<?php
/**
 * Skin: leiderschap — Startdata-agenda ("Fris & professioneel", zodan-stijl)
 * Verwacht dezelfde variabelen als templates/startdata.php: $tracks, $api_url
 */
defined('ABSPATH') or die('No script kiddies please!');

$items = $tracks['results'] ?? $tracks;

if (empty($items) || !is_array($items)) {
    echo '<p>Geen startdata gevonden.</p>';
    return;
}

if (!wp_style_is('mentor-leiderschap-fonts', 'enqueued')) {
    wp_enqueue_style(
        'mentor-leiderschap-fonts',
        'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap',
        array(),
        null
    );
}

// Sorteer op ISO-startdatum
usort($items, function ($a, $b) {
    return strcmp($a['training_start_raw'] ?? '', $b['training_start_raw'] ?? '');
});

// Unieke locaties voor het filter
$locations = [];
foreach ($items as $item) {
    $loc = $item['default_location']['label'] ?? ($item['traininglessons'][0]['location_label_no_room'] ?? '');
    if (!empty($loc) && !in_array($loc, $locations)) {
        $locations[] = $loc;
    }
}
sort($locations);

// Groepeer per jaar
$grouped = [];
foreach ($items as $item) {
    $raw = $item['training_start_raw'] ?? '';
    $year = $raw ? gmdate('Y', strtotime($raw)) : gmdate('Y');
    $grouped[$year][] = $item;
}
ksort($grouped);

$instance_id = 'lc-startdata-' . wp_unique_id();
$initial_visible = 6;
?>

<style>
#<?php echo esc_attr($instance_id); ?> {
    --lc-bg: #F7F8FA;
    --lc-surface: #FFFFFF;
    --lc-ink: #16202C;
    --lc-body: #586575;
    --lc-blue: #2F6BFF;
    --lc-blue-dark: #2456D6;
    --lc-blue-soft: #EAF0FF;
    --lc-line: #E6EAF0;
    --lc-ok: #16A34A;
    --lc-bad: #DC2626;
    --lc-shadow-sm: 0 2px 6px rgba(22, 32, 44, 0.06);

    background: transparent;
    color: var(--lc-ink);
    font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    -webkit-font-smoothing: antialiased;
}
#<?php echo esc_attr($instance_id); ?> * { box-sizing: border-box; }
#<?php echo esc_attr($instance_id); ?> .lc-wrap {
    max-width: 100%;
    margin: 0;
    padding: 0;
}
#<?php echo esc_attr($instance_id); ?> .lc-head {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-end;
    justify-content: space-between;
    gap: 24px;
    margin-bottom: 40px;
}
#<?php echo esc_attr($instance_id); ?> .lc-title {
    font-family: "Plus Jakarta Sans", "Inter", sans-serif;
    font-size: clamp(2rem, 1.4rem + 2vw, 2.9rem);
    font-weight: 800;
    letter-spacing: -0.02em;
    color: var(--lc-ink);
    margin: 0;
    line-height: 1.05;
}
#<?php echo esc_attr($instance_id); ?> .lc-title span { color: var(--lc-blue); }
#<?php echo esc_attr($instance_id); ?> .lc-controls {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 18px;
}
#<?php echo esc_attr($instance_id); ?> .lc-select {
    border: 1px solid var(--lc-line);
    background: var(--lc-surface);
    color: var(--lc-ink);
    border-radius: 9999px;
    padding: 12px 44px 12px 18px;
    font-size: 14px;
    font-family: inherit;
    min-width: 200px;
    cursor: pointer;
    box-shadow: var(--lc-shadow-sm);
    appearance: none;
    -webkit-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='none' viewBox='0 0 24 24' stroke='%232F6BFF' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19.5 8.25l-7.5 7.5-7.5-7.5'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 16px center;
}
#<?php echo esc_attr($instance_id); ?> .lc-toggle {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    cursor: pointer;
    user-select: none;
    font-size: 14px;
    color: var(--lc-ink);
}
#<?php echo esc_attr($instance_id); ?> .lc-toggle input {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}
#<?php echo esc_attr($instance_id); ?> .lc-toggle-track {
    width: 44px;
    height: 26px;
    background: #cdd5e0;
    border-radius: 13px;
    position: relative;
    transition: background 0.2s;
    flex-shrink: 0;
}
#<?php echo esc_attr($instance_id); ?> .lc-toggle input:checked + .lc-toggle-track { background: var(--lc-blue); }
#<?php echo esc_attr($instance_id); ?> .lc-toggle-knob {
    width: 20px;
    height: 20px;
    background: #fff;
    border-radius: 50%;
    position: absolute;
    top: 3px;
    left: 3px;
    transition: transform 0.2s;
    box-shadow: 0 1px 3px rgba(22,32,44,0.25);
}
#<?php echo esc_attr($instance_id); ?> .lc-toggle input:checked + .lc-toggle-track .lc-toggle-knob { transform: translateX(18px); }
#<?php echo esc_attr($instance_id); ?> .lc-year {
    display: flex;
    align-items: center;
    gap: 16px;
    margin: 8px 0 22px 0;
}
#<?php echo esc_attr($instance_id); ?> .lc-year-label {
    font-family: "Plus Jakarta Sans", "Inter", sans-serif;
    font-size: 15px;
    font-weight: 700;
    color: var(--lc-blue);
    white-space: nowrap;
}
#<?php echo esc_attr($instance_id); ?> .lc-year-line {
    flex-grow: 1;
    height: 1px;
    background: var(--lc-line);
}
#<?php echo esc_attr($instance_id); ?> .lc-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 22px;
    margin-bottom: 36px;
}
@media (max-width: 1024px) { #<?php echo esc_attr($instance_id); ?> .lc-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 640px)  { #<?php echo esc_attr($instance_id); ?> .lc-grid { grid-template-columns: 1fr; } }
#<?php echo esc_attr($instance_id); ?> .lc-card {
    background: var(--lc-surface);
    border: 1px solid var(--lc-line);
    border-radius: 20px;
    box-shadow: var(--lc-shadow-sm);
    padding: 28px;
    display: flex;
    flex-direction: column;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
@media (hover: hover) and (pointer: fine) {
    #<?php echo esc_attr($instance_id); ?> .lc-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 18px 36px -20px rgba(22, 32, 44, 0.28);
    }
}
#<?php echo esc_attr($instance_id); ?> .lc-date {
    font-family: "Plus Jakarta Sans", "Inter", sans-serif;
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--lc-ink);
    margin: 0 0 20px 0;
    line-height: 1.15;
}
#<?php echo esc_attr($instance_id); ?> .lc-meta {
    display: flex;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 22px;
}
#<?php echo esc_attr($instance_id); ?> .lc-meta-label {
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: var(--lc-body);
    margin-bottom: 5px;
}
#<?php echo esc_attr($instance_id); ?> .lc-meta-value {
    font-size: 14px;
    font-weight: 600;
    color: var(--lc-ink);
}
#<?php echo esc_attr($instance_id); ?> .lc-avail { white-space: nowrap; }
#<?php echo esc_attr($instance_id); ?> .lc-avail-dot {
    display: inline-block;
    width: 9px;
    height: 9px;
    border-radius: 50%;
    margin-right: 8px;
    vertical-align: middle;
}
#<?php echo esc_attr($instance_id); ?> .lc-acc-btn {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: var(--lc-blue-soft);
    border: none;
    border-radius: 12px;
    padding: 13px 16px;
    font-size: 14px;
    font-weight: 600;
    color: var(--lc-blue);
    cursor: pointer;
    font-family: inherit;
    transition: background 0.15s;
}
@media (hover: hover) and (pointer: fine) {
    #<?php echo esc_attr($instance_id); ?> .lc-acc-btn:hover { background: #dde7ff; }
}
#<?php echo esc_attr($instance_id); ?> .lc-acc-icon {
    width: 18px;
    height: 18px;
    color: var(--lc-blue);
    transition: transform 0.2s;
}
#<?php echo esc_attr($instance_id); ?> .lc-lesson {
    background: var(--lc-bg);
    border-radius: 12px;
    padding: 12px 16px;
    font-size: 14px;
    margin-top: 8px;
}
#<?php echo esc_attr($instance_id); ?> .lc-lesson-name {
    font-weight: 600;
    color: var(--lc-ink);
    margin-bottom: 4px;
}
#<?php echo esc_attr($instance_id); ?> .lc-lesson-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    font-size: 12px;
    color: var(--lc-body);
}
#<?php echo esc_attr($instance_id); ?> .lc-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-top: auto;
    padding: 13px 28px;
    background: var(--lc-blue);
    color: #fff;
    font-size: 14px;
    font-weight: 700;
    text-decoration: none;
    border-radius: 9999px;
    transition: background 0.2s;
}
@media (hover: hover) and (pointer: fine) {
    #<?php echo esc_attr($instance_id); ?> .lc-btn:hover { background: var(--lc-blue-dark); }
}
#<?php echo esc_attr($instance_id); ?> .lc-btn svg { width: 15px; height: 15px; }
#<?php echo esc_attr($instance_id); ?> .lc-show-all {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 14px 34px;
    background: var(--lc-surface);
    color: var(--lc-blue);
    border: 1px solid var(--lc-blue);
    border-radius: 9999px;
    font-size: 14px;
    font-weight: 700;
    font-family: inherit;
    cursor: pointer;
    transition: background 0.2s, color 0.2s;
}
@media (hover: hover) and (pointer: fine) {
    #<?php echo esc_attr($instance_id); ?> .lc-show-all:hover { background: var(--lc-blue); color: #fff; }
}
#<?php echo esc_attr($instance_id); ?> .lc-show-all svg { width: 15px; height: 15px; }

@media (max-width: 640px) {
    #<?php echo esc_attr($instance_id); ?> .lc-wrap { padding: 52px 20px; }
    #<?php echo esc_attr($instance_id); ?> .lc-controls { width: 100%; gap: 14px; }
    #<?php echo esc_attr($instance_id); ?> .lc-select { width: 100%; min-width: 0; }
    #<?php echo esc_attr($instance_id); ?> .lc-toggle { width: 100%; justify-content: space-between; }
}
</style>

<div id="<?php echo esc_attr($instance_id); ?>">
    <div class="lc-wrap">

        <div class="lc-head">
            <h2 class="lc-title">Start<span>data</span></h2>
            <div class="lc-controls">
                <select id="<?php echo esc_attr($instance_id); ?>-location" class="lc-select" aria-label="Filter op locatie">
                    <option value="">Alle locaties</option>
                    <?php foreach ($locations as $loc): ?>
                        <option value="<?php echo esc_attr($loc); ?>"><?php echo esc_html($loc); ?></option>
                    <?php endforeach; ?>
                </select>
                <label class="lc-toggle">
                    <span>Toon enkel beschikbaar</span>
                    <input type="checkbox" id="<?php echo esc_attr($instance_id); ?>-avail" checked aria-label="Toon alleen beschikbare trainingen">
                    <span class="lc-toggle-track"><span class="lc-toggle-knob"></span></span>
                </label>
            </div>
        </div>

        <?php $card_index = 0; ?>
        <?php foreach ($grouped as $year => $year_items): ?>

            <div class="lc-year lc-year-sep">
                <span class="lc-year-label"><?php echo esc_html($year); ?></span>
                <span class="lc-year-line"></span>
            </div>

            <div class="lc-grid lc-year-grid">
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
                    $hidden_class = $card_index >= $initial_visible ? 'lc-extra' : '';
                    $card_index++;
                ?>

                <div class="lc-card lc-sd-card <?php echo esc_attr($hidden_class); ?>"
                     <?php if ($hidden_class): ?>style="display:none;"<?php endif; ?>
                     data-location="<?php echo esc_attr($location); ?>"
                     data-available="<?php echo esc_attr($is_available ? '1' : '0'); ?>">

                    <div class="lc-date"><?php echo esc_html($start_display); ?></div>

                    <div class="lc-meta">
                        <div>
                            <div class="lc-meta-label">Locatie</div>
                            <div class="lc-meta-value"><?php echo esc_html($location); ?></div>
                        </div>
                        <div>
                            <div class="lc-meta-label">Beschikbaarheid</div>
                            <div class="lc-meta-value lc-avail">
                                <span class="lc-avail-dot" style="background: <?php echo esc_attr($is_available ? 'var(--lc-ok)' : 'var(--lc-bad)'); ?>;"></span><?php echo esc_html($avail_text); ?>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($lessons)): ?>
                    <div style="margin-bottom: 22px;">
                        <button class="lc-acc-btn lc-acc-trigger" data-target="<?php echo esc_attr($accordion_id); ?>" type="button" aria-expanded="false" aria-controls="<?php echo esc_attr($accordion_id); ?>">
                            <span>Planning modules</span>
                            <svg class="lc-acc-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                            </svg>
                        </button>
                        <div id="<?php echo esc_attr($accordion_id); ?>" style="display: none;">
                            <?php foreach ($lessons as $lesson): ?>
                                <div class="lc-lesson">
                                    <div class="lc-lesson-name"><?php echo esc_html($lesson['lesson'] ?? ''); ?></div>
                                    <div class="lc-lesson-meta">
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

                    <a href="<?php echo esc_url($link); ?>" class="lc-btn">
                        Inschrijven
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                        </svg>
                    </a>
                </div>

                <?php endforeach; ?>
            </div>

        <?php endforeach; ?>

        <?php if ($card_index > $initial_visible): ?>
        <div style="text-align: center; margin-top: 8px;" id="<?php echo esc_attr($instance_id); ?>-show-all-wrap">
            <button id="<?php echo esc_attr($instance_id); ?>-show-all" class="lc-show-all" type="button">
                Toon alle data
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
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
        var cards = root.querySelectorAll(".lc-sd-card");
        var allRevealed = false;

        function applyFilters() {
            var locVal = locationFilter ? locationFilter.value : "";
            var availOnly = availToggle ? availToggle.checked : false;

            cards.forEach(function(card) {
                var matchLoc = !locVal || card.dataset.location === locVal;
                var matchAvail = !availOnly || card.dataset.available === "1";
                var isExtra = card.classList.contains("lc-extra") && !allRevealed;
                card.style.display = (matchLoc && matchAvail && !isExtra) ? "" : "none";
            });

            root.querySelectorAll(".lc-year-grid").forEach(function(grid) {
                var sep = grid.previousElementSibling;
                var hasVisible = Array.from(grid.querySelectorAll(".lc-sd-card")).some(function(c) {
                    return c.style.display !== "none";
                });
                if (sep && sep.classList.contains("lc-year-sep")) {
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
                cards.forEach(function(card) { card.classList.remove("lc-extra"); });
                if (showAllWrap) showAllWrap.style.display = "none";
                applyFilters();
            });
        }

        root.querySelectorAll(".lc-acc-trigger").forEach(function(btn) {
            btn.addEventListener("click", function() {
                var panel = document.getElementById(this.dataset.target);
                var icon = this.querySelector(".lc-acc-icon");
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
