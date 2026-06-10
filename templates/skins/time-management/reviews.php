<?php
/**
 * Skin: time-management - Reviews ("Calm focus", planner-thema)
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

if (!wp_style_is('mentor-time-management-fonts', 'enqueued')) {
    wp_enqueue_style(
        'mentor-time-management-fonts',
        'https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;600;700&display=swap',
        array(),
        null
    );
}

$stars = '#E8930C';
$instance_id = 'tm-reviews-' . wp_unique_id();
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
    --tm-shadow-sm: 0 4px 12px -4px rgba(26, 31, 54, 0.10);

    background: transparent;
    color: var(--tm-ink);
    font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    -webkit-font-smoothing: antialiased;
}
#<?php echo esc_attr($instance_id); ?> * { box-sizing: border-box; }
#<?php echo esc_attr($instance_id); ?> .tm-wrap { max-width: 100%; margin: 0; padding: 0; }
#<?php echo esc_attr($instance_id); ?> .tm-title {
    font-family: "Outfit", sans-serif;
    font-size: clamp(1.7rem, 1.2rem + 1.6vw, 2.4rem);
    font-weight: 700; letter-spacing: -0.02em; color: var(--tm-ink);
    margin: 0 0 30px 0; line-height: 1.1;
}
#<?php echo esc_attr($instance_id); ?> .tm-title span { color: var(--tm-indigo); }

#<?php echo esc_attr($instance_id); ?> .tm-summary {
    display: flex; gap: 48px; align-items: center;
    padding: 32px;
    background: var(--tm-surface);
    border: 1.5px solid var(--tm-line);
    border-radius: 18px;
    box-shadow: var(--tm-shadow-sm);
    margin-bottom: 28px;
}
#<?php echo esc_attr($instance_id); ?> .tm-score { text-align: center; flex-shrink: 0; min-width: 150px; }
#<?php echo esc_attr($instance_id); ?> .tm-score-num {
    font-family: "Outfit", sans-serif;
    font-size: 3.4rem; font-weight: 700; line-height: 1;
    color: var(--tm-indigo); margin-bottom: 10px;
    letter-spacing: -0.02em;
}
#<?php echo esc_attr($instance_id); ?> .tm-score-count {
    font-family: "JetBrains Mono", ui-monospace, SFMono-Regular, monospace;
    font-size: 12px; color: var(--tm-body); margin-top: 10px;
}

#<?php echo esc_attr($instance_id); ?> .tm-dist { flex-grow: 1; display: flex; flex-direction: column; gap: 8px; }
#<?php echo esc_attr($instance_id); ?> .tm-dist-row { display: flex; align-items: center; gap: 12px; }
#<?php echo esc_attr($instance_id); ?> .tm-dist-label {
    font-family: "JetBrains Mono", monospace;
    font-size: 12px; font-weight: 600; color: var(--tm-body);
    width: 16px; text-align: right; flex-shrink: 0;
}
#<?php echo esc_attr($instance_id); ?> .tm-dist-bg {
    flex-grow: 1; height: 7px; background: var(--tm-indigo-soft);
    border-radius: 4px; overflow: hidden;
}
#<?php echo esc_attr($instance_id); ?> .tm-dist-bar { height: 100%; background: var(--tm-indigo); border-radius: 4px; }
#<?php echo esc_attr($instance_id); ?> .tm-dist-count {
    font-family: "JetBrains Mono", monospace;
    font-size: 12px; color: var(--tm-body);
    width: 28px; flex-shrink: 0;
}

#<?php echo esc_attr($instance_id); ?> .tm-cats {
    display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 32px;
}
#<?php echo esc_attr($instance_id); ?> .tm-cat {
    background: var(--tm-line-soft);
    border-radius: 8px;
    padding: 9px 14px;
    display: flex; align-items: center; gap: 10px;
}
#<?php echo esc_attr($instance_id); ?> .tm-cat-name { font-size: 13px; color: var(--tm-body); }
#<?php echo esc_attr($instance_id); ?> .tm-cat-score {
    font-family: "Outfit", sans-serif;
    font-size: 15px; font-weight: 700; color: var(--tm-indigo);
}

#<?php echo esc_attr($instance_id); ?> .tm-grid {
    display: grid; grid-template-columns: repeat(3, 1fr);
    align-items: start; gap: 20px; margin-bottom: 24px;
}
@media (max-width: 980px) { #<?php echo esc_attr($instance_id); ?> .tm-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 640px) { #<?php echo esc_attr($instance_id); ?> .tm-grid { grid-template-columns: 1fr; } }

#<?php echo esc_attr($instance_id); ?> .tm-card {
    background: var(--tm-surface);
    border: 1.5px solid var(--tm-line);
    border-radius: 18px;
    box-shadow: var(--tm-shadow-sm);
    padding: 26px;
    position: relative;
}
#<?php echo esc_attr($instance_id); ?> .tm-card-stars { margin-bottom: 12px; }
#<?php echo esc_attr($instance_id); ?> .tm-card-title {
    font-family: "Outfit", sans-serif;
    font-size: 16px; font-weight: 700; color: var(--tm-ink);
    margin: 0 0 12px 0; letter-spacing: -0.01em;
}
#<?php echo esc_attr($instance_id); ?> .tm-card-desc {
    font-size: 15px; line-height: 1.7; color: var(--tm-body); margin-bottom: 18px;
}
#<?php echo esc_attr($instance_id); ?> .tm-card-foot {
    display: flex; justify-content: space-between; align-items: baseline;
    border-top: 1px solid var(--tm-line); padding-top: 14px;
}
#<?php echo esc_attr($instance_id); ?> .tm-card-name { font-size: 14px; font-weight: 700; color: var(--tm-ink); }
#<?php echo esc_attr($instance_id); ?> .tm-card-date {
    font-family: "JetBrains Mono", monospace;
    font-size: 11px; color: var(--tm-muted); letter-spacing: 0.02em;
}
#<?php echo esc_attr($instance_id); ?> .tm-card-module {
    font-family: "JetBrains Mono", monospace;
    font-size: 11px; font-weight: 600; color: var(--tm-indigo);
    margin-top: 10px; letter-spacing: 0.02em;
}

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
    #<?php echo esc_attr($instance_id); ?> .tm-summary { flex-direction: column; gap: 24px; padding: 24px; }
    #<?php echo esc_attr($instance_id); ?> .tm-score { width: 100%; }
}
</style>

<div id="<?php echo esc_attr($instance_id); ?>">
    <div class="tm-wrap">

        <h2 class="tm-title">Wat deelnemers <span>zeggen</span></h2>

        <div class="tm-summary">
            <div class="tm-score">
                <div class="tm-score-num"><?php echo esc_html(number_format($avg, 1, ',', '')); ?></div>
                <div><?php echo mentor_render_stars($avg, 20, $stars); ?></div>
                <div class="tm-score-count"><?php echo esc_html($total); ?> review<?php echo esc_html($total !== 1 ? 's' : ''); ?></div>
            </div>
            <div class="tm-dist">
                <?php for ($i = 5; $i >= 1; $i--):
                    $count = $distribution[$i] ?? ($distribution["$i"] ?? 0);
                    $pct = $total > 0 ? ($count / $total) * 100 : 0;
                ?>
                <div class="tm-dist-row">
                    <span class="tm-dist-label"><?php echo esc_html($i); ?></span>
                    <div class="tm-dist-bg"><div class="tm-dist-bar" style="width: <?php echo esc_attr($pct); ?>%"></div></div>
                    <span class="tm-dist-count"><?php echo esc_html($count); ?></span>
                </div>
                <?php endfor; ?>
            </div>
        </div>

        <?php if (!empty($cat_averages)): ?>
        <div class="tm-cats">
            <?php foreach ($cat_averages as $cat):
                $cat_name = $cat['category_name'] ?? $cat['name'] ?? '';
                $cat_avg = $cat['average'] ?? $cat['avg'] ?? 0;
                if (empty($cat_name)) continue;
            ?>
            <div class="tm-cat">
                <span class="tm-cat-name"><?php echo esc_html($cat_name); ?></span>
                <span class="tm-cat-score"><?php echo esc_html(number_format((float) $cat_avg, 1, ',', '')); ?></span>
                <?php echo mentor_render_stars((float) $cat_avg, 13, $stars); ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php $review_index = 0; ?>
        <div class="tm-grid">
            <?php foreach ($reviews as $review):
                $hidden = $review_index >= $initial_visible ? 'tm-extra' : '';
                $review_index++;
            ?>
            <div class="tm-card <?php echo esc_attr($hidden); ?>"
                 <?php if ($hidden): ?>style="display:none;"<?php endif; ?>>
                <div class="tm-card-stars"><?php echo mentor_render_stars($review['overall_rating'] ?? 0, 16, $stars); ?></div>
                <?php if (!empty($review['title'])): ?>
                    <div class="tm-card-title"><?php echo esc_html($review['title']); ?></div>
                <?php endif; ?>
                <?php if (!empty($review['description'])): ?>
                    <div class="tm-card-desc"><?php echo esc_html($review['description']); ?></div>
                <?php endif; ?>
                <div class="tm-card-foot">
                    <span class="tm-card-name"><?php echo esc_html($review['display_name'] ?? 'Anoniem'); ?></span>
                    <span class="tm-card-date"><?php
                        $date = $review['published_at'] ?? $review['created'] ?? '';
                        if ($date) echo esc_html(date_i18n('j M Y', strtotime($date)));
                    ?></span>
                </div>
                <?php if (empty($module_id) && !empty($review['module_title'])): ?>
                    <div class="tm-card-module">// <?php echo esc_html($review['module_title']); ?></div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if ($review_index > $initial_visible): ?>
        <div style="text-align: center;" id="<?php echo esc_attr($instance_id); ?>-show-all-wrap">
            <button id="<?php echo esc_attr($instance_id); ?>-show-all" class="tm-show-all" type="button">
                Toon alle reviews
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
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
                root.querySelectorAll(".tm-extra").forEach(function(el) {
                    el.style.display = "";
                    el.classList.remove("tm-extra");
                });
                if (wrap) wrap.style.display = "none";
            });
        }
    });
})();
</script>
<?php endif; ?>
