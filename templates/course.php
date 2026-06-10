<?php
// Prevent direct access
defined('ABSPATH') or die('No script kiddies please!');

$instance_id = 'mentor-courses-' . wp_unique_id();
$course_items = $courses['results'] ?? [];
$detail_page_id = (int) get_option('mentor_detail_page_id', 0);
$detail_page_url = $detail_page_id ? get_permalink($detail_page_id) : '';

// Verzamel unieke thema's (subjects)
$subjects = [];
foreach ($course_items as $course) {
    $subj = $course['subject']['title'] ?? '';
    if (!empty($subj) && !in_array($subj, $subjects)) {
        $subjects[] = $subj;
    }
}
sort($subjects);
?>

<style>
#<?php echo esc_attr($instance_id); ?> .mc-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}
@media (max-width: 1024px) {
    #<?php echo esc_attr($instance_id); ?> .mc-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
@media (max-width: 640px) {
    #<?php echo esc_attr($instance_id); ?> .mc-grid {
        grid-template-columns: 1fr;
    }
    #<?php echo esc_attr($instance_id); ?> .mc-header {
        flex-direction: column !important;
        align-items: flex-start !important;
    }
}
#<?php echo esc_attr($instance_id); ?> .mc-card {
    background: #fff;
    border-radius: 16px;
    border: 1px solid #e5e7eb;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    position: relative;
}
#<?php echo esc_attr($instance_id); ?> .mc-card-img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    display: block;
}
#<?php echo esc_attr($instance_id); ?> .mc-card-body {
    padding: 24px;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}
#<?php echo esc_attr($instance_id); ?> .mc-card-title {
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--color-body-text, #1f2937);
    margin: 0 0 8px 0;
    line-height: 1.3;
}
#<?php echo esc_attr($instance_id); ?> .mc-card-subject {
    font-size: 12px;
    font-weight: 600;
    color: var(--color-primary, #417AB3);
    margin: 0 0 12px 0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
#<?php echo esc_attr($instance_id); ?> .mc-card-desc {
    font-size: 14px;
    color: #6b7280;
    line-height: 1.6;
    margin: 0 0 20px 0;
    flex-grow: 1;
}
#<?php echo esc_attr($instance_id); ?> .mc-card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: auto;
    padding-top: 16px;
    border-top: 1px solid #f3f4f6;
}
#<?php echo esc_attr($instance_id); ?> .mc-price {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--color-body-text, #1f2937);
}
#<?php echo esc_attr($instance_id); ?> .mc-btn {
    display: inline-flex;
    align-items: center;
    padding: 10px 24px;
    border-radius: 9999px;
    font-size: 14px;
    font-weight: 700;
    color: #fff;
    text-decoration: none;
    background-color: var(--color-primary, #417AB3);
    transition: opacity 0.2s;
    white-space: nowrap;
}
@media (hover: hover) and (pointer: fine) {
    #<?php echo esc_attr($instance_id); ?> .mc-btn:hover {
        opacity: 0.9;
    }
}
#<?php echo esc_attr($instance_id); ?> .mc-btn:active {
    opacity: 0.85;
    transform: scale(0.98);
}
#<?php echo esc_attr($instance_id); ?> .mc-btn svg {
    width: 16px;
    height: 16px;
    margin-left: 8px;3;;
    
}
#<?php echo esc_attr($instance_id); ?> .mc-select {
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
#<?php echo esc_attr($instance_id); ?> .mc-search {
    border: 1px solid #d1d5db;
    border-radius: 8px;
    padding: 10px 16px 10px 40px;
    font-size: 14px;
    background: #fff;
    min-width: 220px;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='none' viewBox='0 0 24 24' stroke='%239ca3af' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: left 12px center;
}
#<?php echo esc_attr($instance_id); ?> .mc-search:focus {
    outline: none;
    border-color: var(--color-primary, #417AB3);
    box-shadow: 0 0 0 2px rgba(65, 122, 179, 0.15);
}
#<?php echo esc_attr($instance_id); ?> .mc-no-results {
    grid-column: 1 / -1;
    text-align: center;
    padding: 40px 20px;
    color: #9ca3af;
    font-size: 15px;
}
#<?php echo esc_attr($instance_id); ?> .mc-review-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: #6b7280;
    margin-bottom: 12px;
}
#<?php echo esc_attr($instance_id); ?> .mc-review-badge strong {
    color: var(--color-body-text, #1f2937);
    font-weight: 700;
}

/* Mobile: full-width controls, smaller heading, tighter padding */
@media (max-width: 640px) {
    #<?php echo esc_attr($instance_id); ?> > div {
        padding: 24px 0;
    }
    #<?php echo esc_attr($instance_id); ?> .mc-header h2 {
        font-size: 1.5rem;
    }
    #<?php echo esc_attr($instance_id); ?> .mc-header > div {
        width: 100%;
    }
    #<?php echo esc_attr($instance_id); ?> .mc-search,
    #<?php echo esc_attr($instance_id); ?> .mc-select {
        width: 100%;
        min-width: 0;
        box-sizing: border-box;
    }
}

/* Touch devices: make the whole card a tap target (stretched link) */
@media (hover: none) {
    #<?php echo esc_attr($instance_id); ?> .mc-card .mc-btn::after {
        content: "";
        position: absolute;
        inset: 0;
        z-index: 1;
    }
}
</style>

