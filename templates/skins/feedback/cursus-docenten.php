<?php
/**
 * Skin: feedback - Docenten ("Organisch & groei", feedback-thema)
 * Verwacht dezelfde variabelen als templates/cursus-docenten.php: $teachers, $api_url
 */
defined('ABSPATH') or die('No script kiddies please!');

if (empty($teachers)) return;

if (!wp_style_is('mentor-feedback-fonts', 'enqueued')) {
    wp_enqueue_style(
        'mentor-feedback-fonts',
        'https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700;9..144,800&family=Inter:wght@400;500;600;700&display=swap',
        array(),
        null
    );
}

$instance_id = 'fb-docenten-' . wp_unique_id();
?>

<style>
#<?php echo esc_attr($instance_id); ?> {
    --fb-bg: #FBFAF4;
    --fb-ink: #1E2D1E;
    --fb-body: #5B6A5B;
    --fb-green: #4e7847;
    --fb-leaf: #91d66b;
    --fb-leaf-soft: #E5F2D6;

    font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    -webkit-font-smoothing: antialiased;
}
#<?php echo esc_attr($instance_id); ?> * { box-sizing: border-box; }
#<?php echo esc_attr($instance_id); ?> .fb-trainers {
    display: flex;
    flex-direction: column;
    gap: 80px;
}
#<?php echo esc_attr($instance_id); ?> .fb-trainer {
    display: flex;
    gap: 56px;
    align-items: center;
}
#<?php echo esc_attr($instance_id); ?> .fb-trainer:nth-child(even) {
    flex-direction: row-reverse;
}

/* Photo with organic blob frame */
#<?php echo esc_attr($instance_id); ?> .fb-photo-wrap {
    flex: 0 0 320px;
    max-width: 320px;
    position: relative;
}
#<?php echo esc_attr($instance_id); ?> .fb-photo-wrap::before {
    /* Background blob behind the photo */
    content: "";
    position: absolute;
    inset: -28px -22px -14px -14px;
    background: var(--fb-leaf);
    border-radius: 56% 44% 62% 38% / 50% 58% 42% 50%;
    opacity: 0.55;
    z-index: 0;
}
#<?php echo esc_attr($instance_id); ?> .fb-trainer:nth-child(even) .fb-photo-wrap::before {
    inset: -28px -14px -14px -22px;
    border-radius: 44% 56% 38% 62% / 58% 50% 50% 42%;
}
#<?php echo esc_attr($instance_id); ?> .fb-photo-wrap::after {
    /* Second deeper blob */
    content: "";
    position: absolute;
    inset: 18px -32px -22px 18px;
    background: var(--fb-green);
    border-radius: 48% 52% 36% 64% / 60% 40% 60% 40%;
    opacity: 0.16;
    z-index: 0;
}
#<?php echo esc_attr($instance_id); ?> .fb-photo {
    position: relative;
    z-index: 1;
    width: 100%;
    aspect-ratio: 1 / 1;
    object-fit: contain;
    object-position: center bottom;
    background: var(--fb-leaf-soft);
    border-radius: 50% 50% 55% 45% / 48% 52% 46% 50%;
    box-shadow: 0 22px 50px -22px rgba(30, 45, 30, 0.30);
    display: block;
}
#<?php echo esc_attr($instance_id); ?> .fb-photo-ph {
    position: relative;
    z-index: 1;
    width: 100%;
    aspect-ratio: 1 / 1;
    border-radius: 50% 50% 55% 45% / 48% 52% 46% 50%;
    background: linear-gradient(135deg, var(--fb-leaf-soft) 0%, #C8E5B0 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--fb-green);
    font-family: "Fraunces", serif;
    font-variation-settings: "opsz" 144;
    font-size: 3.8rem;
    font-weight: 700;
    letter-spacing: 0.02em;
    box-shadow: 0 22px 50px -22px rgba(30, 45, 30, 0.30);
}

#<?php echo esc_attr($instance_id); ?> .fb-body { flex: 1 1 auto; min-width: 0; }
#<?php echo esc_attr($instance_id); ?> .fb-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--fb-green);
    margin-bottom: 12px;
}
#<?php echo esc_attr($instance_id); ?> .fb-eyebrow svg { width: 14px; height: 14px; }
#<?php echo esc_attr($instance_id); ?> .fb-name {
    font-family: "Fraunces", Georgia, serif;
    font-variation-settings: "opsz" 96;
    font-size: 2rem;
    font-weight: 700;
    letter-spacing: -0.02em;
    color: var(--fb-ink);
    margin: 0 0 16px 0;
    line-height: 1.1;
}
#<?php echo esc_attr($instance_id); ?> .fb-themes {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin: 0 0 20px 0;
}
#<?php echo esc_attr($instance_id); ?> .fb-theme {
    font-size: 12px;
    font-weight: 700;
    padding: 5px 14px;
    background: var(--fb-leaf-soft);
    color: var(--fb-green);
    border-radius: 9999px;
}
#<?php echo esc_attr($instance_id); ?> .fb-bio {
    font-size: 16px;
    line-height: 1.8;
    color: var(--fb-body);
}
#<?php echo esc_attr($instance_id); ?> .fb-bio p { margin: 0 0 14px 0; }
#<?php echo esc_attr($instance_id); ?> .fb-bio p:last-child { margin-bottom: 0; }

