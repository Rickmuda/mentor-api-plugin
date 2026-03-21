<?php
defined('ABSPATH') or die('No script kiddies please!');

$instance_id = 'mentor-reviews-' . wp_unique_id();
$avg = $aggregate['average_rating'] ?? 0;
$total = $aggregate['total_reviews'] ?? 0;
$distribution = $aggregate['rating_distribution'] ?? [];
$cat_averages = $aggregate['category_averages'] ?? [];
$initial_visible = 6;

if ($total < 1) {
    return;
}
?>

<style>
#<?php echo esc_attr($instance_id); ?> .mr-header {
    display: flex;
    gap: 40px;
    align-items: flex-start;
    margin-bottom: 32px;
    padding: 28px;
    background: #fff;
    border-radius: 16px;
    border: 1px solid #e5e7eb;
}
@media (max-width: 640px) {
    #<?php echo esc_attr($instance_id); ?> .mr-header {
        flex-direction: column;
        gap: 24px;
    }
}
#<?php echo esc_attr($instance_id); ?> .mr-score-block {
    text-align: center;
    min-width: 140px;
    flex-shrink: 0;
}
#<?php echo esc_attr($instance_id); ?> .mr-score-num {
    font-size: 3rem;
    font-weight: 800;
    color: var(--color-body-text, #1f2937);
    line-height: 1;
    margin-bottom: 8px;
}
#<?php echo esc_attr($instance_id); ?> .mr-score-stars {
    display: inline-flex;
    gap: 2px;
    margin-bottom: 6px;
}
#<?php echo esc_attr($instance_id); ?> .mr-score-count {
    font-size: 13px;
    color: #9ca3af;
}
#<?php echo esc_attr($instance_id); ?> .mr-distribution {
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    gap: 6px;
}
#<?php echo esc_attr($instance_id); ?> .mr-dist-row {
    display: flex;
    align-items: center;
    gap: 10px;
}
#<?php echo esc_attr($instance_id); ?> .mr-dist-label {
    font-size: 13px;
    font-weight: 600;
    color: #6b7280;
    width: 30px;
    text-align: right;
    flex-shrink: 0;
}
#<?php echo esc_attr($instance_id); ?> .mr-dist-bar-bg {
    flex-grow: 1;
    height: 10px;
    background: #f3f4f6;
    border-radius: 5px;
    overflow: hidden;
}
#<?php echo esc_attr($instance_id); ?> .mr-dist-bar {
    height: 100%;
    background: #f59e0b;
    border-radius: 5px;
    transition: width 0.3s;
}
#<?php echo esc_attr($instance_id); ?> .mr-dist-count {
    font-size: 13px;
    color: #9ca3af;
    width: 28px;
    flex-shrink: 0;
}
#<?php echo esc_attr($instance_id); ?> .mr-categories {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 32px;
}
#<?php echo esc_attr($instance_id); ?> .mr-cat {
    background: #f9fafb;
    border-radius: 10px;
    padding: 10px 16px;
    display: flex;
    align-items: center;
    gap: 8px;
}
#<?php echo esc_attr($instance_id); ?> .mr-cat-name {
    font-size: 13px;
    color: #6b7280;
}
#<?php echo esc_attr($instance_id); ?> .mr-cat-score {
    font-size: 14px;
    font-weight: 700;
    color: var(--color-body-text, #1f2937);
}
#<?php echo esc_attr($instance_id); ?> .mr-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    margin-bottom: 24px;
}
@media (max-width: 768px) {
    #<?php echo esc_attr($instance_id); ?> .mr-grid {
        grid-template-columns: 1fr;
    }
}
#<?php echo esc_attr($instance_id); ?> .mr-card {
    background: #fff;
    border-radius: 16px;
    border: 1px solid #e5e7eb;
    padding: 24px;
}
#<?php echo esc_attr($instance_id); ?> .mr-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}
#<?php echo esc_attr($instance_id); ?> .mr-card-name {
    font-size: 15px;
    font-weight: 700;
    color: var(--color-body-text, #1f2937);
}
#<?php echo esc_attr($instance_id); ?> .mr-card-date {
    font-size: 12px;
    color: #9ca3af;
}
#<?php echo esc_attr($instance_id); ?> .mr-card-stars {
    display: inline-flex;
    gap: 2px;
    margin-bottom: 10px;
}
#<?php echo esc_attr($instance_id); ?> .mr-card-title {
    font-size: 14px;
    font-weight: 600;
    color: var(--color-body-text, #1f2937);
    margin-bottom: 6px;
}
#<?php echo esc_attr($instance_id); ?> .mr-card-desc {
    font-size: 14px;
    color: #6b7280;
    line-height: 1.6;
}
#<?php echo esc_attr($instance_id); ?> .mr-card-module {
    font-size: 12px;
    color: var(--color-primary, #417AB3);
    font-weight: 600;
    margin-top: 10px;
}
#<?php echo esc_attr($instance_id); ?> .mr-btn-show-all {
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
#<?php echo esc_attr($instance_id); ?> .mr-btn-show-all:hover {
    opacity: 0.9;
}
</style>

