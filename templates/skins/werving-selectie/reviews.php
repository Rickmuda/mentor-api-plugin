<?php
/**
 * Skin: werving-selectie - Reviews ("Decisive hiring", executive/beslis-thema)
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

if (!wp_style_is('mentor-werving-selectie-fonts', 'enqueued')) {
    wp_enqueue_style(
        'mentor-werving-selectie-fonts',
        'https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600&display=swap',
        array(),
        null
    );
}

$stars = '#FF4F36';
$instance_id = 'ws-reviews-' . wp_unique_id();
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
    --ws-shadow-sm: 0 6px 18px -10px rgba(20, 24, 31, 0.18);

    background: transparent;
    color: var(--ws-ink);
    font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    -webkit-font-smoothing: antialiased;
}
#<?php echo esc_attr($instance_id); ?> * { box-sizing: border-box; }
#<?php echo esc_attr($instance_id); ?> .ws-wrap { max-width: 100%; margin: 0; padding: 0; }

#<?php echo esc_attr($instance_id); ?> .ws-title {
    font-family: "Space Grotesk", "Segoe UI", sans-serif;
    font-size: clamp(1.9rem, 1.3rem + 2vw, 2.8rem);
    font-weight: 700;
    letter-spacing: -0.02em;
    color: var(--ws-ink);
    margin: 0 0 40px 0;
    line-height: 1.05;
}
#<?php echo esc_attr($instance_id); ?> .ws-title em { font-style: normal; font-weight: 700; color: var(--ws-coral); }

#<?php echo esc_attr($instance_id); ?> .ws-summary {
    display: flex;
    gap: 48px;
    align-items: center;
    padding: 40px;
    background: var(--ws-surface);
    border: 1px solid var(--ws-line);
    border-left: 4px solid var(--ws-coral);
    border-radius: 8px;
    box-shadow: var(--ws-shadow-sm);
    margin-bottom: 36px;
    position: relative;
    overflow: hidden;
}
#<?php echo esc_attr($instance_id); ?> .ws-summary > * { position: relative; }
#<?php echo esc_attr($instance_id); ?> .ws-score {
    text-align: center;
    flex-shrink: 0;
    min-width: 160px;
}
#<?php echo esc_attr($instance_id); ?> .ws-score-num {
    font-family: "Space Grotesk", "Segoe UI", sans-serif;
    font-size: 4rem;
    font-weight: 700;
    line-height: 1;
    color: var(--ws-coral);
    margin-bottom: 12px;
    letter-spacing: -0.03em;
}
#<?php echo esc_attr($instance_id); ?> .ws-score-count {
    font-size: 13px;
    color: var(--ws-body);
    margin-top: 10px;
}
#<?php echo esc_attr($instance_id); ?> .ws-dist {
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    gap: 9px;
}
#<?php echo esc_attr($instance_id); ?> .ws-dist-row {
    display: flex;
    align-items: center;
    gap: 12px;
}
#<?php echo esc_attr($instance_id); ?> .ws-dist-label {
    font-size: 13px;
    font-weight: 600;
    color: var(--ws-body);
    width: 16px;
    text-align: right;
    flex-shrink: 0;
}
#<?php echo esc_attr($instance_id); ?> .ws-dist-bg {
    flex-grow: 1;
    height: 9px;
    background: var(--ws-coral-soft);
    border-radius: 4px;
    overflow: hidden;
}
#<?php echo esc_attr($instance_id); ?> .ws-dist-bar {
    height: 100%;
    background: var(--ws-coral);
    border-radius: 4px;
}
#<?php echo esc_attr($instance_id); ?> .ws-dist-count {
    font-size: 13px;
    color: var(--ws-body);
    width: 28px;
    flex-shrink: 0;
}

#<?php echo esc_attr($instance_id); ?> .ws-cats {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 40px;
}
#<?php echo esc_attr($instance_id); ?> .ws-cat {
    background: var(--ws-surface);
    border: 1px solid var(--ws-line);
    border-radius: 6px;
    padding: 9px 17px;
    display: flex;
    align-items: center;
    gap: 10px;
}
#<?php echo esc_attr($instance_id); ?> .ws-cat-name { font-size: 13px; color: var(--ws-body); }
#<?php echo esc_attr($instance_id); ?> .ws-cat-score {
    font-family: "Space Grotesk", "Segoe UI", sans-serif;
    font-size: 15px;
    font-weight: 700;
    color: var(--ws-coral);
}

#<?php echo esc_attr($instance_id); ?> .ws-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    align-items: start;
    gap: 26px;
    margin-bottom: 32px;
}
@media (max-width: 980px) { #<?php echo esc_attr($instance_id); ?> .ws-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 640px) { #<?php echo esc_attr($instance_id); ?> .ws-grid { grid-template-columns: 1fr; } }

#<?php echo esc_attr($instance_id); ?> .ws-card {
    position: relative;
    background: var(--ws-surface);
    border: 1px solid var(--ws-line);
    border-top: 3px solid var(--ws-coral);
    border-radius: 8px;
    box-shadow: var(--ws-shadow-sm);
    padding: 30px;
    overflow: hidden;
}
#<?php echo esc_attr($instance_id); ?> .ws-quote {
    font-family: "Space Grotesk", "Segoe UI", sans-serif;
    font-size: 4.2rem;
    line-height: 0.6;
    color: var(--ws-coral);
    opacity: 0.18;
    position: absolute;
    top: 26px;
    right: 28px;
    font-weight: 700;
}
/* .ws-quote moet absoluut blijven (zit in de hoek); andere kinderen krijgen
   wel position:relative zodat ze boven eventueel ::before-decoraties stapelen. */
