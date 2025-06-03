<?php
// Prevent direct access
defined('ABSPATH') or die('No script kiddies please!');
?>
<div class="tw container mx-auto px-4">
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-2">
        <?php foreach ($courses['results'] as $course): ?>
            <div class="max-w-sm rounded overflow-hidden shadow-lg m-4 flex flex-col">
                <?php if (!empty($course['image_card_medium'])) : ?>
                    <img class="w-full object-cover" style="height: 200px !important;" src="<?php echo esc_url($course['image_card_medium']); ?>" alt="<?php echo esc_attr($course['title']); ?>">
                <?php endif; ?>
                <div class="px-6 py-4 flex-grow">
                    <div class="font-bold text-xl mb-2"><?php echo esc_html($course['title']); ?></div>
                    <p class="text-gray-700 text-base">
                        <?php echo wp_kses_post($course['description_truncated']); ?>
                    </p>
                </div>
                <div class="px-6 pt-4 pb-2 mt-auto">
                    <span class="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2 mb-2"
      style="background-color: var(--color-secondary); color: var(--color-body-text);">
    <strong>&euro;<?php echo esc_html($course['price']); ?></strong>
</span>
                    <?php if (!empty($course['link_to_mentor'])) : ?>
                        <a href="<?php echo esc_url($course['link_to_mentor']); ?>"
                           class="inline-block text-white font-bold py-2 px-4 rounded transition-colors duration-200"
                           style="background-color: var(--color-primary);"
                           onmouseover="this.style.backgroundColor='var(--color-secondary-hover)'"
                           onmouseout="this.style.backgroundColor='var(--color-primary)'">
                            Meer info
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>