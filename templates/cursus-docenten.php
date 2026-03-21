<?php
defined('ABSPATH') or die('No script kiddies please!');

if (empty($teachers)) return;

$instance_id = 'mentor-docenten-' . wp_unique_id();
?>

<style>
#<?php echo esc_attr($instance_id); ?> .mdt-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
}
#<?php echo esc_attr($instance_id); ?> .mdt-card {
    background: #fff;
    border-radius: 16px;
    border: 1px solid #e5e7eb;
    padding: 24px;
    display: flex;
    gap: 16px;
    align-items: flex-start;
}
#<?php echo esc_attr($instance_id); ?> .mdt-photo {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    object-fit: cover;
    flex-shrink: 0;
}
#<?php echo esc_attr($instance_id); ?> .mdt-name {
    font-size: 16px;
    font-weight: 700;
    color: var(--color-body-text, #1f2937);
    margin: 0 0 4px 0;
}
#<?php echo esc_attr($instance_id); ?> .mdt-bio {
    font-size: 13px;
    color: #6b7280;
    margin-top: 6px;
    line-height: 1.5;
}
#<?php echo esc_attr($instance_id); ?> .mdt-themes {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    margin-top: 8px;
}
#<?php echo esc_attr($instance_id); ?> .mdt-theme {
    font-size: 11px;
    font-weight: 600;
    padding: 3px 8px;
    border-radius: 9999px;
    background: #f3f4f6;
    color: #6b7280;
}
</style>

<div id="<?php echo esc_attr($instance_id); ?>">
    <div class="mdt-grid">
        <?php foreach ($teachers as $teacher):
            $photo = $teacher['profile_picture_lg'] ?? $teacher['profile_picture'] ?? '';
            if (!empty($photo) && strpos($photo, 'http') !== 0) {
                $photo = rtrim($api_url, '/') . '/' . ltrim($photo, '/');
                if (strpos($photo, '..') !== false) $photo = '';
            }
            $bio = wp_strip_all_tags($teacher['summary'] ?? '');
            if (mb_strlen($bio) > 150) {
                $bio = mb_substr($bio, 0, 150) . '...';
            }
        ?>
            <div class="mdt-card">
                <?php if (!empty($photo) && strpos($photo, 'gravatar') === false): ?>
                    <img class="mdt-photo" src="<?php echo esc_url($photo); ?>" alt="<?php echo esc_attr($teacher['full_name'] ?? ''); ?>">
                <?php endif; ?>
                <div>
                    <div class="mdt-name"><?php echo esc_html($teacher['full_name'] ?? ''); ?></div>
                    <?php if (!empty($bio)): ?>
                        <div class="mdt-bio"><?php echo esc_html($bio); ?></div>
                    <?php endif; ?>
                    <?php if (!empty($teacher['themes'])): ?>
                        <div class="mdt-themes">
                            <?php foreach ($teacher['themes'] as $theme): ?>
                                <span class="mdt-theme"><?php echo esc_html($theme); ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
