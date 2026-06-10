<?php
/**
 * Skin: feedback - Reviews ("Organisch & groei", feedback-thema)
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

if (!wp_style_is('mentor-feedback-fonts', 'enqueued')) {
    wp_enqueue_style(
        'mentor-feedback-fonts',
        'https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700;9..144,800&family=Inter:wght@400;500;600;700&display=swap',
        array(),
        null
    );
}

$stars = '#4e7847';
$instance_id = 'fb-reviews-' . wp_unique_id();
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
    --fb-shadow-sm: 0 4px 14px -6px rgba(78, 120, 71, 0.18);

    background: transparent;
    color: var(--fb-ink);
    font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    -webkit-font-smoothing: antialiased;
}
#<?php echo esc_attr($instance_id); ?> * { box-sizing: border-box; }
#<?php echo esc_attr($instance_id); ?> .fb-wrap { max-width: 100%; margin: 0; padding: 0; }

#<?php echo esc_attr($instance_id); ?> .fb-title {
    font-family: "Fraunces", Georgia, serif;
    font-variation-settings: "opsz" 144;
    font-size: clamp(1.9rem, 1.3rem + 2vw, 2.8rem);
    font-weight: 700;
    letter-spacing: -0.025em;
    color: var(--fb-ink);
    margin: 0 0 40px 0;
    line-height: 1.05;
}
#<?php echo esc_attr($instance_id); ?> .fb-title em { font-style: italic; font-weight: 600; color: var(--fb-green); }

#<?php echo esc_attr($instance_id); ?> .fb-summary {
    display: flex;
    gap: 48px;
    align-items: center;
    padding: 40px;
    background: var(--fb-surface);
    border: 1.5px solid var(--fb-line);
    border-radius: 42px 56px 42px 56px / 56px 42px 56px 42px;
    box-shadow: var(--fb-shadow-sm);
    margin-bottom: 36px;
    position: relative;
    overflow: hidden;
}
#<?php echo esc_attr($instance_id); ?> .fb-summary::before {
    content: "";
    position: absolute;
    top: -50px;
    right: -50px;
    width: 180px;
    height: 180px;
    background: var(--fb-leaf-soft);
    border-radius: 50% 30% 60% 40% / 40% 60% 30% 50%;
    opacity: 0.7;
}
#<?php echo esc_attr($instance_id); ?> .fb-summary > * { position: relative; }
#<?php echo esc_attr($instance_id); ?> .fb-score {
    text-align: center;
    flex-shrink: 0;
    min-width: 160px;
}
#<?php echo esc_attr($instance_id); ?> .fb-score-num {
    font-family: "Fraunces", Georgia, serif;
    font-variation-settings: "opsz" 144;
    font-size: 4rem;
    font-weight: 700;
    line-height: 1;
    color: var(--fb-green);
    margin-bottom: 12px;
    letter-spacing: -0.03em;
}
#<?php echo esc_attr($instance_id); ?> .fb-score-count {
    font-size: 13px;
    color: var(--fb-body);
    margin-top: 10px;
}
#<?php echo esc_attr($instance_id); ?> .fb-dist {
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    gap: 9px;
}
#<?php echo esc_attr($instance_id); ?> .fb-dist-row {
    display: flex;
    align-items: center;
    gap: 12px;
}
#<?php echo esc_attr($instance_id); ?> .fb-dist-label {
    font-size: 13px;
    font-weight: 700;
    color: var(--fb-body);
    width: 16px;
    text-align: right;
    flex-shrink: 0;
}
#<?php echo esc_attr($instance_id); ?> .fb-dist-bg {
    flex-grow: 1;
    height: 9px;
    background: var(--fb-leaf-soft);
    border-radius: 9999px;
    overflow: hidden;
}
#<?php echo esc_attr($instance_id); ?> .fb-dist-bar {
    height: 100%;
    background: linear-gradient(90deg, var(--fb-leaf) 0%, var(--fb-green) 100%);
    border-radius: 9999px;
}
#<?php echo esc_attr($instance_id); ?> .fb-dist-count {
    font-size: 13px;
    color: var(--fb-body);
    width: 28px;
    flex-shrink: 0;
}

#<?php echo esc_attr($instance_id); ?> .fb-cats {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 40px;
}
#<?php echo esc_attr($instance_id); ?> .fb-cat {
    background: var(--fb-leaf-soft);
    border-radius: 9999px;
    padding: 9px 17px;
    display: flex;
    align-items: center;
    gap: 10px;
}
#<?php echo esc_attr($instance_id); ?> .fb-cat-name { font-size: 13px; color: var(--fb-body); }
#<?php echo esc_attr($instance_id); ?> .fb-cat-score {
    font-family: "Fraunces", Georgia, serif;
    font-variation-settings: "opsz" 24;
    font-size: 15px;
    font-weight: 700;
    color: var(--fb-green);
}

#<?php echo esc_attr($instance_id); ?> .fb-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    align-items: start;
    gap: 26px;
    margin-bottom: 32px;
}
@media (max-width: 980px) { #<?php echo esc_attr($instance_id); ?> .fb-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 640px) { #<?php echo esc_attr($instance_id); ?> .fb-grid { grid-template-columns: 1fr; } }

#<?php echo esc_attr($instance_id); ?> .fb-card {
    position: relative;
    background: var(--fb-surface);
    border: 1.5px solid var(--fb-line);
    border-radius: 36px 48px 36px 48px / 48px 36px 48px 36px;
    box-shadow: var(--fb-shadow-sm);
    padding: 30px;
    overflow: hidden;
}
#<?php echo esc_attr($instance_id); ?> .fb-card:nth-child(even) {
    border-radius: 48px 36px 48px 36px / 36px 48px 36px 48px;
}
#<?php echo esc_attr($instance_id); ?> .fb-card:nth-child(3n) {
    border-radius: 42px 42px 56px 32px / 42px 42px 32px 56px;
}
#<?php echo esc_attr($instance_id); ?> .fb-quote {
    font-family: "Fraunces", Georgia, serif;
    font-variation-settings: "opsz" 144;
    font-size: 4.2rem;
    line-height: 0.6;
    color: var(--fb-leaf);
    opacity: 0.4;
    position: absolute;
    top: 26px;
    right: 28px;
    font-style: italic;
    font-weight: 700;
}
/* .fb-quote moet absoluut blijven (zit in de hoek); andere kinderen krijgen
   wel position:relative zodat ze boven eventueel ::before-decoraties stapelen. */
