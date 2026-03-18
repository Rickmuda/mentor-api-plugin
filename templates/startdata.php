<?php
defined('ABSPATH') or die('No script kiddies please!');

$items = $tracks['results'] ?? $tracks;

if (empty($items) || !is_array($items)) {
    echo '<p>Geen startdata gevonden.</p>';
    return;
}

// Sorteer op training_start_raw (ISO datum)
usort($items, function ($a, $b) {
    return strcmp($a['training_start_raw'] ?? '', $b['training_start_raw'] ?? '');
});

// Verzamel unieke locaties voor filter
$locations = [];
foreach ($items as $item) {
    $loc = $item['default_location']['label'] ?? ($item['traininglessons'][0]['location_label_no_room'] ?? '');
    if (!empty($loc) && !in_array($loc, $locations)) {
        $locations[] = $loc;
    }
}
sort($locations);

// Groepeer per jaar op basis van training_start_raw
$grouped = [];
foreach ($items as $item) {
    $raw = $item['training_start_raw'] ?? '';
    $year = $raw ? date('Y', strtotime($raw)) : date('Y');
    $grouped[$year][] = $item;
}
ksort($grouped);

$instance_id = 'startdata-' . wp_unique_id();
$initial_visible = 6;
?>

<style>
#<?php echo $instance_id; ?> .sd-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-bottom: 32px;
}
@media (max-width: 1024px) {
    #<?php echo $instance_id; ?> .sd-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
@media (max-width: 640px) {
    #<?php echo $instance_id; ?> .sd-grid {
        grid-template-columns: 1fr;
    }
    #<?php echo $instance_id; ?> .sd-header {
        flex-direction: column !important;
        align-items: flex-start !important;
    }
}
#<?php echo $instance_id; ?> .sd-card {
    background: #fff;
    border-radius: 16px;
    border: 1px solid #e5e7eb;
    padding: 28px;
    display: flex;
    flex-direction: column;
}
#<?php echo $instance_id; ?> .sd-date {
    font-size: 1.35rem;
    font-weight: 700;
    color: var(--color-primary, #417AB3);
    margin: 0 0 20px 0;
    line-height: 1.3;
}
#<?php echo $instance_id; ?> .sd-meta {
    display: flex;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 24px;
}
#<?php echo $instance_id; ?> .sd-meta-label {
    font-size: 12px;
    color: #9ca3af;
    margin-bottom: 4px;
}
#<?php echo $instance_id; ?> .sd-meta-value {
    font-size: 13px;
    font-weight: 500;
    color: #1f2937;
}
#<?php echo $instance_id; ?> .sd-meta-value-avail {
    white-space: nowrap;
}
#<?php echo $instance_id; ?> .sd-avail-dot {
    display: inline-block;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    margin-right: 8px;
    vertical-align: middle;
}
#<?php echo $instance_id; ?> .sd-accordion-btn {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #f3f4f6;
    border-radius: 12px;
    padding: 14px 16px;
    font-size: 14px;
    font-weight: 600;
    color: #1f2937;
    border: none;
    cursor: pointer;
    transition: background 0.15s;
}
#<?php echo $instance_id; ?> .sd-accordion-btn:hover {
    background: #e5e7eb;
}
#<?php echo $instance_id; ?> .sd-accordion-icon {
    width: 20px;
    height: 20px;
    color: #6b7280;
    transition: transform 0.2s;
}
#<?php echo $instance_id; ?> .sd-btn-inschrijven {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 12px 28px;
    border-radius: 9999px;
    font-size: 14px;
    font-weight: 700;
    color: #fff;
    text-decoration: none;
    background-color: var(--color-primary, #417AB3);
    transition: opacity 0.2s;
}
#<?php echo $instance_id; ?> .sd-btn-inschrijven:hover {
    opacity: 0.9;
}
#<?php echo $instance_id; ?> .sd-btn-show-all {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 14px 32px;
    border-radius: 9999px;
    font-size: 14px;
    font-weight: 700;
    color: #fff;
    background-color: var(--color-primary, #417AB3);
    border: none;
    cursor: pointer;
    transition: opacity 0.2s;
}
#<?php echo $instance_id; ?> .sd-btn-show-all:hover {
    opacity: 0.9;
}
#<?php echo $instance_id; ?> .sd-select {
    border: 1px solid var(--color-primary, #417AB3);
    border-radius: 8px;
    padding: 10px 40px 10px 16px;
    font-size: 14px;
    background: #fff;
    min-width: 200px;
    cursor: pointer;
    appearance: none;
    -webkit-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='none' viewBox='0 0 24 24' stroke='%23417AB3' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19.5 8.25l-7.5 7.5-7.5-7.5'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
}
#<?php echo $instance_id; ?> .sd-toggle {
    position: relative;
    display: inline-flex;
    align-items: center;
    gap: 12px;
    cursor: pointer;
    user-select: none;
    font-size: 14px;
    color: #1f2937;
}
#<?php echo $instance_id; ?> .sd-toggle input {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}
#<?php echo $instance_id; ?> .sd-toggle-track {
    width: 44px;
    height: 26px;
    background: #d1d5db;
    border-radius: 13px;
    position: relative;
    transition: background 0.2s;
    flex-shrink: 0;
}
#<?php echo $instance_id; ?> .sd-toggle input:checked + .sd-toggle-track {
    background: #22c55e;
}
#<?php echo $instance_id; ?> .sd-toggle-knob {
    width: 20px;
    height: 20px;
    background: #fff;
    border-radius: 50%;
    position: absolute;
    top: 3px;
    left: 3px;
    transition: transform 0.2s;
    box-shadow: 0 1px 3px rgba(0,0,0,0.15);
}
#<?php echo $instance_id; ?> .sd-toggle input:checked + .sd-toggle-track .sd-toggle-knob {
    transform: translateX(18px);
}
#<?php echo $instance_id; ?> .sd-year-sep {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 24px;
    margin-top: 8px;
}
#<?php echo $instance_id; ?> .sd-year-label {
    font-size: 14px;
    font-weight: 700;
    color: #6b7280;
    white-space: nowrap;
}
#<?php echo $instance_id; ?> .sd-year-line {
    flex-grow: 1;
    height: 1px;
    background: #d1d5db;
}
</style>

