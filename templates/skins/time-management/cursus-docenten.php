<?php
/**
 * Skin: time-management - Docenten ("Calm focus", planner-thema)
 * Verwacht dezelfde variabelen als templates/cursus-docenten.php: $teachers, $api_url
 *
 * Trainer-data uit de Mentor-API:
 *   full_name, profile_picture_lg / profile_picture (relatief t.o.v. $api_url), summary, themes[]
 * Geen first_name/last_name/biography/image-keys: die bestaan niet en gaven een lege kaart
 * met "TM"-placeholder en "Trainer" als naam (oorzaak van de "waar zijn de trainers"-klacht).
 */
defined('ABSPATH') or die('No script kiddies please!');

if (empty($teachers)) return;

if (!wp_style_is('mentor-time-management-fonts', 'enqueued')) {
    wp_enqueue_style(
        'mentor-time-management-fonts',
        'https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;600;700&display=swap',
        array(),
        null
    );
}

$instance_id = 'tm-docenten-' . wp_unique_id();
?>

<style>
#<?php echo esc_attr($instance_id); ?> {
    --tm-ink: #1A1F36;
    --tm-body: #525A75;
    --tm-muted: #8A8F9F;
    --tm-indigo: #2D3FB5;
    --tm-indigo-soft: #E5E8FA;
    --tm-amber: #E8930C;
    --tm-amber-soft: #FBE9C8;
    --tm-line: #E7E2D5;
    --tm-line-soft: #F0EBDF;
    --tm-surface: #FFFFFF;

    font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    -webkit-font-smoothing: antialiased;
}
#<?php echo esc_attr($instance_id); ?> * { box-sizing: border-box; }

#<?php echo esc_attr($instance_id); ?> .tm-trainers {
    display: flex;
    flex-direction: column;
    gap: 64px;
}
#<?php echo esc_attr($instance_id); ?> .tm-trainer {
    display: flex;
    gap: 56px;
    align-items: center;
}
#<?php echo esc_attr($instance_id); ?> .tm-trainer:nth-child(even) {
    flex-direction: row-reverse;
}
#<?php echo esc_attr($instance_id); ?> .tm-photo-wrap {
    flex: 0 0 300px;
    max-width: 300px;
    position: relative;
}
#<?php echo esc_attr($instance_id); ?> .tm-photo-wrap::before {
    content: "";
    position: absolute;
    inset: -16px -12px -8px 12px;
    background: var(--tm-indigo-soft);
    border-radius: 22px;
    z-index: 0;
}
#<?php echo esc_attr($instance_id); ?> .tm-trainer:nth-child(even) .tm-photo-wrap::before {
    inset: -16px 12px -8px -12px;
    background: var(--tm-amber-soft);
}
#<?php echo esc_attr($instance_id); ?> .tm-photo {
    position: relative;
    z-index: 1;
    width: 100%;
    aspect-ratio: 4 / 5;
    object-fit: cover;
    border-radius: 22px;
    border: 5px solid var(--tm-surface);
    box-shadow: 0 22px 46px -20px rgba(26, 31, 54, 0.38);
    display: block;
}
#<?php echo esc_attr($instance_id); ?> .tm-photo-ph {
    position: relative;
    z-index: 1;
    width: 100%;
    aspect-ratio: 4 / 5;
    border-radius: 22px;
    border: 5px solid var(--tm-surface);
    box-shadow: 0 22px 46px -20px rgba(26, 31, 54, 0.38);
    background: linear-gradient(135deg, var(--tm-indigo-soft) 0%, #C7CCEC 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--tm-indigo);
    font-family: "Outfit", sans-serif;
    font-size: 3.6rem;
    font-weight: 700;
    letter-spacing: -0.02em;
}
#<?php echo esc_attr($instance_id); ?> .tm-body { flex: 1 1 auto; min-width: 0; }
#<?php echo esc_attr($instance_id); ?> .tm-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    font-family: "JetBrains Mono", ui-monospace, SFMono-Regular, monospace;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: var(--tm-indigo);
    margin-bottom: 12px;
}
#<?php echo esc_attr($instance_id); ?> .tm-eyebrow::before {
    content: "";
    width: 24px;
    height: 2px;
    background: var(--tm-indigo);
}
#<?php echo esc_attr($instance_id); ?> .tm-name {
    font-family: "Outfit", sans-serif;
    font-size: clamp(1.4rem, 1rem + 1vw, 1.8rem);
    font-weight: 700;
    letter-spacing: -0.02em;
    color: var(--tm-ink);
    margin: 0 0 14px;
    line-height: 1.15;
}
#<?php echo esc_attr($instance_id); ?> .tm-bio {
    font-size: 15.5px;
    line-height: 1.75;
    color: var(--tm-body);
    margin: 0 0 18px;
}
#<?php echo esc_attr($instance_id); ?> .tm-bio p { margin: 0 0 12px; }
#<?php echo esc_attr($instance_id); ?> .tm-bio p:last-child { margin-bottom: 0; }
#<?php echo esc_attr($instance_id); ?> .tm-themes {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 16px;
}
#<?php echo esc_attr($instance_id); ?> .tm-theme {
    display: inline-block;
    font-family: "JetBrains Mono", monospace;
    font-size: 11px;
    font-weight: 600;
    color: var(--tm-body);
    background: var(--tm-line-soft);
    padding: 6px 11px;
    border-radius: 6px;
    letter-spacing: 0.02em;
}

