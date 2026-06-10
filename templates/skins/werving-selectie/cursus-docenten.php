<?php
/**
 * Skin: werving-selectie - Docenten ("Decisive hiring", executive/beslis-thema)
 * Verwacht dezelfde variabelen als templates/cursus-docenten.php: $teachers, $api_url
 */
defined('ABSPATH') or die('No script kiddies please!');

if (empty($teachers)) return;

if (!wp_style_is('mentor-werving-selectie-fonts', 'enqueued')) {
    wp_enqueue_style(
        'mentor-werving-selectie-fonts',
        'https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600&display=swap',
        array(),
        null
    );
}

$instance_id = 'ws-docenten-' . wp_unique_id();
?>

<style>
#<?php echo esc_attr($instance_id); ?> {
    --ws-bg: #F5F3EE;
    --ws-ink: #14181F;
    --ws-body: #5A5E66;
    --ws-coral: #FF4F36;
    --ws-coral-dark: #E23A22;
    --ws-coral-soft: #FCE4DF;
    --ws-line: #E2DED6;

    font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    -webkit-font-smoothing: antialiased;
}
#<?php echo esc_attr($instance_id); ?> * { box-sizing: border-box; }
#<?php echo esc_attr($instance_id); ?> .ws-trainers {
    display: flex;
    flex-direction: column;
    gap: 80px;
}
#<?php echo esc_attr($instance_id); ?> .ws-trainer {
    display: flex;
    gap: 56px;
    align-items: center;
}
#<?php echo esc_attr($instance_id); ?> .ws-trainer:nth-child(even) {
    flex-direction: row-reverse;
}

/* Photo with sharp frame and coral accent block */
#<?php echo esc_attr($instance_id); ?> .ws-photo-wrap {
    flex: 0 0 320px;
    max-width: 320px;
    position: relative;
}
#<?php echo esc_attr($instance_id); ?> .ws-photo-wrap::before {
    /* Coral accent block behind the photo */
    content: "";
    position: absolute;
    left: -18px;
    bottom: -18px;
    width: 46%;
    height: 46%;
    background: var(--ws-coral);
    border-radius: 8px;
    opacity: 0.16;
    z-index: 0;
}
#<?php echo esc_attr($instance_id); ?> .ws-trainer:nth-child(even) .ws-photo-wrap::before {
    left: auto;
    right: -18px;
}
#<?php echo esc_attr($instance_id); ?> .ws-photo-wrap::after {
    /* Thin hairline frame, top offset */
    content: "";
    position: absolute;
    top: -16px;
    right: -16px;
    width: 50%;
    height: 50%;
    border: 1px solid var(--ws-coral);
    border-radius: 8px;
    opacity: 0.5;
    z-index: 0;
}
#<?php echo esc_attr($instance_id); ?> .ws-trainer:nth-child(even) .ws-photo-wrap::after {
    right: auto;
    left: -16px;
}
#<?php echo esc_attr($instance_id); ?> .ws-photo {
    position: relative;
    z-index: 1;
    width: 100%;
    aspect-ratio: 1 / 1;
    object-fit: cover;
    object-position: center top;
    background: var(--ws-coral-soft);
    border-radius: 8px;
    box-shadow: 0 24px 50px -28px rgba(20, 24, 31, 0.28);
    display: block;
}
#<?php echo esc_attr($instance_id); ?> .ws-photo-ph {
    position: relative;
    z-index: 1;
    width: 100%;
    aspect-ratio: 1 / 1;
    border-radius: 8px;
    background: var(--ws-ink);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--ws-coral);
    font-family: "Space Grotesk", "Segoe UI", sans-serif;
    font-size: 3.8rem;
    font-weight: 700;
    letter-spacing: 0.02em;
    box-shadow: 0 24px 50px -28px rgba(20, 24, 31, 0.28);
}

#<?php echo esc_attr($instance_id); ?> .ws-body { flex: 1 1 auto; min-width: 0; }
#<?php echo esc_attr($instance_id); ?> .ws-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.16em;
    text-transform: uppercase;
    color: var(--ws-coral);
    margin-bottom: 12px;
}
#<?php echo esc_attr($instance_id); ?> .ws-eyebrow svg { width: 14px; height: 14px; }
#<?php echo esc_attr($instance_id); ?> .ws-name {
    font-family: "Space Grotesk", "Segoe UI", sans-serif;
    font-size: 2rem;
    font-weight: 700;
    letter-spacing: -0.02em;
    color: var(--ws-ink);
    margin: 0 0 16px 0;
    line-height: 1.1;
}
#<?php echo esc_attr($instance_id); ?> .ws-themes {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin: 0 0 20px 0;
}
#<?php echo esc_attr($instance_id); ?> .ws-theme {
    font-size: 12px;
    font-weight: 600;
    padding: 5px 14px;
    background: var(--ws-coral-soft);
    color: var(--ws-coral-dark);
    border-radius: 4px;
}
#<?php echo esc_attr($instance_id); ?> .ws-bio {
    font-size: 16px;
    line-height: 1.8;
    color: var(--ws-body);
}
#<?php echo esc_attr($instance_id); ?> .ws-bio p { margin: 0 0 14px 0; }
#<?php echo esc_attr($instance_id); ?> .ws-bio p:last-child { margin-bottom: 0; }

