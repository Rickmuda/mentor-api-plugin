<?php
// Prevent direct access
defined('ABSPATH') or die('No script kiddies please!');
?>

<div class="tailwind-scope tw">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-4">
            <?php foreach ($categories['results'] as $category): ?>
                <div class="flex h-full w-full">
                    <div class="rounded overflow-hidden shadow-lg m-4 flex flex-col h-full w-full bg-white">
                        <div class="px-6 py-4 flex flex-col flex-grow">
                            <div class="font-bold text-xl">
                                <?php echo esc_html($category['title']); ?>
                            </div>
                            <p class="text-gray-700 text-base flex-grow">
                                <?php
                                $desc = wp_kses_post($category['description']);
                                if (mb_strlen($desc) > 100) {
                                    echo mb_substr($desc, 0, 100) . '...';
                                } else {
                                    echo $desc;
                                }
                                ?>
                            </p>
                            <a href="<?php echo esc_url($api_url); ?>/theme/<?php echo $category['id'] ?>/"
                               class="inline-block text-white font-bold py-2 px-4 rounded mt-auto transition-colors duration-200"
                               style="background-color: var(--color-primary);" 
                               onmouseover="this.style.backgroundColor='var(--color-secondary-hover)'" 
                               onmouseout="this.style.backgroundColor='var(--color-primary)'">
                                Ga naar <?php echo esc_html($category['title']); ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>