<?php
// Prevent direct access
defined('ABSPATH') or die('No script kiddies please!');

$instance_id = 'mentor-cats-' . wp_unique_id();
$cat_items = $categories['results'] ?? [];
?>

<style>
#<?php echo $instance_id; ?> .mcat-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}
@media (max-width: 1024px) {
    #<?php echo $instance_id; ?> .mcat-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
@media (max-width: 640px) {
    #<?php echo $instance_id; ?> .mcat-grid {
        grid-template-columns: 1fr;
    }
}
#<?php echo $instance_id; ?> .mcat-card {
    background: #fff;
    border-radius: 16px;
    border: 1px solid #e5e7eb;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}
#<?php echo $instance_id; ?> .mcat-card-img {
    width: 100%;
    height: 180px;
    object-fit: cover;
    display: block;
}
#<?php echo $instance_id; ?> .mcat-card-body {
    padding: 24px;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}
#<?php echo $instance_id; ?> .mcat-card-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--color-primary, #417AB3);
    margin: 0 0 12px 0;
    line-height: 1.3;
}
#<?php echo $instance_id; ?> .mcat-card-desc {
    font-size: 14px;
    color: #6b7280;
    line-height: 1.6;
    margin: 0 0 20px 0;
    flex-grow: 1;
}
#<?php echo $instance_id; ?> .mcat-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-bottom: 16px;
}
#<?php echo $instance_id; ?> .mcat-tag {
    display: inline-block;
    font-size: 11px;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 9999px;
    background: #f3f4f6;
    color: #6b7280;
}
#<?php echo $instance_id; ?> .mcat-btn {
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
    margin-top: auto;
}
#<?php echo $instance_id; ?> .mcat-btn:hover {
    opacity: 0.9;
}
#<?php echo $instance_id; ?> .mcat-btn svg {
    width: 16px;
    height: 16px;
    margin-left: 8px;
}
#<?php echo $instance_id; ?> .mcat-search {
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
#<?php echo $instance_id; ?> .mcat-search:focus {
    outline: none;
    border-color: var(--color-primary, #417AB3);
    box-shadow: 0 0 0 2px rgba(65, 122, 179, 0.15);
}
#<?php echo $instance_id; ?> .mcat-no-results {
    grid-column: 1 / -1;
    text-align: center;
    padding: 40px 20px;
    color: #9ca3af;
    font-size: 15px;
}
</style>

<div class="tailwind-scope tw container mx-auto px-4" id="<?php echo $instance_id; ?>">
    <div style="padding: 40px 0;">

        <!-- Header -->
        <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 32px;">
            <h2 style="font-size: 2.25rem; font-weight: 800; color: var(--color-primary, #417AB3); margin: 0; line-height: 1.2;">
                Thema's
            </h2>

            <input type="text"
                   id="<?php echo $instance_id; ?>-search"
                   class="mcat-search"
                   placeholder="Zoek een thema..."
                   aria-label="Zoek een thema">
        </div>

        <!-- Cards -->
        <div class="mcat-grid" id="<?php echo $instance_id; ?>-grid">
            <?php foreach ($cat_items as $category):
                $desc = wp_kses_post($category['description']);
                if (mb_strlen(strip_tags($desc)) > 120) {
                    $desc = mb_substr(strip_tags($desc), 0, 120) . '...';
                }
                $tags = $category['catalogtag_set'] ?? [];
            ?>
                <div class="mcat-card"
                     data-title="<?php echo esc_attr(strtolower($category['title'] ?? '')); ?>">

                    <?php if (!empty($category['image'])): ?>
                        <img class="mcat-card-img"
                             src="<?php echo esc_url($category['image']); ?>"
                             alt="<?php echo esc_attr($category['title']); ?>">
                    <?php endif; ?>

                    <div class="mcat-card-body">
                        <h3 class="mcat-card-title"><?php echo esc_html($category['title']); ?></h3>

                        <?php if (!empty($tags)): ?>
                            <div class="mcat-tags">
                                <?php foreach ($tags as $tag): ?>
                                    <span class="mcat-tag"><?php echo esc_html($tag['title'] ?? $tag['name'] ?? ''); ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($desc)): ?>
                            <div class="mcat-card-desc"><?php echo wp_kses_post($desc); ?></div>
                        <?php endif; ?>

                        <a href="<?php echo esc_url($api_url); ?>/theme/<?php echo intval($category['id']); ?>/"
                           class="mcat-btn">
                            Bekijk trainingen
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                            </svg>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    </div>
</div>

<script>
(function() {
    document.addEventListener("DOMContentLoaded", function() {
        var root = document.getElementById("<?php echo $instance_id; ?>");
        if (!root) return;

        var searchInput = root.querySelector("#<?php echo $instance_id; ?>-search");
        var grid = root.querySelector("#<?php echo $instance_id; ?>-grid");
        var cards = root.querySelectorAll(".mcat-card");

        function applyFilter() {
            var searchVal = searchInput ? searchInput.value.toLowerCase().trim() : "";
            var visibleCount = 0;

            cards.forEach(function(card) {
                var match = !searchVal || card.dataset.title.indexOf(searchVal) !== -1;
                card.style.display = match ? "" : "none";
                if (match) visibleCount++;
            });

            var noResults = root.querySelector(".mcat-no-results");
            if (visibleCount === 0) {
                if (!noResults) {
                    noResults = document.createElement("div");
                    noResults.className = "mcat-no-results";
                    noResults.textContent = "Geen thema\u2019s gevonden.";
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
                debounceTimer = setTimeout(applyFilter, 200);
            });
        }
    });
})();
</script>
