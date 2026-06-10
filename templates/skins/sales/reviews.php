<?php
/**
 * Skin: sales - Reviews ("Sales-cockpit", data-dashboard-thema)
 * Verwacht dezelfde variabelen als templates/reviews.php: $reviews, $aggregate, $module_id
 */
defined('ABSPATH') or die('No script kiddies please!');

$avg = $aggregate['average_rating'] ?? 0;
$total = $aggregate['total_reviews'] ?? 0;
$distribution = $aggregate['rating_distribution'] ?? [];
$cat_averages = $aggregate['category_averages'] ?? [];
$initial_visible = 6;

if ($total < 1) {
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

$stars = '#19C37D';
$instance_id = 'sc-reviews-' . wp_unique_id();
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
    --sc-shadow-sm: 0 4px 14px -6px rgba(14, 22, 38, 0.18);

    background: transparent;
    color: var(--sc-ink);
    font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    -webkit-font-smoothing: antialiased;
}
#<?php echo esc_attr($instance_id); ?> * { box-sizing: border-box; }
#<?php echo esc_attr($instance_id); ?> .sc-wrap { max-width: 100%; margin: 0; padding: 0; }

#<?php echo esc_attr($instance_id); ?> .sc-title {
    font-family: "Sora", -apple-system, sans-serif;
    font-size: clamp(1.9rem, 1.3rem + 2vw, 2.8rem);
    font-weight: 800;
    letter-spacing: -0.025em;
    color: var(--sc-ink);
    margin: 0 0 40px 0;
    line-height: 1.05;
}
#<?php echo esc_attr($instance_id); ?> .sc-title em { font-style: normal; font-weight: 700; color: var(--sc-emerald-dk); }

/* Charcoal-navy summary band with emerald highlights */
#<?php echo esc_attr($instance_id); ?> .sc-summary {
    display: flex;
    gap: 48px;
    align-items: center;
    padding: 40px;
    background: var(--sc-ink);
    border: 1px solid var(--sc-ink);
    border-top: 3px solid var(--sc-emerald);
    border-radius: 16px;
    box-shadow: var(--sc-shadow-sm);
    margin-bottom: 36px;
    position: relative;
    overflow: hidden;
}
#<?php echo esc_attr($instance_id); ?> .sc-summary > * { position: relative; }
#<?php echo esc_attr($instance_id); ?> .sc-score {
    text-align: center;
    flex-shrink: 0;
    min-width: 160px;
}
#<?php echo esc_attr($instance_id); ?> .sc-score-num {
    font-family: "IBM Plex Mono", ui-monospace, monospace;
    font-size: 4rem;
    font-weight: 600;
    line-height: 1;
    color: var(--sc-emerald);
    margin-bottom: 12px;
    letter-spacing: -0.03em;
}
#<?php echo esc_attr($instance_id); ?> .sc-score-count {
    font-family: "IBM Plex Mono", ui-monospace, monospace;
    font-size: 13px;
    color: #AEB8C4;
    margin-top: 10px;
}
#<?php echo esc_attr($instance_id); ?> .sc-dist {
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    gap: 9px;
}
#<?php echo esc_attr($instance_id); ?> .sc-dist-row {
    display: flex;
    align-items: center;
    gap: 12px;
}
#<?php echo esc_attr($instance_id); ?> .sc-dist-label {
    font-family: "IBM Plex Mono", ui-monospace, monospace;
    font-size: 13px;
    font-weight: 600;
    color: #AEB8C4;
    width: 16px;
    text-align: right;
    flex-shrink: 0;
}
#<?php echo esc_attr($instance_id); ?> .sc-dist-bg {
    flex-grow: 1;
    height: 9px;
    background: rgba(255, 255, 255, 0.10);
    border-radius: 9999px;
    overflow: hidden;
}
#<?php echo esc_attr($instance_id); ?> .sc-dist-bar {
    height: 100%;
    background: linear-gradient(90deg, var(--sc-emerald-dk) 0%, var(--sc-emerald) 100%);
    border-radius: 9999px;
}
#<?php echo esc_attr($instance_id); ?> .sc-dist-count {
    font-family: "IBM Plex Mono", ui-monospace, monospace;
    font-size: 13px;
    color: #AEB8C4;
    width: 28px;
    flex-shrink: 0;
}

