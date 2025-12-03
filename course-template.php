<?php
// Prevent direct access
defined('ABSPATH') or die('No script kiddies please!');
?>
<div class="tailwind-scope tw container mx-auto px-4">
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-2">
        <?php foreach ($courses['results'] as $course): ?>
            <div class="max-w-sm rounded overflow-hidden shadow-lg m-4 flex flex-col">
                <?php if (!empty($course['image_card_medium'])) : ?>
                    <img class="w-full object-cover" style="height: 200px !important;" src="<?php echo esc_url($course['image_card_medium']); ?>" alt="<?php echo esc_attr($course['title']); ?>">
                <?php endif; ?>
                <div class="px-6 py-4 flex-grow">
                    <h3 class="font-bold text-xl mb-2"><?php echo esc_html($course['title']); ?></h3>
                    <div class="text-gray-700 text-base">
                        <?php echo wp_kses_post($course['description_truncated']); ?>
                    </div>
                </div>
                <div class="px-6 pt-4 pb-2 mb-2 mt-auto flex justify-between items-end">
    <span class="align-bottom">
        &euro;<?php echo esc_html($course['price']); ?>
    </span>
    <?php if (!empty($course['link_to_mentor'])) : ?>
        <a href="<?php echo esc_url($course['link_to_mentor']); ?>"
           class="inline-block text-white font-bold py-1 px-4 rounded transition-colors duration-200"
           style="background-color: var(--color-secondary);"
           onmouseover="this.style.backgroundColor='var(--color-secondary-hover)'"
           onmouseout="this.style.backgroundColor='var(--color-secondary)'">
            Meer info
        </a>
    <?php endif; ?>
</div>
            </div>
        <?php endforeach; ?>
    </div>
</div>