@media (max-width: 880px) {
    #<?php echo esc_attr($instance_id); ?> .tm-trainer,
    #<?php echo esc_attr($instance_id); ?> .tm-trainer:nth-child(even) {
        flex-direction: column;
        gap: 28px;
        align-items: stretch;
    }
    #<?php echo esc_attr($instance_id); ?> .tm-photo-wrap { max-width: 280px; }
    #<?php echo esc_attr($instance_id); ?> .tm-trainers { gap: 56px; }
    #<?php echo esc_attr($instance_id); ?> .tm-name { font-size: 1.55rem; }
}
</style>

<div id="<?php echo esc_attr($instance_id); ?>">
    <div class="tm-trainers">
        <?php foreach ($teachers as $teacher):
            $name  = $teacher['full_name'] ?? '';
            $photo = $teacher['profile_picture_lg'] ?? $teacher['profile_picture'] ?? '';
            if (!empty($photo) && strpos($photo, 'http') !== 0) {
                $photo = rtrim($api_url, '/') . '/' . ltrim($photo, '/');
                if (strpos($photo, '..') !== false) $photo = '';
            }
            $has_photo = !empty($photo) && strpos($photo, 'gravatar') === false;

            $initials = '';
            $parts = preg_split('/\s+/', trim($name));
            if (!empty($parts[0])) $initials .= mb_substr($parts[0], 0, 1);
            if (count($parts) > 1) $initials .= mb_substr(end($parts), 0, 1);
            $initials = mb_strtoupper($initials);

            $bio = trim(wp_strip_all_tags($teacher['summary'] ?? ''));
            $themes = $teacher['themes'] ?? [];
        ?>
        <div class="tm-trainer">
            <div class="tm-photo-wrap">
                <?php if ($has_photo): ?>
                    <img class="tm-photo" src="<?php echo esc_url($photo); ?>" alt="<?php echo esc_attr($name); ?>">
                <?php else: ?>
                    <div class="tm-photo-ph" aria-hidden="true"><?php echo esc_html($initials !== '' ? $initials : 'TM'); ?></div>
                <?php endif; ?>
            </div>
            <div class="tm-body">
                <span class="tm-eyebrow">Trainer</span>
                <h3 class="tm-name"><?php echo esc_html($name); ?></h3>
                <?php if (!empty($themes) && is_array($themes)): ?>
                    <div class="tm-themes">
                        <?php foreach ($themes as $theme):
                            $t = is_array($theme) ? ($theme['name'] ?? '') : $theme;
                            if ($t === '') continue;
                        ?>
                            <span class="tm-theme"><?php echo esc_html($t); ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <?php if ($bio !== ''): ?>
                    <div class="tm-bio"><?php echo wp_kses_post(wpautop($bio)); ?></div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
