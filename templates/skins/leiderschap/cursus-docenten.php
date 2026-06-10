<?php
/**
 * Skin: leiderschap - Docenten ("Fris & professioneel", prominente blokken)
 * Verwacht dezelfde variabelen als templates/cursus-docenten.php: $teachers, $api_url
 */
defined('ABSPATH') or die('No script kiddies please!');

if (empty($teachers)) return;

if (!wp_style_is('mentor-leiderschap-fonts', 'enqueued')) {
    wp_enqueue_style(
        'mentor-leiderschap-fonts',
        'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap',
        array(),
        null
    );
}

$instance_id = 'lc-docenten-' . wp_unique_id();
?>

<style>
#<?php echo esc_attr($instance_id); ?> {
    font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    -webkit-font-smoothing: antialiased;
}
#<?php echo esc_attr($instance_id); ?> * { box-sizing: border-box; }
#<?php echo esc_attr($instance_id); ?> .lc-trainers {
    display: flex;
    flex-direction: column;
    gap: 64px;
}
#<?php echo esc_attr($instance_id); ?> .lc-trainer {
    display: flex;
    gap: 48px;
    align-items: center;
}
#<?php echo esc_attr($instance_id); ?> .lc-trainer:nth-child(even) {
    flex-direction: row-reverse;
}
#<?php echo esc_attr($instance_id); ?> .lc-photo-wrap {
    flex: 0 0 320px;
    max-width: 320px;
}
#<?php echo esc_attr($instance_id); ?> .lc-photo {
    width: 100%;
    aspect-ratio: 4 / 5;
    object-fit: cover;
    border-radius: 24px;
    box-shadow: 0 18px 40px -22px rgba(22, 32, 44, 0.30);
    display: block;
}
#<?php echo esc_attr($instance_id); ?> .lc-photo-ph {
    width: 100%;
    aspect-ratio: 4 / 5;
    border-radius: 24px;
    background: linear-gradient(135deg, #EAF0FF 0%, #DCE6FF 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #2F6BFF;
    font-family: "Plus Jakarta Sans", sans-serif;
    font-size: 3.4rem;
    font-weight: 800;
    letter-spacing: 0.02em;
}
#<?php echo esc_attr($instance_id); ?> .lc-body { flex: 1 1 auto; min-width: 0; }
#<?php echo esc_attr($instance_id); ?> .lc-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: #2F6BFF;
    margin-bottom: 10px;
}
#<?php echo esc_attr($instance_id); ?> .lc-eyebrow::before {
    content: "";
    width: 8px; height: 8px; border-radius: 50%; background: #2F6BFF;
}
#<?php echo esc_attr($instance_id); ?> .lc-name {
    font-family: "Plus Jakarta Sans", sans-serif;
    font-size: 1.7rem;
    font-weight: 800;
    letter-spacing: -0.01em;
    color: #16202C;
    margin: 0 0 14px 0;
    line-height: 1.15;
}
#<?php echo esc_attr($instance_id); ?> .lc-themes {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin: 0 0 18px 0;
}
#<?php echo esc_attr($instance_id); ?> .lc-theme {
    font-size: 12px;
    font-weight: 600;
    padding: 5px 13px;
    background: #EAF0FF;
    color: #2F6BFF;
    border-radius: 9999px;
}
#<?php echo esc_attr($instance_id); ?> .lc-bio {
    font-size: 16px;
    line-height: 1.8;
    color: #586575;
}
#<?php echo esc_attr($instance_id); ?> .lc-bio p { margin: 0 0 14px 0; }
#<?php echo esc_attr($instance_id); ?> .lc-bio p:last-child { margin-bottom: 0; }
#<?php echo esc_attr($instance_id); ?> .lc-story {
    margin-top: 22px;
    padding: 18px 22px;
    background: #F7F8FA;
    border-left: 3px solid #2F6BFF;
    border-radius: 0 12px 12px 0;
}
#<?php echo esc_attr($instance_id); ?> .lc-story-label {
    display: block;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: #2F6BFF;
    margin-bottom: 8px;
}
#<?php echo esc_attr($instance_id); ?> .lc-story p {
    margin: 0;
    font-size: 15px;
    line-height: 1.7;
    color: #586575;
    font-style: italic;
}

