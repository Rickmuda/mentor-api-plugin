<?php
/**
 * Skin: time-management - Startdata-agenda ("Calm focus", planner-thema)
 * Verwacht dezelfde variabelen als templates/startdata.php: $tracks, $api_url
 */
defined('ABSPATH') or die('No script kiddies please!');

$items = $tracks['results'] ?? $tracks;

if (empty($items) || !is_array($items)) {
    echo '<p>Geen startdata gevonden.</p>';
    return;
}

if (!wp_style_is('mentor-time-management-fonts', 'enqueued')) {
    wp_enqueue_style(
        'mentor-time-management-fonts',
        'https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;600;700&display=swap',
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

$instance_id = 'tm-startdata-' . wp_unique_id();
$initial_visible = 6;
?>

<style>
#<?php echo esc_attr($instance_id); ?> {
    --tm-ink: #1A1F36;
    --tm-body: #525A75;
    --tm-muted: #8A8F9F;
    --tm-indigo: #2D3FB5;
    --tm-indigo-dark: #1F2C8A;
    --tm-indigo-soft: #E5E8FA;
    --tm-amber: #E8930C;
    --tm-amber-soft: #FBE9C8;
    --tm-line: #E7E2D5;
    --tm-line-soft: #F0EBDF;
    --tm-surface: #FFFFFF;
    --tm-ok: #16A34A;
    --tm-bad: #DC2626;
    --tm-shadow-sm: 0 4px 12px -4px rgba(26, 31, 54, 0.10);

    background: transparent;
    color: var(--tm-ink);
    font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    -webkit-font-smoothing: antialiased;
}
#<?php echo esc_attr($instance_id); ?> * { box-sizing: border-box; }
#<?php echo esc_attr($instance_id); ?> .tm-wrap { max-width: 100%; margin: 0; padding: 0; }
#<?php echo esc_attr($instance_id); ?> .tm-head {
    display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between;
    gap: 24px; margin-bottom: 36px;
}
#<?php echo esc_attr($instance_id); ?> .tm-title {
    font-family: "Outfit", sans-serif;
    font-size: clamp(1.8rem, 1.3rem + 1.8vw, 2.6rem);
    font-weight: 700; letter-spacing: -0.02em; color: var(--tm-ink);
    margin: 0; line-height: 1.05;
}
#<?php echo esc_attr($instance_id); ?> .tm-title span { color: var(--tm-indigo); }
#<?php echo esc_attr($instance_id); ?> .tm-controls {
    display: flex; flex-wrap: wrap; align-items: center; gap: 16px;
}
#<?php echo esc_attr($instance_id); ?> .tm-select {
    border: 1px solid var(--tm-line); background: var(--tm-surface); color: var(--tm-ink);
    border-radius: 10px; padding: 11px 38px 11px 16px;
    font-size: 14px; font-family: inherit; min-width: 200px; cursor: pointer;
    box-shadow: var(--tm-shadow-sm);
    appearance: none; -webkit-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='none' viewBox='0 0 24 24' stroke='%232D3FB5' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19.5 8.25l-7.5 7.5-7.5-7.5'/%3E%3C/svg%3E");
    background-repeat: no-repeat; background-position: right 14px center;
}
#<?php echo esc_attr($instance_id); ?> .tm-toggle {
    display: inline-flex; align-items: center; gap: 12px; cursor: pointer; user-select: none;
    font-size: 14px; color: var(--tm-ink);
}
#<?php echo esc_attr($instance_id); ?> .tm-toggle input { position: absolute; opacity: 0; width: 0; height: 0; }
#<?php echo esc_attr($instance_id); ?> .tm-toggle-track {
    width: 42px; height: 24px; background: #CFD3DD; border-radius: 12px;
    position: relative; transition: background .2s; flex-shrink: 0;
}
#<?php echo esc_attr($instance_id); ?> .tm-toggle input:checked + .tm-toggle-track { background: var(--tm-indigo); }
#<?php echo esc_attr($instance_id); ?> .tm-toggle-knob {
    width: 18px; height: 18px; background: #fff; border-radius: 50%;
    position: absolute; top: 3px; left: 3px; transition: transform .2s;
    box-shadow: 0 1px 3px rgba(26,31,54,0.25);
}
#<?php echo esc_attr($instance_id); ?> .tm-toggle input:checked + .tm-toggle-track .tm-toggle-knob { transform: translateX(18px); }