<div class="tailwind-scope tw container mx-auto px-4" id="<?php echo esc_attr($instance_id); ?>">
    <div style="padding: 40px 0;">

        <!-- Header + Filters -->
        <div class="mc-header" style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 32px;">
            <h2 style="font-size: 2.25rem; font-weight: 800; color: var(--color-primary, #417AB3); margin: 0; line-height: 1.2;">
                Trainingen
            </h2>

            <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 16px;">
                <!-- Zoeken -->
                <input type="text"
                       id="<?php echo esc_attr($instance_id); ?>-search"
                       class="mc-search"
                       placeholder="Zoek een training..."
                       aria-label="Zoek een training">

                <!-- Thema filter -->
                <?php if (count($subjects) > 1): ?>
                <select id="<?php echo esc_attr($instance_id); ?>-subject-filter" class="mc-select" aria-label="Filter op thema">
                    <option value="">Alle thema's</option>
                    <?php foreach ($subjects as $subj): ?>
                        <option value="<?php echo esc_attr($subj); ?>"><?php echo esc_html($subj); ?></option>
                    <?php endforeach; ?>
                </select>
                <?php endif; ?>
            </div>
        </div>

        <!-- Cards -->
        <div class="mc-grid" id="<?php echo esc_attr($instance_id); ?>-grid">
            <?php foreach ($course_items as $course): ?>
                <div class="mc-card"
                     data-subject="<?php echo esc_attr($course['subject']['title'] ?? ''); ?>"
                     data-title="<?php echo esc_attr(strtolower($course['title'] ?? '')); ?>">

                    <?php if (!empty($course['image_card_medium'])) : ?>
                        <img class="mc-card-img"
                             src="<?php echo esc_url($course['image_card_medium']); ?>"
                             alt="<?php echo esc_attr($course['title']); ?>">
                    <?php endif; ?>

                    <div class="mc-card-body">
                        <?php if (!empty($course['subject']['title'])): ?>
                            <div class="mc-card-subject"><?php echo esc_html($course['subject']['title']); ?></div>
                        <?php endif; ?>

                        <h3 class="mc-card-title"><?php echo esc_html($course['title']); ?></h3>

                        <div class="mc-card-desc">
                            <?php echo wp_kses_post($course['description_truncated']); ?>
                        </div>

                        <?php
                            $cid = $course['id'] ?? 0;
                            if (!empty($review_stats[$cid])):
                                $rs = $review_stats[$cid];
                        ?>
                        <div class="mc-review-badge">
                            <?php echo wp_kses_post(mentor_render_stars($rs['average'], 14)); ?>
                            <strong><?php echo esc_html(number_format($rs['average'], 1, ',', '')); ?></strong>
                            <span>(<?php echo esc_html($rs['count']); ?>)</span>
                        </div>
                        <?php endif; ?>

                        <div class="mc-card-footer">
                            <?php if ($price = mentor_get_course_price($course)) : ?>
                                <span class="mc-price">&euro;<?php echo esc_html($price['display']); ?> <small style="font-size: 0.7em; font-weight: 400; color: #6b7280;"><?php echo esc_html($price['vat_label']); ?></small></span>
                            <?php endif; ?>

                            <?php $course_link = mentor_resolve_course_link($course); if (!empty($course_link)) : ?>
                                <a href="<?php echo esc_url($course_link); ?>" class="mc-btn">
                                    <?php echo esc_html(mentor_get_cta_label()); ?>
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                                    </svg>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    </div>
</div>

<script>
(function() {
    document.addEventListener("DOMContentLoaded", function() {
        var root = document.getElementById("<?php echo esc_attr($instance_id); ?>");
        if (!root) return;

        var searchInput = root.querySelector("#<?php echo esc_attr($instance_id); ?>-search");
        var subjectFilter = root.querySelector("#<?php echo esc_attr($instance_id); ?>-subject-filter");
        var grid = root.querySelector("#<?php echo esc_attr($instance_id); ?>-grid");
        var cards = root.querySelectorAll(".mc-card");

        function applyFilters() {
            var searchVal = searchInput ? searchInput.value.toLowerCase().trim() : "";
            var subjectVal = subjectFilter ? subjectFilter.value : "";
            var visibleCount = 0;

            cards.forEach(function(card) {
                var matchSubject = !subjectVal || card.dataset.subject === subjectVal;
                var matchSearch = !searchVal || card.dataset.title.indexOf(searchVal) !== -1;

                if (matchSubject && matchSearch) {
                    card.style.display = "";
                    visibleCount++;
                } else {
                    card.style.display = "none";
                }
            });

            // No results melding
            var noResults = root.querySelector(".mc-no-results");
            if (visibleCount === 0) {
                if (!noResults) {
                    noResults = document.createElement("div");
                    noResults.className = "mc-no-results";
                    noResults.textContent = "Geen trainingen gevonden.";
                    grid.appendChild(noResults);
                }
                noResults.style.display = "";
            } else if (noResults) {
                noResults.style.display = "none";
            }
        }

        if (searchInput) {
            var debounceTimer;
            searchInput.addEventListener("input", function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(applyFilters, 200);
            });
        }

        if (subjectFilter) subjectFilter.addEventListener("change", applyFilters);
    });
})();
</script>