@media (max-width: 768px) {
    #<?php echo esc_attr($instance_id); ?> .lc-trainer,
    #<?php echo esc_attr($instance_id); ?> .lc-trainer:nth-child(even) {
        flex-direction: column;
        gap: 22px;
        align-items: flex-start;
    }
    #<?php echo esc_attr($instance_id); ?> .lc-photo-wrap { flex-basis: auto; max-width: 240px; }
    #<?php echo esc_attr($instance_id); ?> .lc-name { font-size: 1.45rem; }
}
</style>

<div id="<?php echo esc_attr($instance_id); ?>">
    <div class="lc-trainers">
        <?php foreach ($teachers as $teacher):
            $name  = $teacher['full_name'] ?? '';
            $photo = $teacher['profile_picture_lg'] ?? $teacher['profile_picture'] ?? '';
            if (!empty($photo) && strpos($photo, 'http') !== 0) {
                $photo = rtrim($api_url, '/') . '/' . ltrim($photo, '/');
                if (strpos($photo, '..') !== false) $photo = '';
            }
            $has_photo = !empty($photo) && strpos($photo, 'gravatar') === false;

            // Initialen voor de placeholder
            $initials = '';
            $parts = preg_split('/\s+/', trim($name));
            if (!empty($parts[0])) $initials .= mb_substr($parts[0], 0, 1);
            if (count($parts) > 1) $initials .= mb_substr(end($parts), 0, 1);
            $initials = mb_strtoupper($initials);

            $bio = trim(wp_strip_all_tags($teacher['summary'] ?? ''));

            // Verzonnen "persoonlijk verhaal" (demo) - per bekende trainer, met generieke fallback.
            $lc_stories = [
                'Marieke van der Berg' => 'Mijn eerste leidinggevende rol kreeg ik op mijn 28e - en eerlijk: ik liep vast. Ik wilde iedereen te vriend houden en durfde geen knopen door te hakken. Een mentor leerde me dat duidelijkheid juist rust geeft. Die ommekeer is precies wat ik nu aan anderen wil doorgeven.',
                'Joost Hendriks'       => 'Ik liet een scale-up groeien van 5 naar 80 mensen en ontdekte dat de techniek het makkelijke deel was - mensen écht meekrijgen was de echte uitdaging. De fouten die ik daar maakte, gebruik ik nu als mijn beste lesmateriaal.',
            ];
            $lc_story = $lc_stories[$name] ?? 'Leiderschap leer je niet uit een boek, maar door het te doen - met vallen en opstaan. Die eigen ervaring, inclusief de blunders, neem ik mee de training in, zodat jij ze niet hoeft te herhalen.';
        ?>
            <div class="lc-trainer">
                <div class="lc-photo-wrap">
                    <?php if ($has_photo): ?>
                        <img class="lc-photo" src="<?php echo esc_url($photo); ?>" alt="<?php echo esc_attr($name); ?>">
                    <?php else: ?>
                        <div class="lc-photo-ph"><?php echo esc_html($initials !== '' ? $initials : '★'); ?></div>
                    <?php endif; ?>
                </div>
                <div class="lc-body">
                    <span class="lc-eyebrow">Trainer</span>
                    <h3 class="lc-name"><?php echo esc_html($name); ?></h3>
                    <?php if (!empty($teacher['themes'])): ?>
                        <div class="lc-themes">
                            <?php foreach ($teacher['themes'] as $theme): ?>
                                <span class="lc-theme"><?php echo esc_html($theme); ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($bio !== ''): ?>
                        <div class="lc-bio"><?php echo wp_kses_post(wpautop($bio)); ?></div>
                    <?php endif; ?>
                    <?php if ($lc_story !== ''): ?>
                        <div class="lc-story">
                            <span class="lc-story-label">Persoonlijk verhaal</span>
                            <p><?php echo esc_html($lc_story); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