#<?php echo esc_attr($instance_id); ?> .tm-year {
    display: flex; align-items: center; gap: 16px;
    margin: 8px 0 22px 0;
}
#<?php echo esc_attr($instance_id); ?> .tm-year-label {
    font-family: "JetBrains Mono", ui-monospace, SFMono-Regular, monospace;
    font-size: 13px; font-weight: 600; color: var(--tm-indigo);
    letter-spacing: 0.04em; white-space: nowrap;
}
#<?php echo esc_attr($instance_id); ?> .tm-year-line { flex-grow: 1; height: 1px; background: var(--tm-line); }

#<?php echo esc_attr($instance_id); ?> .tm-grid {
    display: grid; grid-template-columns: repeat(3, 1fr);
    gap: 20px; margin-bottom: 32px; align-items: start;
}
@media (max-width: 1024px) { #<?php echo esc_attr($instance_id); ?> .tm-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 640px)  { #<?php echo esc_attr($instance_id); ?> .tm-grid { grid-template-columns: 1fr; } }

#<?php echo esc_attr($instance_id); ?> .tm-card {
    background: var(--tm-surface);
    border: 1.5px solid var(--tm-line);
    border-radius: 18px;
    box-shadow: var(--tm-shadow-sm);
    padding: 24px 26px;
    display: flex; flex-direction: column;
    transition: transform .2s, box-shadow .2s, border-color .2s;
    position: relative;
}
@media (hover: hover) and (pointer: fine) {
    #<?php echo esc_attr($instance_id); ?> .tm-card:hover {
        transform: translateY(-3px);
        border-color: var(--tm-indigo-soft);
        box-shadow: 0 18px 36px -20px rgba(26, 31, 54, 0.22);
    }
}

/* Datum-blok als ticker-strip: dag-nummer groot, maand mono. */
#<?php echo esc_attr($instance_id); ?> .tm-date-block {
    display: flex; align-items: baseline; gap: 12px;
    padding-bottom: 18px; margin-bottom: 18px;
    border-bottom: 1px solid var(--tm-line);
}
#<?php echo esc_attr($instance_id); ?> .tm-date-day {
    font-family: "Outfit", sans-serif;
    font-size: 2.6rem; font-weight: 700; color: var(--tm-ink);
    line-height: 1; letter-spacing: -0.02em;
}
#<?php echo esc_attr($instance_id); ?> .tm-date-month {
    font-family: "JetBrains Mono", monospace;
    font-size: 12px; font-weight: 600; color: var(--tm-indigo);
    text-transform: uppercase; letter-spacing: 0.06em;
}
#<?php echo esc_attr($instance_id); ?> .tm-meta {
    display: grid; grid-template-columns: 1fr 1fr; gap: 14px;
    margin-bottom: 18px;
}
#<?php echo esc_attr($instance_id); ?> .tm-meta-label {
    font-family: "JetBrains Mono", monospace;
    font-size: 10px; font-weight: 600; letter-spacing: 0.06em;
    text-transform: uppercase; color: var(--tm-muted); margin-bottom: 5px;
}
#<?php echo esc_attr($instance_id); ?> .tm-meta-value { font-size: 14px; font-weight: 600; color: var(--tm-ink); }
#<?php echo esc_attr($instance_id); ?> .tm-avail { white-space: nowrap; }
#<?php echo esc_attr($instance_id); ?> .tm-avail-dot {
    display: inline-block; width: 8px; height: 8px; border-radius: 50%;
    margin-right: 8px; vertical-align: middle;
}
#<?php echo esc_attr($instance_id); ?> .tm-acc-btn {
    width: 100%; display: flex; align-items: center; justify-content: space-between;
    background: var(--tm-line-soft); border: none; border-radius: 10px;
    padding: 11px 14px; font-size: 13px; font-weight: 600; color: var(--tm-indigo);
    cursor: pointer; font-family: inherit; transition: background .15s;
    margin-bottom: 16px;
}
#<?php echo esc_attr($instance_id); ?> .tm-acc-panel { display: none; margin-bottom: 16px; }
@media (hover: hover) and (pointer: fine) {
    #<?php echo esc_attr($instance_id); ?> .tm-acc-btn { cursor: default; }
    #<?php echo esc_attr($instance_id); ?> .tm-acc:hover .tm-acc-btn { background: var(--tm-indigo-soft); }
    #<?php echo esc_attr($instance_id); ?> .tm-acc:hover .tm-acc-panel,
    #<?php echo esc_attr($instance_id); ?> .tm-acc:focus-within .tm-acc-panel { display: block; }
    #<?php echo esc_attr($instance_id); ?> .tm-acc:hover .tm-acc-icon,
    #<?php echo esc_attr($instance_id); ?> .tm-acc:focus-within .tm-acc-icon { transform: rotate(45deg); }
}
#<?php echo esc_attr($instance_id); ?> .tm-acc-icon { width: 16px; height: 16px; color: var(--tm-indigo); transition: transform .2s; }
#<?php echo esc_attr($instance_id); ?> .tm-lesson {
    background: var(--tm-line-soft); border-radius: 10px; padding: 11px 14px;
    font-size: 13.5px; margin-top: 6px;
}
#<?php echo esc_attr($instance_id); ?> .tm-lesson-name { font-weight: 600; color: var(--tm-ink); margin-bottom: 4px; }
#<?php echo esc_attr($instance_id); ?> .tm-lesson-meta {
    display: flex; flex-wrap: wrap; gap: 10px;
    font-family: "JetBrains Mono", monospace; font-size: 11px; color: var(--tm-body);
}
#<?php echo esc_attr($instance_id); ?> .tm-btn {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    margin-top: auto; padding: 12px 24px;
    background: var(--tm-indigo); color: #fff;
    font-size: 14px; font-weight: 700; text-decoration: none;
    border-radius: 9999px; transition: background .2s, transform .15s;
}
@media (hover: hover) and (pointer: fine) {
    #<?php echo esc_attr($instance_id); ?> .tm-btn:hover { background: var(--tm-indigo-dark); transform: translateY(-1px); }
}
#<?php echo esc_attr($instance_id); ?> .tm-btn svg { width: 15px; height: 15px; }

