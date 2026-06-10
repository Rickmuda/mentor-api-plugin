<?php
/**
 * Skin: sales - Docenten ("Sales-cockpit", data-dashboard-thema)
 * Verwacht dezelfde variabelen als templates/cursus-docenten.php: $teachers, $api_url
 */
defined('ABSPATH') or die('No script kiddies please!');

if (empty($teachers)) return;

if (!wp_style_is('mentor-sales-fonts', 'enqueued')) {
    wp_enqueue_style(
        'mentor-sales-fonts',
        'https://fonts.googleapis.com/css2?family=Sora:wght@500;600;700;800&family=Inter:wght@400;500;600&family=IBM+Plex+Mono:wght@500;600&display=swap',
        array(),
        null
    );
}

$instance_id = 'sc-docenten-' . wp_unique_id();
?>

<style>
#<?php echo esc_attr($instance_id); ?> {
    --sc-bg: #F4F7F6;
    --sc-ink: #0E1626;
    --sc-body: #5B6675;
    --sc-emerald: #19C37D;
    --sc-emerald-dk: #0E8F5C;
    --sc-emerald-soft: #D7F0E4;
    --sc-line: #DDE4E3;

    font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    -webkit-font-smoothing: antialiased;
}
#<?php echo esc_attr($instance_id); ?> * { box-sizing: border-box; }
#<?php echo esc_attr($instance_id); ?> .sc-trainers {
    display: flex;
    flex-direction: column;
    gap: 80px;
}
#<?php echo esc_attr($instance_id); ?> .sc-trainer {
    display: flex;
    gap: 56px;
    align-items: center;
}
#<?php echo esc_attr($instance_id); ?> .sc-trainer:nth-child(even) {
    flex-direction: row-reverse;
}

/* Photo with crisp dashboard frame */
#<?php echo esc_attr($instance_id); ?> .sc-photo-wrap {
    flex: 0 0 320px;
    max-width: 320px;
    position: relative;
}
#<?php echo esc_attr($instance_id); ?> .sc-photo-wrap::before {
    /* Soft emerald panel behind the photo */
    content: "";
    position: absolute;
    inset: -22px -22px 22px 22px;
    background: var(--sc-emerald-soft);
    border-radius: 16px;
    z-index: 0;
}
#<?php echo esc_attr($instance_id); ?> .sc-trainer:nth-child(even) .sc-photo-wrap::before {
    inset: -22px 22px 22px -22px;
}
#<?php echo esc_attr($instance_id); ?> .sc-photo-wrap::after {
    /* Thin emerald top-rule accent */
    content: "";
    position: absolute;
    top: -22px;
    left: -22px;
    width: 86px;
    height: 4px;
    background: var(--sc-emerald);
    border-radius: 9999px;
    z-index: 2;
}
#<?php echo esc_attr($instance_id); ?> .sc-trainer:nth-child(even) .sc-photo-wrap::after {
    left: auto;
    right: -22px;
}
#<?php echo esc_attr($instance_id); ?> .sc-photo {
    position: relative;
    z-index: 1;
    width: 100%;
    aspect-ratio: 1 / 1;
    object-fit: contain;
    object-position: center bottom;
    background: var(--sc-emerald-soft);
    border-radius: 16px;
    box-shadow: 0 22px 50px -22px rgba(14, 22, 38, 0.30);
    display: block;
}
#<?php echo esc_attr($instance_id); ?> .sc-photo-ph {
    position: relative;
    z-index: 1;
    width: 100%;
    aspect-ratio: 1 / 1;
    border-radius: 16px;
    background: linear-gradient(135deg, var(--sc-emerald-soft) 0%, #B5E6CE 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--sc-emerald-dk);
    font-family: "IBM Plex Mono", ui-monospace, monospace;
    font-size: 3.8rem;
    font-weight: 600;
    letter-spacing: 0.02em;
    box-shadow: 0 22px 50px -22px rgba(14, 22, 38, 0.30);
}

#<?php echo esc_attr($instance_id); ?> .sc-body { flex: 1 1 auto; min-width: 0; }
#<?php echo esc_attr($instance_id); ?> .sc-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    font-family: "IBM Plex Mono", ui-monospace, monospace;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--sc-emerald-dk);
    margin-bottom: 12px;
}
#<?php echo esc_attr($instance_id); ?> .sc-eyebrow svg { width: 14px; height: 14px; }
#<?php echo esc_attr($instance_id); ?> .sc-name {
    font-family: "Sora", -apple-system, sans-serif;
    font-size: 2rem;
    font-weight: 700;
    letter-spacing: -0.02em;
    color: var(--sc-ink);
    margin: 0 0 16px 0;
    line-height: 1.1;
}
#<?php echo esc_attr($instance_id); ?> .sc-themes {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin: 0 0 20px 0;
}
#<?php echo esc_attr($instance_id); ?> .sc-theme {
    font-family: "IBM Plex Mono", ui-monospace, monospace;
    font-size: 12px;
    font-weight: 600;
    padding: 5px 14px;
    background: var(--sc-emerald-soft);
    color: var(--sc-emerald-dk);
    border-radius: 8px;
}
#<?php echo esc_attr($instance_id); ?> .sc-bio {
    font-size: 16px;
    line-height: 1.8;
    color: var(--sc-body);
}
#<?php echo esc_attr($instance_id); ?> .sc-bio p { margin: 0 0 14px 0; }
#<?php echo esc_attr($instance_id); ?> .sc-bio p:last-child { margin-bottom: 0; }