/* Personal story - editorial quote with coral left rule */
#<?php echo esc_attr($instance_id); ?> .ws-story {
    margin-top: 26px;
    padding: 22px 26px;
    background: var(--ws-bg);
    border-radius: 8px;
    position: relative;
    overflow: hidden;
}
#<?php echo esc_attr($instance_id); ?> .ws-story::before {
    content: "";
    position: absolute;
    left: 0; top: 0; bottom: 0;
    width: 4px;
    background: var(--ws-coral);
}
#<?php echo esc_attr($instance_id); ?> .ws-story-label {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.16em;
    text-transform: uppercase;
    color: var(--ws-coral);
    margin-bottom: 10px;
}
#<?php echo esc_attr($instance_id); ?> .ws-story-label svg { width: 13px; height: 13px; }
#<?php echo esc_attr($instance_id); ?> .ws-story p {
    margin: 0;
    font-family: "Space Grotesk", "Segoe UI", sans-serif;
    font-size: 16px;
    line-height: 1.65;
    color: var(--ws-ink);
    font-weight: 500;
}

@media (max-width: 768px) {
    #<?php echo esc_attr($instance_id); ?> .ws-trainer,
    #<?php echo esc_attr($instance_id); ?> .ws-trainer:nth-child(even) {
        flex-direction: column;
        gap: 28px;
        align-items: flex-start;
    }
    #<?php echo esc_attr($instance_id); ?> .ws-photo-wrap { flex-basis: auto; max-width: 240px; }
    #<?php echo esc_attr($instance_id); ?> .ws-name { font-size: 1.55rem; }
}
</style>

<div id="<?php echo esc_attr($instance_id); ?>">
    <div class="ws-trainers">
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

            $ws_stories = [
                'Marieke van der Berg' => 'Vroeg in mijn carriere koos ik een kandidaat puur op klik in het gesprek - hij was vlot, zelfverzekerd, sympathiek. Negen maanden later moest ik afscheid nemen. Die misser leerde me dat een gestructureerd interview met vooraf vastgestelde criteria geen bureaucratie is, maar bescherming tegen je eigen onderbuik.',
                'Joost Hendriks'       => 'Ik dacht jarenlang dat ik mensen goed kon lezen. Tot ik mijn eigen wervingsbeslissingen ging meten en zag dat mijn beste hires juist degenen waren die ik in eerste instantie had getwijfeld. Selecteren leer je pas echt als je je eerste indruk durft te wantrouwen en op bewijs durft te sturen.',
            ];
            $ws_story = $ws_stories[$name] ?? 'Een verkeerde aanname kost een organisatie al snel een jaarsalaris. Wat ik in deze training overdraag, is hoe je voorbij de eerste indruk kijkt, kandidaten gestructureerd toetst en met vertrouwen een beslissing neemt die standhoudt.';
        ?>
            <div class="ws-trainer">
                <div class="ws-photo-wrap">
                    <?php if ($has_photo): ?>
                        <img class="ws-photo" src="<?php echo esc_url($photo); ?>" alt="<?php echo esc_attr($name); ?>">
                    <?php else: ?>
                        <div class="ws-photo-ph"><?php echo esc_html($initials !== '' ? $initials : '✦'); ?></div>
                    <?php endif; ?>
                </div>
                <div class="ws-body">
                    <span class="ws-eyebrow">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M5 12h12m0 0l-5-5m5 5l-5 5" stroke="#FF4F36" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Trainer
                    </span>
                    <h3 class="ws-name"><?php echo esc_html($name); ?></h3>
                    <?php if (!empty($teacher['themes'])): ?>
                        <div class="ws-themes">
                            <?php foreach ($teacher['themes'] as $theme): ?>
                                <span class="ws-theme"><?php echo esc_html($theme); ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($bio !== ''): ?>
                        <div class="ws-bio"><?php echo wp_kses_post(wpautop($bio)); ?></div>
                    <?php endif; ?>
                    <?php if ($ws_story !== ''): ?>
                        <div class="ws-story">
                            <span class="ws-story-label">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M5 12h12m0 0l-5-5m5 5l-5 5" stroke="#FF4F36" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                Persoonlijk verhaal
                            </span>
                            <p>&ldquo;<?php echo esc_html($ws_story); ?>&rdquo;</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
