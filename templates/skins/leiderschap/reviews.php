<?php
/**
 * Skin: leiderschap — Reviews ("Fris & professioneel", zodan-stijl)
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

if (!wp_style_is('mentor-leiderschap-fonts', 'enqueued')) {
    wp_enqueue_style(
        'mentor-leiderschap-fonts',
        'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap',
        array(),
        null
    );
}

$stars = '#F5A524';
$instance_id = 'lc-reviews-' . wp_unique_id();
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
#<?php echo esc_attr($instance_id); ?> .lc-title {
    font-family: "Plus Jakarta Sans", "Inter", sans-serif;
    font-size: clamp(1.8rem, 1.3rem + 1.8vw, 2.6rem);
    font-weight: 800;
    letter-spacing: -0.02em;
    color: var(--lc-ink);
    margin: 0 0 36px 0;
    line-height: 1.1;
}
#<?php echo esc_attr($instance_id); ?> .lc-title span { color: var(--lc-blue); }
#<?php echo esc_attr($instance_id); ?> .lc-summary {
    display: flex;
    gap: 48px;
    align-items: center;
    padding: 36px;
    background: var(--lc-surface);
    border: 1px solid var(--lc-line);
    border-radius: 24px;
    box-shadow: var(--lc-shadow-sm);
    margin-bottom: 32px;
}
#<?php echo esc_attr($instance_id); ?> .lc-score {
    text-align: center;
    flex-shrink: 0;
    min-width: 150px;
}
#<?php echo esc_attr($instance_id); ?> .lc-score-num {
    font-family: "Plus Jakarta Sans", "Inter", sans-serif;
    font-size: 3.6rem;
    font-weight: 800;
    line-height: 1;
    color: var(--lc-blue);
    margin-bottom: 10px;
}
#<?php echo esc_attr($instance_id); ?> .lc-score-count {
    font-size: 13px;
    color: var(--lc-body);
    margin-top: 8px;
}
#<?php echo esc_attr($instance_id); ?> .lc-dist {
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    gap: 8px;
}
#<?php echo esc_attr($instance_id); ?> .lc-dist-row {
    display: flex;
    align-items: center;
    gap: 12px;
}
#<?php echo esc_attr($instance_id); ?> .lc-dist-label {
    font-size: 13px;
    font-weight: 600;
    color: var(--lc-body);
    width: 16px;
    text-align: right;
    flex-shrink: 0;
}
#<?php echo esc_attr($instance_id); ?> .lc-dist-bg {
    flex-grow: 1;
    height: 8px;
    background: var(--lc-blue-soft);
    border-radius: 9999px;
    overflow: hidden;
}
#<?php echo esc_attr($instance_id); ?> .lc-dist-bar {
    height: 100%;
    background: var(--lc-blue);
    border-radius: 9999px;
}
#<?php echo esc_attr($instance_id); ?> .lc-dist-count {
    font-size: 13px;
    color: var(--lc-body);
    width: 28px;
    flex-shrink: 0;
}
#<?php echo esc_attr($instance_id); ?> .lc-cats {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 36px;
}
#<?php echo esc_attr($instance_id); ?> .lc-cat {
    background: var(--lc-blue-soft);
    border-radius: 9999px;
    padding: 9px 16px;
    display: flex;
    align-items: center;
    gap: 9px;
}
#<?php echo esc_attr($instance_id); ?> .lc-cat-name { font-size: 13px; color: var(--lc-body); }
#<?php echo esc_attr($instance_id); ?> .lc-cat-score {
    font-family: "Plus Jakarta Sans", "Inter", sans-serif;
    font-size: 15px;
    font-weight: 700;
    color: var(--lc-blue);
}
#<?php echo esc_attr($instance_id); ?> .lc-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    align-items: start;
    gap: 22px;
    margin-bottom: 28px;
}
@media (max-width: 980px) { #<?php echo esc_attr($instance_id); ?> .lc-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 640px) { #<?php echo esc_attr($instance_id); ?> .lc-grid { grid-template-columns: 1fr; } }
#<?php echo esc_attr($instance_id); ?> .lc-card {
    background: var(--lc-surface);
    border: 1px solid var(--lc-line);
    border-radius: 20px;
    box-shadow: var(--lc-shadow-sm);
    padding: 28px;
    position: relative;
}
#<?php echo esc_attr($instance_id); ?> .lc-quote {
    font-family: "Plus Jakarta Sans", Georgia, serif;
    font-size: 3rem;
    line-height: 1;
    color: var(--lc-blue);
    opacity: 0.18;
    position: absolute;
    top: 18px;
    right: 24px;
}
#<?php echo esc_attr($instance_id); ?> .lc-card-stars { margin-bottom: 14px; }
#<?php echo esc_attr($instance_id); ?> .lc-card-title {
    font-family: "Plus Jakarta Sans", "Inter", sans-serif;
    font-size: 16px;
    font-weight: 700;
    color: var(--lc-ink);
    margin-bottom: 8px;
}
#<?php echo esc_attr($instance_id); ?> .lc-card-desc {
    font-size: 15px;
    line-height: 1.7;
    color: var(--lc-body);
    margin-bottom: 18px;
}
#<?php echo esc_attr($instance_id); ?> .lc-card-foot {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    border-top: 1px solid var(--lc-line);
    padding-top: 14px;
}
#<?php echo esc_attr($instance_id); ?> .lc-card-name {
    font-size: 14px;
    font-weight: 700;
    color: var(--lc-ink);
}
#<?php echo esc_attr($instance_id); ?> .lc-card-date { font-size: 12px; color: var(--lc-body); }
#<?php echo esc_attr($instance_id); ?> .lc-card-module {
    font-size: 12px;
    font-weight: 600;
    color: var(--lc-blue);
    margin-top: 10px;
}
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
    #<?php echo esc_attr($instance_id); ?> .lc-summary { flex-direction: column; gap: 28px; padding: 28px; }
    #<?php echo esc_attr($instance_id); ?> .lc-score { width: 100%; }
}
</style>

<div id="<?php echo esc_attr($instance_id); ?>">
    <div class="lc-wrap">

        <h2 class="lc-title">Wat deelnemers <span>zeggen</span></h2>

        <div class="lc-summary">
            <div class="lc-score">
                <div class="lc-score-num"><?php echo esc_html(number_format($avg, 1, ',', '')); ?></div>
                <div><?php echo wp_kses_post(mentor_render_stars($avg, 20, $stars)); ?></div>
                <div class="lc-score-count"><?php echo esc_html($total); ?> review<?php echo esc_html($total !== 1 ? 's' : ''); ?></div>
            </div>
            <div class="lc-dist">
                <?php for ($i = 5; $i >= 1; $i--):
                    $count = $distribution[$i] ?? ($distribution["$i"] ?? 0);
                    $pct = $total > 0 ? ($count / $total) * 100 : 0;
                ?>
                <div class="lc-dist-row">
                    <span class="lc-dist-label"><?php echo esc_html($i); ?></span>
                    <div class="lc-dist-bg"><div class="lc-dist-bar" style="width: <?php echo esc_attr($pct); ?>%"></div></div>
                    <span class="lc-dist-count"><?php echo esc_html($count); ?></span>
                </div>
                <?php endfor; ?>
            </div>
        </div>

        <?php if (!empty($cat_averages)): ?>
        <div class="lc-cats">
            <?php foreach ($cat_averages as $cat):
                $cat_name = $cat['category_name'] ?? $cat['name'] ?? '';
                $cat_avg = $cat['average'] ?? $cat['avg'] ?? 0;
                if (empty($cat_name)) continue;
            ?>
            <div class="lc-cat">
                <span class="lc-cat-name"><?php echo esc_html($cat_name); ?></span>
                <span class="lc-cat-score"><?php echo esc_html(number_format((float) $cat_avg, 1, ',', '')); ?></span>
                <?php echo wp_kses_post(mentor_render_stars((float) $cat_avg, 13, $stars)); ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php $review_index = 0; ?>
        <div class="lc-grid">
            <?php foreach ($reviews as $review):
                $hidden = $review_index >= $initial_visible ? 'lc-extra' : '';
                $review_index++;
            ?>
            <div class="lc-card <?php echo esc_attr($hidden); ?>"
                 <?php if ($hidden): ?>style="display:none;"<?php endif; ?>>
                <span class="lc-quote" aria-hidden="true">&ldquo;</span>
                <div class="lc-card-stars"><?php echo wp_kses_post(mentor_render_stars($review['overall_rating'] ?? 0, 16, $stars)); ?></div>
                <?php if (!empty($review['title'])): ?>
                    <div class="lc-card-title"><?php echo esc_html($review['title']); ?></div>
                <?php endif; ?>
                <?php if (!empty($review['description'])): ?>
                    <div class="lc-card-desc"><?php echo esc_html($review['description']); ?></div>
                <?php endif; ?>
                <div class="lc-card-foot">
                    <span class="lc-card-name"><?php echo esc_html($review['display_name'] ?? 'Anoniem'); ?></span>
                    <span class="lc-card-date"><?php
                        $date = $review['published_at'] ?? $review['created'] ?? '';
                        if ($date) echo esc_html(date_i18n('j F Y', strtotime($date)));
                    ?></span>
                </div>
                <?php if (empty($module_id) && !empty($review['module_title'])): ?>
                    <div class="lc-card-module"><?php echo esc_html($review['module_title']); ?></div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if ($review_index > $initial_visible): ?>
        <div style="text-align: center;" id="<?php echo esc_attr($instance_id); ?>-show-all-wrap">
            <button id="<?php echo esc_attr($instance_id); ?>-show-all" class="lc-show-all" type="button">
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
                root.querySelectorAll(".lc-extra").forEach(function(el) {
                    el.style.display = "";
                    el.classList.remove("lc-extra");
                });
                if (wrap) wrap.style.display = "none";
            });
        }
    });
})();
</script>
<?php endif; ?>