/* Feedback story - quote-style with sprout marker */
#<?php echo esc_attr($instance_id); ?> .fb-story {
    margin-top: 26px;
    padding: 22px 26px;
    background: var(--fb-bg);
    border-radius: 22px 28px 22px 28px / 28px 22px 28px 22px;
    position: relative;
    overflow: hidden;
}
#<?php echo esc_attr($instance_id); ?> .fb-story::before {
    content: "";
    position: absolute;
    left: 0; top: 0; bottom: 0;
    width: 4px;
    background: var(--fb-leaf);
    border-radius: 9999px;
}
#<?php echo esc_attr($instance_id); ?> .fb-story-label {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--fb-green);
    margin-bottom: 10px;
}
#<?php echo esc_attr($instance_id); ?> .fb-story-label svg { width: 13px; height: 13px; }
#<?php echo esc_attr($instance_id); ?> .fb-story p {
    margin: 0;
    font-family: "Fraunces", Georgia, serif;
    font-variation-settings: "opsz" 24;
    font-size: 16px;
    line-height: 1.65;
    color: var(--fb-ink);
    font-style: italic;
}

@media (max-width: 768px) {
    #<?php echo esc_attr($instance_id); ?> .fb-trainer,
    #<?php echo esc_attr($instance_id); ?> .fb-trainer:nth-child(even) {
        flex-direction: column;
        gap: 28px;
        align-items: flex-start;
    }
    #<?php echo esc_attr($instance_id); ?> .fb-photo-wrap { flex-basis: auto; max-width: 240px; }
    #<?php echo esc_attr($instance_id); ?> .fb-name { font-size: 1.55rem; }
}
</style>

<div id="<?php echo esc_attr($instance_id); ?>">
    <div class="fb-trainers">
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

            $fb_stories = [
                'Marieke van der Berg' => 'Ik kreeg ooit van een collega de feedback dat mijn "feedback" eigenlijk verkapte kritiek was. Dat schopte hard tegen mijn ego, en juist dat moment werd het zaadje van mijn werk nu. Echt geven én ontvangen is een vak - en het vraagt iets van je hart, niet alleen van je hoofd.',
                'Joost Hendriks'       => 'In mijn vorige rol kreeg ik tijdens een 360-feedback ronde te horen dat ik zelden iemand een compliment gaf. Schokkend, want ik vond mezelf juist waarderend. Sindsdien weet ik: feedback geven leer je pas écht door eerst te oefenen in écht ontvangen.',
            ];
            $fb_story = $fb_stories[$name] ?? 'Goede feedback is als water voor een plant - te weinig en het groeit niet, te veel en het verzuipt. Wat ik in deze training meeneem, is hoe je dat evenwicht voor jezelf en je team vindt, met eerlijkheid én zorg.';
        ?>
            <div class="fb-trainer">
                <div class="fb-photo-wrap">
                    <?php if ($has_photo): ?>
                        <img class="fb-photo" src="<?php echo esc_url($photo); ?>" alt="<?php echo esc_attr($name); ?>">
                    <?php else: ?>
                        <div class="fb-photo-ph"><?php echo esc_html($initials !== '' ? $initials : '✦'); ?></div>
                    <?php endif; ?>
                </div>
                <div class="fb-body">
                    <span class="fb-eyebrow">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 2C8 6 8 12 12 13C16 12 16 6 12 2Z" fill="#91d66b"/>
                            <path d="M12 13V22" stroke="#4e7847" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        Trainer
                    </span>
                    <h3 class="fb-name"><?php echo esc_html($name); ?></h3>
                    <?php if (!empty($teacher['themes'])): ?>
                        <div class="fb-themes">
                            <?php foreach ($teacher['themes'] as $theme): ?>
                                <span class="fb-theme"><?php echo esc_html($theme); ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($bio !== ''): ?>
                        <div class="fb-bio"><?php echo wp_kses_post(wpautop($bio)); ?></div>
                    <?php endif; ?>
                    <?php if ($fb_story !== ''): ?>
                        <div class="fb-story">
                            <span class="fb-story-label">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M12 22V11" stroke="#4e7847" stroke-width="2" stroke-linecap="round"/>
                                    <path d="M12 11C12 11 6 8 6 3C11 3 12 8 12 11Z" fill="#91d66b"/>
                                    <path d="M12 13C12 13 18 10 18 5C13 5 12 10 12 13Z" fill="#91d66b"/>
                                </svg>
                                Persoonlijk verhaal
                            </span>
                            <p>&ldquo;<?php echo esc_html($fb_story); ?>&rdquo;</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
