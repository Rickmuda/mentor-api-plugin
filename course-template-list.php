<?php
// Prevent direct access
defined('ABSPATH') or die('No script kiddies please!');
?>

<div class="tailwind-scope tw container mx-auto px-4">
    <div class="grid grid-cols-1 gap-4">
        <?php foreach ($context['courses']['results'] as $course): ?>
            <div class="flex flex-col md:flex-row rounded overflow-hidden shadow-lg m-4">
                <?php if (!empty($course['image_card_medium'])) : ?>
                    <div class="flex-shrink-0">
                        <img class="w-full md:w-48 object-cover" style="height: 200px;" src="<?php echo esc_url($course['image_card_medium']); ?>" alt="<?php echo esc_attr($course['title']); ?>">
                    </div>
                <?php endif; ?>
                <div class="p-4 flex flex-col justify-between">
                    <div>
                        <div class="font-bold text-xl mb-2"><?php echo esc_html($course['title']); ?></div>
                        <p class="text-gray-700 text-base">
                            <?php echo wp_kses_post($course['description_truncated']); ?>
                        </p>
                    </div>
                    <div class="mt-4">
                        <span class="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2 mb-2"><strong>&euro;<?php echo esc_html($course['price']); ?></strong></span>
                        <?php if (!empty($course['link_to_mentor'])) : ?>
                            <a href="<?php echo esc_url($course['link_to_mentor']); ?>" class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Meer info</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