<div class="tailwind-scope tw container mx-auto px-4" id="<?php echo $instance_id; ?>">
    <div style="padding: 40px 0;">

        <!-- Header -->
        <div class="sd-header" style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 32px;">
            <h2 style="font-size: 2.25rem; font-weight: 800; color: var(--color-primary, #417AB3); margin: 0; line-height: 1.2;">
                Startdata
            </h2>

            <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 24px;">
                <select id="<?php echo $instance_id; ?>-location-filter" class="sd-select" aria-label="Filter op locatie">
                    <option value="">Alle locaties</option>
                    <?php foreach ($locations as $loc): ?>
                        <option value="<?php echo esc_attr($loc); ?>"><?php echo esc_html($loc); ?></option>
                    <?php endforeach; ?>
                </select>

                <label class="sd-toggle">
                    <span>Toon enkel beschikbaar</span>
                    <input type="checkbox" id="<?php echo $instance_id; ?>-avail-toggle" checked aria-label="Toon alleen beschikbare trainingen">
                    <span class="sd-toggle-track">
                        <span class="sd-toggle-knob"></span>
                    </span>
                </label>
            </div>
        </div>

        <!-- Cards per jaar -->
        <?php $card_index = 0; ?>
        <?php foreach ($grouped as $year => $year_items): ?>

            <div class="sd-year-sep startdata-year-sep">
                <span class="sd-year-label"><?php echo esc_html($year); ?></span>
                <span class="sd-year-line"></span>
            </div>

            <div class="sd-grid startdata-year-grid">
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
                    $hidden_class = $card_index >= $initial_visible ? 'startdata-extra' : '';
                    $card_index++;
                ?>

                <div class="sd-card startdata-card <?php echo $hidden_class; ?>"
                     <?php if ($hidden_class): ?>style="display:none;"<?php endif; ?>
                     data-location="<?php echo esc_attr($location); ?>"
                     data-available="<?php echo $is_available ? '1' : '0'; ?>">

                    <div class="sd-date"><?php echo esc_html($start_display); ?></div>

                    <div class="sd-meta">
                        <div>
                            <div class="sd-meta-label">Locatie</div>
                            <div class="sd-meta-value"><?php echo esc_html($location); ?></div>
                        </div>
                        <div>
                            <div class="sd-meta-label">Beschikbaarheid</div>
                            <div class="sd-meta-value sd-meta-value-avail">
                                <span class="sd-avail-dot" style="background: <?php echo $is_available ? '#22c55e' : '#ef4444'; ?>;"></span><?php echo esc_html($avail_text); ?>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($lessons)): ?>
                    <div style="margin-bottom: 24px;">
                        <button class="sd-accordion-btn startdata-accordion-btn" data-target="<?php echo $accordion_id; ?>" type="button" aria-expanded="false" aria-controls="<?php echo $accordion_id; ?>">
                            <span>Planning modules</span>
                            <svg class="sd-accordion-icon startdata-accordion-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                            </svg>
                        </button>
                        <div id="<?php echo $accordion_id; ?>" style="display: none; margin-top: 8px;">
                            <?php foreach ($lessons as $lesson): ?>
                                <div style="background: #f9fafb; border-radius: 8px; padding: 12px 16px; font-size: 14px; margin-bottom: 8px;">
                                    <div style="font-weight: 600; color: #1f2937; margin-bottom: 4px;">
                                        <?php echo esc_html($lesson['lesson'] ?? ''); ?>
                                    </div>
                                    <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 12px; font-size: 12px; color: #6b7280;">
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

                    <div style="margin-top: auto;">
                        <a href="<?php echo esc_url($link); ?>" class="sd-btn-inschrijven">
                            Inschrijven
                            <svg style="width: 16px; height: 16px; margin-left: 8px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                            </svg>
                        </a>
                    </div>
                </div>

                <?php endforeach; ?>
            </div>

        <?php endforeach; ?>

        <?php if ($card_index > $initial_visible): ?>
        <div style="text-align: center; margin-top: 16px;" id="<?php echo $instance_id; ?>-show-all-wrap">
            <button id="<?php echo $instance_id; ?>-show-all" class="sd-btn-show-all" type="button">
                Toon alle data
                <svg style="width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
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
        var root = document.getElementById("<?php echo $instance_id; ?>");
        if (!root) return;

        var locationFilter = root.querySelector("#<?php echo $instance_id; ?>-location-filter");
        var availToggle = root.querySelector("#<?php echo $instance_id; ?>-avail-toggle");
        var showAllBtn = root.querySelector("#<?php echo $instance_id; ?>-show-all");
        var showAllWrap = root.querySelector("#<?php echo $instance_id; ?>-show-all-wrap");
        var cards = root.querySelectorAll(".startdata-card");
        var allRevealed = false;

        function applyFilters() {
            var locVal = locationFilter ? locationFilter.value : "";
            var availOnly = availToggle ? availToggle.checked : false;

            cards.forEach(function(card) {
                var matchLoc = !locVal || card.dataset.location === locVal;
                var matchAvail = !availOnly || card.dataset.available === "1";
                var isExtra = card.classList.contains("startdata-extra") && !allRevealed;

                card.style.display = (matchLoc && matchAvail && !isExtra) ? "" : "none";
            });

            root.querySelectorAll(".startdata-year-grid").forEach(function(grid) {
                var sep = grid.previousElementSibling;
                var hasVisible = Array.from(grid.querySelectorAll(".startdata-card")).some(function(c) {
                    return c.style.display !== "none";
                });
                if (sep && sep.classList.contains("startdata-year-sep")) {
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
                cards.forEach(function(card) {
                    card.classList.remove("startdata-extra");
                });
                if (showAllWrap) showAllWrap.style.display = "none";
                applyFilters();
            });
        }

        root.querySelectorAll(".startdata-accordion-btn").forEach(function(btn) {
            btn.addEventListener("click", function() {
                var panel = document.getElementById(this.dataset.target);
                var icon = this.querySelector(".startdata-accordion-icon");
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