#<?php echo esc_attr($instance_id); ?> .sc-cats {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 40px;
}
#<?php echo esc_attr($instance_id); ?> .sc-cat {
    background: var(--sc-surface);
    border: 1px solid var(--sc-line);
    border-radius: 12px;
    padding: 9px 17px;
    display: flex;
    align-items: center;
    gap: 10px;
}
#<?php echo esc_attr($instance_id); ?> .sc-cat-name { font-size: 13px; color: var(--sc-body); }
#<?php echo esc_attr($instance_id); ?> .sc-cat-score {
    font-family: "IBM Plex Mono", ui-monospace, monospace;
    font-size: 15px;
    font-weight: 600;
    color: var(--sc-emerald-dk);
}

#<?php echo esc_attr($instance_id); ?> .sc-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    align-items: start;
    gap: 26px;
    margin-bottom: 32px;
}
@media (max-width: 980px) { #<?php echo esc_attr($instance_id); ?> .sc-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 640px) { #<?php echo esc_attr($instance_id); ?> .sc-grid { grid-template-columns: 1fr; } }

#<?php echo esc_attr($instance_id); ?> .sc-card {
    position: relative;
    background: var(--sc-surface);
    border: 1px solid var(--sc-line);
    border-top: 3px solid var(--sc-emerald);
    border-radius: 14px;
    box-shadow: var(--sc-shadow-sm);
    padding: 30px;
    overflow: hidden;
}
#<?php echo esc_attr($instance_id); ?> .sc-quote {
    font-family: "Sora", -apple-system, sans-serif;
    font-size: 4.2rem;
    line-height: 0.6;
    color: var(--sc-emerald);
    opacity: 0.28;
    position: absolute;
    top: 26px;
    right: 28px;
    font-style: normal;
    font-weight: 800;
}
/* .sc-quote moet absoluut blijven (zit in de hoek); andere kinderen krijgen
   wel position:relative zodat ze boven eventueel ::before-decoraties stapelen. */
#<?php echo esc_attr($instance_id); ?> .sc-card > *:not(.sc-quote) { position: relative; }
#<?php echo esc_attr($instance_id); ?> .sc-card-stars { margin-bottom: 14px; }
#<?php echo esc_attr($instance_id); ?> .sc-card-title {
    font-family: "Sora", -apple-system, sans-serif;
    font-size: 17px;
    font-weight: 700;
    color: var(--sc-ink);
    margin-bottom: 10px;
    letter-spacing: -0.01em;
}
#<?php echo esc_attr($instance_id); ?> .sc-card-desc {
    font-size: 15px;
    line-height: 1.7;
    color: var(--sc-body);
    margin-bottom: 20px;
}
#<?php echo esc_attr($instance_id); ?> .sc-card-foot {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    border-top: 1px solid var(--sc-line);
    padding-top: 14px;
}
#<?php echo esc_attr($instance_id); ?> .sc-card-name {
    font-size: 14px;
    font-weight: 700;
    color: var(--sc-ink);
}
#<?php echo esc_attr($instance_id); ?> .sc-card-date {
    font-family: "IBM Plex Mono", ui-monospace, monospace;
    font-size: 12px;
    color: var(--sc-body);
}
#<?php echo esc_attr($instance_id); ?> .sc-card-module {
    font-family: "IBM Plex Mono", ui-monospace, monospace;
    font-size: 12px;
    font-weight: 600;
    color: var(--sc-emerald-dk);
    margin-top: 10px;
}

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
    #<?php echo esc_attr($instance_id); ?> .sc-summary { flex-direction: column; gap: 28px; padding: 32px; }
    #<?php echo esc_attr($instance_id); ?> .sc-score { width: 100%; }
}
</style>