#<?php echo esc_attr($instance_id); ?> .fb-card > *:not(.fb-quote) { position: relative; }
#<?php echo esc_attr($instance_id); ?> .fb-card-stars { margin-bottom: 14px; }
#<?php echo esc_attr($instance_id); ?> .fb-card-title {
    font-family: "Fraunces", Georgia, serif;
    font-variation-settings: "opsz" 36;
    font-size: 17px;
    font-weight: 700;
    color: var(--fb-ink);
    margin-bottom: 10px;
    letter-spacing: -0.01em;
}
#<?php echo esc_attr($instance_id); ?> .fb-card-desc {
    font-size: 15px;
    line-height: 1.7;
    color: var(--fb-body);
    margin-bottom: 20px;
}
#<?php echo esc_attr($instance_id); ?> .fb-card-foot {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    border-top: 1px solid var(--fb-line);
    padding-top: 14px;
}
#<?php echo esc_attr($instance_id); ?> .fb-card-name {
    font-size: 14px;
    font-weight: 700;
    color: var(--fb-ink);
}
#<?php echo esc_attr($instance_id); ?> .fb-card-date { font-size: 12px; color: var(--fb-body); }
#<?php echo esc_attr($instance_id); ?> .fb-card-module {
    font-size: 12px;
    font-weight: 700;
    color: var(--fb-green);
    margin-top: 10px;
}

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
    #<?php echo esc_attr($instance_id); ?> .fb-summary { flex-direction: column; gap: 28px; padding: 32px; }
    #<?php echo esc_attr($instance_id); ?> .fb-score { width: 100%; }
}
</style>