/* Sales story - quote-style with emerald rule marker */
#<?php echo esc_attr($instance_id); ?> .sc-story {
    margin-top: 26px;
    padding: 22px 26px;
    background: var(--sc-bg);
    border-radius: 14px;
    position: relative;
    overflow: hidden;
}
#<?php echo esc_attr($instance_id); ?> .sc-story::before {
    content: "";
    position: absolute;
    left: 0; top: 0; bottom: 0;
    width: 4px;
    background: var(--sc-emerald);
    border-radius: 9999px;
}
#<?php echo esc_attr($instance_id); ?> .sc-story-label {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-family: "IBM Plex Mono", ui-monospace, monospace;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--sc-emerald-dk);
    margin-bottom: 10px;
}
#<?php echo esc_attr($instance_id); ?> .sc-story-label svg { width: 13px; height: 13px; }
#<?php echo esc_attr($instance_id); ?> .sc-story p {
    margin: 0;
    font-family: "Sora", -apple-system, sans-serif;
    font-size: 16px;
    line-height: 1.65;
    color: var(--sc-ink);
    font-style: normal;
}

@media (max-width: 768px) {
    #<?php echo esc_attr($instance_id); ?> .sc-trainer,
    #<?php echo esc_attr($instance_id); ?> .sc-trainer:nth-child(even) {
        flex-direction: column;
        gap: 28px;
        align-items: flex-start;
    }
    #<?php echo esc_attr($instance_id); ?> .sc-photo-wrap { flex-basis: auto; max-width: 240px; }
    #<?php echo esc_attr($instance_id); ?> .sc-name { font-size: 1.55rem; }
}
</style>

<div id="<?php echo esc_attr($instance_id); ?>">
    <div class="sc-trainers">
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

            $sc_stories = [
                'Marieke van der Berg' => 'Ik verloor ooit een deal die ik al binnen waande, puur omdat ik bleef pitchen terwijl de klant juist wilde sparren over zijn eigen risico. Die afwijzing leerde me meer dan tien gewonnen offertes: een goede account manager praat minder en luistert beter. Dat is precies wat ik in deze training doorgeef.',
                'Joost Hendriks'       => 'Mijn grootste account bouwde ik niet op met een scherpe prijs, maar door drie jaar lang elke belofte na te komen, ook de kleine. Vertrouwen is de enige valuta die in sales blijft renderen. Hoe je dat vertrouwen systematisch opbouwt en meetbaar maakt, staat centraal in wat we hier doen.',
            ];
            $sc_story = $sc_stories[$name] ?? 'De beste deals sluit je niet door harder te verkopen, maar door scherper te luisteren naar wat de klant echt op het spel heeft staan. In deze training maak je van losse gesprekken een voorspelbaar proces, met cijfers die je elke maand laten sturen in plaats van hopen.';
        ?>
            <div class="sc-trainer">
                <div class="sc-photo-wrap">
                    <?php if ($has_photo): ?>
                        <img class="sc-photo" src="<?php echo esc_url($photo); ?>" alt="<?php echo esc_attr($name); ?>">
                    <?php else: ?>
                        <div class="sc-photo-ph"><?php echo esc_html($initials !== '' ? $initials : '✦'); ?></div>
                    <?php endif; ?>
                </div>
                <div class="sc-body">
                    <span class="sc-eyebrow">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M4 16l5-5 4 4 7-7" stroke="#0E8F5C" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M16 8h4v4" stroke="#0E8F5C" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Trainer
                    </span>
                    <h3 class="sc-name"><?php echo esc_html($name); ?></h3>
                    <?php if (!empty($teacher['themes'])): ?>
                        <div class="sc-themes">
                            <?php foreach ($teacher['themes'] as $theme): ?>
                                <span class="sc-theme"><?php echo esc_html($theme); ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($bio !== ''): ?>
                        <div class="sc-bio"><?php echo wp_kses_post(wpautop($bio)); ?></div>
                    <?php endif; ?>
                    <?php if ($sc_story !== ''): ?>
                        <div class="sc-story">
                            <span class="sc-story-label">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M4 16l5-5 4 4 7-7" stroke="#0E8F5C" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M16 8h4v4" stroke="#0E8F5C" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                Persoonlijk verhaal
                            </span>
                            <p>&ldquo;<?php echo esc_html($sc_story); ?>&rdquo;</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