#<?php echo esc_attr($instance_id); ?> .ws-card > *:not(.ws-quote) { position: relative; }
#<?php echo esc_attr($instance_id); ?> .ws-card-stars { margin-bottom: 14px; }
#<?php echo esc_attr($instance_id); ?> .ws-card-title {
    font-family: "Space Grotesk", "Segoe UI", sans-serif;
    font-size: 17px;
    font-weight: 600;
    color: var(--ws-ink);
    margin-bottom: 10px;
    letter-spacing: -0.01em;
}
#<?php echo esc_attr($instance_id); ?> .ws-card-desc {
    font-size: 15px;
    line-height: 1.7;
    color: var(--ws-body);
    margin-bottom: 20px;
}
#<?php echo esc_attr($instance_id); ?> .ws-card-foot {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    border-top: 1px solid var(--ws-line);
    padding-top: 14px;
}
#<?php echo esc_attr($instance_id); ?> .ws-card-name {
    font-size: 14px;
    font-weight: 600;
    color: var(--ws-ink);
}
#<?php echo esc_attr($instance_id); ?> .ws-card-date { font-size: 12px; color: var(--ws-body); }
#<?php echo esc_attr($instance_id); ?> .ws-card-module {
    font-size: 12px;
    font-weight: 600;
    color: var(--ws-coral-dark);
    margin-top: 10px;
}

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
    #<?php echo esc_attr($instance_id); ?> .ws-summary { flex-direction: column; gap: 28px; padding: 32px; }
    #<?php echo esc_attr($instance_id); ?> .ws-score { width: 100%; }
}
</style>

<div id="<?php echo esc_attr($instance_id); ?>">
    <div class="ws-wrap">

        <h2 class="ws-title">Wat deelnemers <em>delen</em></h2>

        <div class="ws-summary">
            <div class="ws-score">
                <div class="ws-score-num"><?php echo esc_html(number_format($avg, 1, ',', '')); ?></div>
                <div><?php echo mentor_render_stars($avg, 20, $stars); ?></div>
                <div class="ws-score-count"><?php echo esc_html($total); ?> review<?php echo esc_html($total !== 1 ? 's' : ''); ?></div>
            </div>
            <div class="ws-dist">
                <?php for ($i = 5; $i >= 1; $i--):
                    $count = $distribution[$i] ?? ($distribution["$i"] ?? 0);
                    $pct = $total > 0 ? ($count / $total) * 100 : 0;
                ?>
                <div class="ws-dist-row">
                    <span class="ws-dist-label"><?php echo esc_html($i); ?></span>
                    <div class="ws-dist-bg"><div class="ws-dist-bar" style="width: <?php echo esc_attr($pct); ?>%"></div></div>
                    <span class="ws-dist-count"><?php echo esc_html($count); ?></span>
                </div>
                <?php endfor; ?>
            </div>
        </div>

        <?php if (!empty($cat_averages)): ?>
        <div class="ws-cats">
            <?php foreach ($cat_averages as $cat):
                $cat_name = $cat['category_name'] ?? $cat['name'] ?? '';
                $cat_avg = $cat['average'] ?? $cat['avg'] ?? 0;
                if (empty($cat_name)) continue;
            ?>
            <div class="ws-cat">
                <span class="ws-cat-name"><?php echo esc_html($cat_name); ?></span>
                <span class="ws-cat-score"><?php echo esc_html(number_format((float) $cat_avg, 1, ',', '')); ?></span>
                <?php echo mentor_render_stars((float) $cat_avg, 13, $stars); ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php $review_index = 0; ?>
        <div class="ws-grid">
            <?php foreach ($reviews as $review):
                $hidden = $review_index >= $initial_visible ? 'ws-extra' : '';
                $review_index++;
            ?>
            <div class="ws-card <?php echo esc_attr($hidden); ?>"
                 <?php if ($hidden): ?>style="display:none;"<?php endif; ?>>
                <span class="ws-quote" aria-hidden="true">&ldquo;</span>
                <div class="ws-card-stars"><?php echo mentor_render_stars($review['overall_rating'] ?? 0, 16, $stars); ?></div>
                <?php if (!empty($review['title'])): ?>
                    <div class="ws-card-title"><?php echo esc_html($review['title']); ?></div>
                <?php endif; ?>
                <?php if (!empty($review['description'])): ?>
                    <div class="ws-card-desc"><?php echo esc_html($review['description']); ?></div>
                <?php endif; ?>
                <div class="ws-card-foot">
                    <span class="ws-card-name"><?php echo esc_html($review['display_name'] ?? 'Anoniem'); ?></span>
                    <span class="ws-card-date"><?php
                        $date = $review['published_at'] ?? $review['created'] ?? '';
                        if ($date) echo esc_html(date_i18n('j F Y', strtotime($date)));
                    ?></span>
                </div>
                <?php if (empty($module_id) && !empty($review['module_title'])): ?>
                    <div class="ws-card-module"><?php echo esc_html($review['module_title']); ?></div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if ($review_index > $initial_visible): ?>
        <div style="text-align: center;" id="<?php echo esc_attr($instance_id); ?>-show-all-wrap">
            <button id="<?php echo esc_attr($instance_id); ?>-show-all" class="ws-show-all" type="button">
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
                root.querySelectorAll(".ws-extra").forEach(function(el) {
                    el.style.display = "";
                    el.classList.remove("ws-extra");
                });
                if (wrap) wrap.style.display = "none";
            });
        }
    });
})();
</script>
<?php endif; ?>