<div id="<?php echo esc_attr($instance_id); ?>">
    <div style="padding: 40px 0;">

        <h2 style="font-size: 1.75rem; font-weight: 800; color: var(--color-primary, #417AB3); margin: 0 0 24px 0; line-height: 1.2;">
            Reviews
        </h2>

        <!-- Aggregate header -->
        <div class="mr-header">
            <div class="mr-score-block">
                <div class="mr-score-num"><?php echo number_format($avg, 1, ',', ''); ?></div>
                <div class="mr-score-stars"><?php echo wp_kses_post(mentor_render_stars($avg, 22)); ?></div>
                <div class="mr-score-count"><?php echo esc_html($total); ?> review<?php echo $total !== 1 ? 's' : ''; ?></div>
            </div>
            <div class="mr-distribution">
                <?php for ($i = 5; $i >= 1; $i--):
                    $count = $distribution[$i] ?? ($distribution["$i"] ?? 0);
                    $pct = $total > 0 ? ($count / $total) * 100 : 0;
                ?>
                <div class="mr-dist-row">
                    <span class="mr-dist-label"><?php echo esc_html($i); ?></span>
                    <div class="mr-dist-bar-bg"><div class="mr-dist-bar" style="width: <?php echo esc_attr($pct); ?>%"></div></div>
                    <span class="mr-dist-count"><?php echo esc_html($count); ?></span>
                </div>
                <?php endfor; ?>
            </div>
        </div>

        <!-- Category averages -->
        <?php if (!empty($cat_averages)): ?>
        <div class="mr-categories">
            <?php foreach ($cat_averages as $cat):
                $cat_name = $cat['category_name'] ?? $cat['name'] ?? '';
                $cat_avg = $cat['average'] ?? $cat['avg'] ?? 0;
                if (empty($cat_name)) continue;
            ?>
            <div class="mr-cat">
                <span class="mr-cat-name"><?php echo esc_html($cat_name); ?></span>
                <span class="mr-cat-score"><?php echo number_format((float) $cat_avg, 1, ',', ''); ?></span>
                <?php echo wp_kses_post(mentor_render_stars((float) $cat_avg, 14)); ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Review cards -->
        <?php $review_index = 0; ?>
        <div class="mr-grid">
            <?php foreach ($reviews as $review):
                $hidden = $review_index >= $initial_visible ? 'mr-extra' : '';
                $review_index++;
            ?>
            <div class="mr-card <?php echo esc_attr($hidden); ?>"
                 <?php if ($hidden): ?>style="display:none;"<?php endif; ?>>
                <div class="mr-card-header">
                    <span class="mr-card-name"><?php echo esc_html($review['display_name'] ?? 'Anoniem'); ?></span>
                    <span class="mr-card-date"><?php
                        $date = $review['published_at'] ?? $review['created'] ?? '';
                        if ($date) echo esc_html(date_i18n('j F Y', strtotime($date)));
                    ?></span>
                </div>
                <div class="mr-card-stars"><?php echo wp_kses_post(mentor_render_stars($review['overall_rating'] ?? 0)); ?></div>
                <?php if (!empty($review['title'])): ?>
                    <div class="mr-card-title"><?php echo esc_html($review['title']); ?></div>
                <?php endif; ?>
                <?php if (!empty($review['description'])): ?>
                    <div class="mr-card-desc"><?php echo esc_html($review['description']); ?></div>
                <?php endif; ?>
                <?php if (empty($module_id) && !empty($review['module_title'])): ?>
                    <div class="mr-card-module"><?php echo esc_html($review['module_title']); ?></div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if ($review_index > $initial_visible): ?>
        <div style="text-align: center;" id="<?php echo esc_attr($instance_id); ?>-show-all-wrap">
            <button id="<?php echo esc_attr($instance_id); ?>-show-all" class="mr-btn-show-all" type="button">
                Toon alle reviews
                <svg style="width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
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
                root.querySelectorAll(".mr-extra").forEach(function(el) {
                    el.style.display = "";
                    el.classList.remove("mr-extra");
                });
                if (wrap) wrap.style.display = "none";
            });
        }
    });
})();
</script>
<?php endif; ?>