<div id="<?php echo esc_attr($instance_id); ?>">
    <div class="sc-wrap">

        <h2 class="sc-title">Wat deelnemers <em>delen</em></h2>

        <div class="sc-summary">
            <div class="sc-score">
                <div class="sc-score-num"><?php echo esc_html(number_format($avg, 1, ',', '')); ?></div>
                <div><?php echo mentor_render_stars($avg, 20, $stars); ?></div>
                <div class="sc-score-count"><?php echo esc_html($total); ?> review<?php echo esc_html($total !== 1 ? 's' : ''); ?></div>
            </div>
            <div class="sc-dist">
                <?php for ($i = 5; $i >= 1; $i--):
                    $count = $distribution[$i] ?? ($distribution["$i"] ?? 0);
                    $pct = $total > 0 ? ($count / $total) * 100 : 0;
                ?>
                <div class="sc-dist-row">
                    <span class="sc-dist-label"><?php echo esc_html($i); ?></span>
                    <div class="sc-dist-bg"><div class="sc-dist-bar" style="width: <?php echo esc_attr($pct); ?>%"></div></div>
                    <span class="sc-dist-count"><?php echo esc_html($count); ?></span>
                </div>
                <?php endfor; ?>
            </div>
        </div>

        <?php if (!empty($cat_averages)): ?>
        <div class="sc-cats">
            <?php foreach ($cat_averages as $cat):
                $cat_name = $cat['category_name'] ?? $cat['name'] ?? '';
                $cat_avg = $cat['average'] ?? $cat['avg'] ?? 0;
                if (empty($cat_name)) continue;
            ?>
            <div class="sc-cat">
                <span class="sc-cat-name"><?php echo esc_html($cat_name); ?></span>
                <span class="sc-cat-score"><?php echo esc_html(number_format((float) $cat_avg, 1, ',', '')); ?></span>
                <?php echo mentor_render_stars((float) $cat_avg, 13, $stars); ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php $review_index = 0; ?>
        <div class="sc-grid">
            <?php foreach ($reviews as $review):
                $hidden = $review_index >= $initial_visible ? 'sc-extra' : '';
                $review_index++;
            ?>
            <div class="sc-card <?php echo esc_attr($hidden); ?>"
                 <?php if ($hidden): ?>style="display:none;"<?php endif; ?>>
                <span class="sc-quote" aria-hidden="true">&ldquo;</span>
                <div class="sc-card-stars"><?php echo mentor_render_stars($review['overall_rating'] ?? 0, 16, $stars); ?></div>
                <?php if (!empty($review['title'])): ?>
                    <div class="sc-card-title"><?php echo esc_html($review['title']); ?></div>
                <?php endif; ?>
                <?php if (!empty($review['description'])): ?>
                    <div class="sc-card-desc"><?php echo esc_html($review['description']); ?></div>
                <?php endif; ?>
                <div class="sc-card-foot">
                    <span class="sc-card-name"><?php echo esc_html($review['display_name'] ?? 'Anoniem'); ?></span>
                    <span class="sc-card-date"><?php
                        $date = $review['published_at'] ?? $review['created'] ?? '';
                        if ($date) echo esc_html(date_i18n('j F Y', strtotime($date)));
                    ?></span>
                </div>
                <?php if (empty($module_id) && !empty($review['module_title'])): ?>
                    <div class="sc-card-module"><?php echo esc_html($review['module_title']); ?></div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if ($review_index > $initial_visible): ?>
        <div style="text-align: center;" id="<?php echo esc_attr($instance_id); ?>-show-all-wrap">
            <button id="<?php echo esc_attr($instance_id); ?>-show-all" class="sc-show-all" type="button">
                Toon alle reviews
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                </svg>
            </button>
        </div>
        <?php endif; ?>

    </div>
</div>

<?php if ($review_index > $initial_visible): ?>
<script>
(function() {
    document.addEventListener("DOMContentLoaded", function() {
        var root = document.getElementById("<?php echo esc_attr($instance_id); ?>");
        if (!root) return;
        var btn = root.querySelector("#<?php echo esc_attr($instance_id); ?>-show-all");
        var wrap = root.querySelector("#<?php echo esc_attr($instance_id); ?>-show-all-wrap");
        if (btn) {
            btn.addEventListener("click", function() {
                root.querySelectorAll(".sc-extra").forEach(function(el) {
                    el.style.display = "";
                    el.classList.remove("sc-extra");
                });
                if (wrap) wrap.style.display = "none";
            });
        }
    });
})();
</script>
<?php endif; ?>