#<?php echo esc_attr($instance_id); ?> .tm-show-all {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 13px 32px; background: var(--tm-surface);
    color: var(--tm-indigo); border: 1.5px solid var(--tm-indigo);
    border-radius: 9999px; font-size: 14px; font-weight: 700; font-family: inherit;
    cursor: pointer; transition: background .2s, color .2s;
}
@media (hover: hover) and (pointer: fine) {
    #<?php echo esc_attr($instance_id); ?> .tm-show-all:hover { background: var(--tm-indigo); color: #fff; }
}
#<?php echo esc_attr($instance_id); ?> .tm-show-all svg { width: 15px; height: 15px; }

@media (max-width: 640px) {
    #<?php echo esc_attr($instance_id); ?> .tm-controls { width: 100%; gap: 12px; }
    #<?php echo esc_attr($instance_id); ?> .tm-select { width: 100%; min-width: 0; }
    #<?php echo esc_attr($instance_id); ?> .tm-toggle { width: 100%; justify-content: space-between; }
}
</style>

<div id="<?php echo esc_attr($instance_id); ?>">
    <div class="tm-wrap">

        <div class="tm-head">
            <h2 class="tm-title">Start<span>data</span></h2>
            <div class="tm-controls">
                <select id="<?php echo esc_attr($instance_id); ?>-location" class="tm-select" aria-label="Filter op locatie">
                    <option value="">Alle locaties</option>
                    <?php foreach ($locations as $loc): ?>
                        <option value="<?php echo esc_attr($loc); ?>"><?php echo esc_html($loc); ?></option>
                    <?php endforeach; ?>
                </select>
                <label class="tm-toggle">
                    <span>Toon enkel beschikbaar</span>
                    <input type="checkbox" id="<?php echo esc_attr($instance_id); ?>-avail" aria-label="Toon alleen beschikbare trainingen">
                    <span class="tm-toggle-track"><span class="tm-toggle-knob"></span></span>
                </label>
            </div>
        </div>

        <?php $card_index = 0; ?>
        <?php foreach ($grouped as $year => $year_items): ?>

            <div class="tm-year tm-year-sep">
                <span class="tm-year-label"><?php echo esc_html($year); ?></span>
                <span class="tm-year-line"></span>
            </div>

            <div class="tm-grid tm-year-grid">
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
                    $start_day = $start_raw ? date_i18n('j', strtotime($start_raw)) : '';
                    $start_month = $start_raw ? date_i18n('M Y', strtotime($start_raw)) : ($track['training_start'] ?? '');
                    $hidden_class = $card_index >= $initial_visible ? 'tm-extra' : '';
                    $card_index++;
                ?>

                <div class="tm-card tm-sd-card <?php echo esc_attr($hidden_class); ?>"
                     <?php if ($hidden_class): ?>style="display:none;"<?php endif; ?>
                     data-location="<?php echo esc_attr($location); ?>"
                     data-available="<?php echo esc_attr($is_available ? '1' : '0'); ?>">

                    <div class="tm-date-block">
                        <span class="tm-date-day"><?php echo esc_html($start_day); ?></span>
                        <span class="tm-date-month"><?php echo esc_html($start_month); ?></span>
                    </div>

                    <div class="tm-meta">
                        <div>
                            <div class="tm-meta-label">Locatie</div>
                            <div class="tm-meta-value"><?php echo esc_html($location); ?></div>
                        </div>
                        <div>
                            <div class="tm-meta-label">Status</div>
                            <div class="tm-meta-value tm-avail">
                                <span class="tm-avail-dot" style="background: <?php echo esc_attr($is_available ? 'var(--tm-ok)' : 'var(--tm-bad)'); ?>;"></span><?php echo esc_html($avail_text); ?>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($lessons)): ?>
                        <div class="tm-acc">
                        <button class="tm-acc-btn tm-acc-trigger" data-target="<?php echo esc_attr($accordion_id); ?>" type="button" aria-expanded="false" aria-controls="<?php echo esc_attr($accordion_id); ?>">
                            <span>Planning modules</span>
                            <svg class="tm-acc-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                            </svg>
                        </button>
                        <div id="<?php echo esc_attr($accordion_id); ?>" class="tm-acc-panel">
                            <?php foreach ($lessons as $lesson): ?>
                                <div class="tm-lesson">
                                    <div class="tm-lesson-name"><?php echo esc_html($lesson['lesson'] ?? ''); ?></div>
                                    <div class="tm-lesson-meta">
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

                    <a href="<?php echo esc_url($link); ?>" class="tm-btn">
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
            <button id="<?php echo esc_attr($instance_id); ?>-show-all" class="tm-show-all" type="button">
                Toon alle data
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                </svg>
            </button>
        </div>
        <?php endif; ?>

        <p class="tm-empty" id="<?php echo esc_attr($instance_id); ?>-empty" style="display:none;text-align:center;color:var(--tm-body);margin:8px 0 0;font-size:14px;">Geen beschikbare startdata op dit moment - zet de filter uit om alle datums te zien.</p>

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
        var cards = root.querySelectorAll(".tm-sd-card");
        var emptyMsg = root.querySelector("#<?php echo esc_attr($instance_id); ?>-empty");
        var allRevealed = false;

        function applyFilters() {
            var locVal = locationFilter ? locationFilter.value : "";
            var availOnly = availToggle ? availToggle.checked : false;

            cards.forEach(function(card) {
                var matchLoc = !locVal || card.dataset.location === locVal;
                var matchAvail = !availOnly || card.dataset.available === "1";
                var isExtra = card.classList.contains("tm-extra") && !allRevealed;
                card.style.display = (matchLoc && matchAvail && !isExtra) ? "" : "none";
            });

            root.querySelectorAll(".tm-year-grid").forEach(function(grid) {
                var sep = grid.previousElementSibling;
                var hasVisible = Array.from(grid.querySelectorAll(".tm-sd-card")).some(function(c) {
                    return c.style.display !== "none";
                });
                if (sep && sep.classList.contains("tm-year-sep")) {
                    sep.style.display = hasVisible ? "" : "none";
                }
                grid.style.display = hasVisible ? "" : "none";
            });

            var anyVisible = Array.prototype.some.call(cards, function(c) { return c.style.display !== "none"; });
            if (emptyMsg) emptyMsg.style.display = anyVisible ? "none" : "";
        }

        if (locationFilter) locationFilter.addEventListener("change", applyFilters);
        if (availToggle) availToggle.addEventListener("change", applyFilters);
        applyFilters();

        if (showAllBtn) {
            showAllBtn.addEventListener("click", function() {
                allRevealed = true;
                cards.forEach(function(card) { card.classList.remove("tm-extra"); });
                if (showAllWrap) showAllWrap.style.display = "none";
                applyFilters();
            });
        }

        // Op hover-apparaten opent het paneel via CSS (:hover/:focus-within); de
        // klik-toggle is alleen nog een tap-fallback voor touch (geen hover).
        var canHover = window.matchMedia("(hover: hover) and (pointer: fine)").matches;
        if (!canHover) {
            root.querySelectorAll(".tm-acc-trigger").forEach(function(btn) {
                btn.addEventListener("click", function() {
                    var panel = document.getElementById(this.dataset.target);
                    var icon = this.querySelector(".tm-acc-icon");
                    if (panel) {
                        var isOpen = panel.style.display === "block";
                        panel.style.display = isOpen ? "none" : "block";
                        this.setAttribute("aria-expanded", isOpen ? "false" : "true");
                        if (icon) icon.style.transform = isOpen ? "" : "rotate(45deg)";
                    }
                });
            });
        }
    });
})();
</script>