<div id="<?php echo esc_attr($instance_id); ?>">
    <div class="fb-wrap">

        <h2 class="fb-title">Wat deelnemers <em>delen</em></h2>

        <div class="fb-summary">
            <div class="fb-score">
                <div class="fb-score-num"><?php echo esc_html(number_format($avg, 1, ',', '')); ?></div>
                <div><?php echo mentor_render_stars($avg, 20, $stars); ?></div>
                <div class="fb-score-count"><?php echo esc_html($total); ?> review<?php echo esc_html($total !== 1 ? 's' : ''); ?></div>
            </div>
            <div class="fb-dist">
                <?php for ($i = 5; $i >= 1; $i--):
                    $count = $distribution[$i] ?? ($distribution["$i"] ?? 0);
                    $pct = $total > 0 ? ($count / $total) * 100 : 0;
                ?>
                <div class="fb-dist-row">
                    <span class="fb-dist-label"><?php echo esc_html($i); ?></span>
                    <div class="fb-dist-bg"><div class="fb-dist-bar" style="width: <?php echo esc_attr($pct); ?>%"></div></div>
                    <span class="fb-dist-count"><?php echo esc_html($count); ?></span>
                </div>
                <?php endfor; ?>
            </div>
        </div>

        <?php if (!empty($cat_averages)): ?>
        <div class="fb-cats">
            <?php foreach ($cat_averages as $cat):
                $cat_name = $cat['category_name'] ?? $cat['name'] ?? '';
                $cat_avg = $cat['average'] ?? $cat['avg'] ?? 0;
                if (empty($cat_name)) continue;
            ?>
            <div class="fb-cat">
                <span class="fb-cat-name"><?php echo esc_html($cat_name); ?></span>
                <span class="fb-cat-score"><?php echo esc_html(number_format((float) $cat_avg, 1, ',', '')); ?></span>
                <?php echo mentor_render_stars((float) $cat_avg, 13, $stars); ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php $review_index = 0; ?>
        <div class="fb-grid">
            <?php foreach ($reviews as $review):
                $hidden = $review_index >= $initial_visible ? 'fb-extra' : '';
                $review_index++;
            ?>
            <div class="fb-card <?php echo esc_attr($hidden); ?>"
                 <?php if ($hidden): ?>style="display:none;"<?php endif; ?>>
                <span class="fb-quote" aria-hidden="true">&ldquo;</span>
                <div class="fb-card-stars"><?php echo mentor_render_stars($review['overall_rating'] ?? 0, 16, $stars); ?></div>
                <?php if (!empty($review['title'])): ?>
                    <div class="fb-card-title"><?php echo esc_html($review['title']); ?></div>
                <?php endif; ?>
                <?php if (!empty($review['description'])): ?>
                    <div class="fb-card-desc"><?php echo esc_html($review['description']); ?></div>
                <?php endif; ?>
                <div class="fb-card-foot">
                    <span class="fb-card-name"><?php echo esc_html($review['display_name'] ?? 'Anoniem'); ?></span>
                    <span class="fb-card-date"><?php
                        $date = $review['published_at'] ?? $review['created'] ?? '';
                        if ($date) echo esc_html(date_i18n('j F Y', strtotime($date)));
                    ?></span>
                </div>
                <?php if (empty($module_id) && !empty($review['module_title'])): ?>
                    <div class="fb-card-module"><?php echo esc_html($review['module_title']); ?></div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if ($review_index > $initial_visible): ?>
        <div style="text-align: center;" id="<?php echo esc_attr($instance_id); ?>-show-all-wrap">
            <button id="<?php echo esc_attr($instance_id); ?>-show-all" class="fb-show-all" type="button">
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
                root.querySelectorAll(".fb-extra").forEach(function(el) {
                    el.style.display = "";
                    el.classList.remove("fb-extra");
                });
                if (wrap) wrap.style.display = "none";
            });
        }
    });
})();
</script>
<?php endif; ?>
