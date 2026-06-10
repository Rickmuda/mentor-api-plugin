<?php
/**
 * Skin: werving-selectie - Cursusdetailpagina ("Decisive hiring", executive/beslis-thema)
 * Verwacht dezelfde variabelen als templates/cursus-detail.php:
 *   $course (array), $tracks, $api_url
 */
defined('ABSPATH') or die('No script kiddies please!');

if (empty($course) || !is_array($course)) {
    echo '<p>Cursus niet gevonden.</p>';
    return;
}

if (!wp_style_is('mentor-werving-selectie-fonts', 'enqueued')) {
    wp_enqueue_style(
        'mentor-werving-selectie-fonts',
        'https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600&display=swap',
        array(),
        null
    );
}

$instance_id = 'ws-detail-' . wp_unique_id();

$title         = $course['title'] ?? '';
$description   = $course['description'] ?? '';
$image         = $course['image'] ?? ($course['image_card_medium'] ?? '');
$price         = $course['price'] ?? '';
$total_price   = $course['total_price'] ?? '';
$subject       = $course['subject']['title'] ?? '';
$link          = $course['link_to_mentor'] ?? '#';
$incl_vat      = !empty($course['show_prices_including_vat']);
$discount      = $course['discount'] ?? [];
$discount_perc = $discount['discount_perc'] ?? 0;
$discount_name = $discount['discount_name'] ?? '';
$teachers      = $course['teachers'] ?? [];

$price_num = (float) str_replace(['.', ','], ['', '.'], (string) $price);
$total_num = (float) str_replace(['.', ','], ['', '.'], (string) $total_price);
?>

<style>
#<?php echo esc_attr($instance_id); ?> {
    --ws-bg: #F5F3EE;
    --ws-surface: #FFFFFF;
    --ws-ink: #14181F;
    --ws-body: #5A5E66;
    --ws-coral: #FF4F36;
    --ws-coral-dark: #E23A22;
    --ws-coral-soft: #FCE4DF;
    --ws-line: #E2DED6;
    --ws-shadow: 0 24px 50px -28px rgba(20, 24, 31, 0.28);
    --ws-shadow-sm: 0 6px 18px -10px rgba(20, 24, 31, 0.18);

    background: transparent;
    color: var(--ws-ink);
    font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    -webkit-font-smoothing: antialiased;
}
#<?php echo esc_attr($instance_id); ?> * { box-sizing: border-box; }
#<?php echo esc_attr($instance_id); ?> .ws-wrap { max-width: 100%; margin: 0; padding: 0; }

#<?php echo esc_attr($instance_id); ?> .ws-label {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.16em;
    text-transform: uppercase;
    color: var(--ws-coral);
    margin: 0 0 22px 0;
}
#<?php echo esc_attr($instance_id); ?> .ws-label svg { width: 14px; height: 14px; }

#<?php echo esc_attr($instance_id); ?> .ws-hero {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 64px;
    align-items: center;
}
#<?php echo esc_attr($instance_id); ?> .ws-title {
    font-family: "Space Grotesk", "Segoe UI", sans-serif;
    font-size: clamp(2.6rem, 1.5rem + 3.4vw, 4.2rem);
    font-weight: 700;
    line-height: 1.02;
    letter-spacing: -0.02em;
    color: var(--ws-ink);
    margin: 0 0 30px 0;
}
#<?php echo esc_attr($instance_id); ?> .ws-title em {
    font-style: normal;
    font-weight: 700;
    color: var(--ws-coral);
}

/* Sharp framed hero image with coral accent bar */
#<?php echo esc_attr($instance_id); ?> .ws-hero-media {
    position: relative;
}
#<?php echo esc_attr($instance_id); ?> .ws-img-frame {
    position: relative;
    width: 100%;
    aspect-ratio: 4 / 4.4;
}
#<?php echo esc_attr($instance_id); ?> .ws-img-frame::before {
    /* Coral accent block offset behind the image */
    content: "";
    position: absolute;
    left: -18px;
    bottom: -18px;
    width: 44%;
    height: 44%;
    background: var(--ws-coral);
    border-radius: 8px;
    opacity: 0.16;
    z-index: 0;
}
#<?php echo esc_attr($instance_id); ?> .ws-img-frame::after {
    /* Thin hairline frame, top-right offset */
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
#<?php echo esc_attr($instance_id); ?> .ws-img {
    position: relative;
    z-index: 1;
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 8px;
    box-shadow: var(--ws-shadow);
    display: block;
}

#<?php echo esc_attr($instance_id); ?> .ws-price {
    background: var(--ws-surface);
    border: 1px solid var(--ws-line);
    border-left: 4px solid var(--ws-coral);
    border-radius: 8px;
    box-shadow: var(--ws-shadow-sm);
    padding: 24px 28px;
    margin: 0 0 30px 0;
    display: inline-block;
    min-width: 240px;
    position: relative;
    overflow: hidden;
}
#<?php echo esc_attr($instance_id); ?> .ws-price > * { position: relative; }
#<?php echo esc_attr($instance_id); ?> .ws-price-label {
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.16em;
    text-transform: uppercase;
    color: var(--ws-body);
    margin-bottom: 6px;
}
#<?php echo esc_attr($instance_id); ?> .ws-price-amount {
    font-family: "Space Grotesk", "Segoe UI", sans-serif;
    font-size: 2.3rem;
    font-weight: 700;
    color: var(--ws-ink);
    line-height: 1;
    letter-spacing: -0.02em;
}
#<?php echo esc_attr($instance_id); ?> .ws-price-vat {
    font-size: 13px;
    color: var(--ws-body);
    margin-top: 6px;
}
#<?php echo esc_attr($instance_id); ?> .ws-price-total {
    font-size: 13px;
    color: var(--ws-body);
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px solid var(--ws-line);
}
#<?php echo esc_attr($instance_id); ?> .ws-price-total strong { color: var(--ws-ink); }
#<?php echo esc_attr($instance_id); ?> .ws-discount {
    display: inline-block;
    margin-top: 14px;
    font-size: 12px;
    font-weight: 600;
    color: var(--ws-coral-dark);
    background: var(--ws-coral-soft);
    padding: 5px 13px;
    border-radius: 4px;
}

#<?php echo esc_attr($instance_id); ?> .ws-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 22px;
    background: var(--ws-coral);
    color: #fff;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    border-radius: 6px;
    box-shadow: 0 10px 22px -12px rgba(255, 79, 54, 0.65);
    transition: background 0.15s ease, transform 0.15s ease;
}
@media (hover: hover) and (pointer: fine) {
    #<?php echo esc_attr($instance_id); ?> .ws-btn:hover {
        background: var(--ws-coral-dark);
        transform: translateY(-1px);
    }
}
#<?php echo esc_attr($instance_id); ?> .ws-btn:active { transform: translateY(0); }
#<?php echo esc_attr($instance_id); ?> .ws-btn svg { width: 14px; height: 14px; }

/* Thin coral rule between hero and body */
#<?php echo esc_attr($instance_id); ?> .ws-divider {
    margin: 80px 0 56px 0;
    height: 0;
    border: 0;
    border-top: 1px solid var(--ws-line);
    position: relative;
}
#<?php echo esc_attr($instance_id); ?> .ws-divider::before {
    content: "";
    position: absolute;
    top: -1px;
    left: 0;
    width: 72px;
    height: 0;
    border-top: 3px solid var(--ws-coral);
}

#<?php echo esc_attr($instance_id); ?> .ws-body {
    max-width: 68ch;
    margin: 0;
    font-size: 17px;
    line-height: 1.85;
    color: var(--ws-body);
}
#<?php echo esc_attr($instance_id); ?> .ws-body p { margin: 0 0 20px 0; }
#<?php echo esc_attr($instance_id); ?> .ws-body h2,
#<?php echo esc_attr($instance_id); ?> .ws-body h3 {
    font-family: "Space Grotesk", "Segoe UI", sans-serif;
    color: var(--ws-ink);
    font-weight: 600;
    letter-spacing: -0.01em;
}
#<?php echo esc_attr($instance_id); ?> .ws-body strong { color: var(--ws-ink); }

#<?php echo esc_attr($instance_id); ?> .ws-section-title {
    font-family: "Space Grotesk", "Segoe UI", sans-serif;
    font-size: clamp(1.8rem, 1.3rem + 1.6vw, 2.6rem);
    font-weight: 700;
    letter-spacing: -0.02em;
    color: var(--ws-ink);
    margin: 88px 0 36px 0;
    display: inline-flex;
    align-items: baseline;
    gap: 14px;
    position: relative;
    padding-bottom: 14px;
}
#<?php echo esc_attr($instance_id); ?> .ws-section-title::after {
    content: "";
    position: absolute;
    left: 0;
    bottom: 0;
    width: 56px;
    height: 0;
    border-top: 3px solid var(--ws-coral);
}
#<?php echo esc_attr($instance_id); ?> .ws-section-title em {
    font-style: normal;
    font-weight: 700;
    color: var(--ws-coral);
}
#<?php echo esc_attr($instance_id); ?> .ws-teachers {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    align-items: start;
    gap: 28px;
}
#<?php echo esc_attr($instance_id); ?> .ws-teacher {
    position: relative;
    background: var(--ws-surface);
    border: 1px solid var(--ws-line);
    border-left: 3px solid var(--ws-coral);
    border-radius: 8px;
    box-shadow: var(--ws-shadow-sm);
    padding: 28px;
    display: flex;
    gap: 18px;
    align-items: flex-start;
    overflow: hidden;
}
#<?php echo esc_attr($instance_id); ?> .ws-teacher-photo {
    width: 72px;
    height: 72px;
    border-radius: 6px;
    object-fit: cover;
    flex-shrink: 0;
    position: relative;
    z-index: 1;
}
#<?php echo esc_attr($instance_id); ?> .ws-teacher-name {
    font-family: "Space Grotesk", "Segoe UI", sans-serif;
    font-size: 18px;
    font-weight: 600;
    color: var(--ws-ink);
    margin: 4px 0 8px 0;
    position: relative;
    z-index: 1;
}
#<?php echo esc_attr($instance_id); ?> .ws-teacher-bio {
    font-size: 14px;
    line-height: 1.65;
    color: var(--ws-body);
    position: relative;
    z-index: 1;
}
#<?php echo esc_attr($instance_id); ?> .ws-teacher-themes {
    display: flex;
    flex-wrap: wrap;
    gap: 7px;
    margin-top: 14px;
    position: relative;
    z-index: 1;
}
#<?php echo esc_attr($instance_id); ?> .ws-teacher-theme {
    font-size: 11px;
    font-weight: 600;
    padding: 4px 12px;
    background: var(--ws-coral-soft);
    color: var(--ws-coral-dark);
    border-radius: 4px;
}

@media (max-width: 860px) {
    #<?php echo esc_attr($instance_id); ?> .ws-hero {
        grid-template-columns: 1fr;
        gap: 40px;
    }
    #<?php echo esc_attr($instance_id); ?> .ws-hero-media { order: -1; }
}
@media (max-width: 640px) {
    #<?php echo esc_attr($instance_id); ?> .ws-wrap { padding: 52px 20px; }
    #<?php echo esc_attr($instance_id); ?> .ws-body { margin-top: 48px; font-size: 16px; }
}
</style>

<div id="<?php echo esc_attr($instance_id); ?>">
    <div class="ws-wrap">

        <div class="ws-hero">
            <div class="ws-hero-info">
                <?php if (!empty($subject)): ?>
                    <p class="ws-label">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M5 12h12m0 0l-5-5m5 5l-5 5" stroke="#FF4F36" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <?php echo esc_html($subject); ?>
                    </p>
                <?php endif; ?>

                <h1 class="ws-title"><?php echo esc_html($title); ?></h1>

                <?php if ($price_num > 0 || $total_num > 0): ?>
                <div class="ws-price">
                    <div class="ws-price-label">Investering</div>
                    <div class="ws-price-amount">&euro;<?php echo esc_html($price); ?></div>
                    <div class="ws-price-vat"><?php echo $incl_vat ? 'incl. btw' : 'excl. btw'; ?></div>
                    <?php if (!empty($total_price) && $total_price !== $price): ?>
                        <div class="ws-price-total">Totaal incl. materiaal: <strong>&euro;<?php echo esc_html($total_price); ?></strong></div>
                    <?php endif; ?>
                    <?php if ($discount_perc > 0): ?>
                        <div class="ws-discount"><?php echo esc_html($discount_name ?: $discount_perc . '% korting'); ?></div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <div>
                    <a href="<?php echo esc_url($link); ?>" class="ws-btn">
                        Inschrijven
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                        </svg>
                    </a>
                </div>
            </div>

            <?php if (!empty($image)): ?>
            <div class="ws-hero-media">
                <div class="ws-img-frame">
                    <img class="ws-img" src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>">
                </div>
            </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($description)): ?>
            <hr class="ws-divider" aria-hidden="true">
            <div class="ws-body"><?php echo wp_kses_post($description); ?></div>
        <?php endif; ?>

        <?php if (!empty($teachers)): ?>
            <h2 class="ws-section-title">Onze <em>trainers</em></h2>
            <div class="ws-teachers">
                <?php foreach ($teachers as $teacher):
                    $photo = $teacher['profile_picture_lg'] ?? $teacher['profile_picture'] ?? '';
                    if (!empty($photo) && strpos($photo, 'http') !== 0) {
                        $photo = rtrim($api_url, '/') . '/' . ltrim($photo, '/');
                        if (strpos($photo, '..') !== false) $photo = '';
                    }
                    $bio = wp_strip_all_tags($teacher['summary'] ?? '');
                    if (mb_strlen($bio) > 160) {
                        $bio = mb_substr($bio, 0, 160) . '...';
                    }
                ?>
                    <div class="ws-teacher">
                        <?php if (!empty($photo) && strpos($photo, 'gravatar') === false): ?>
                            <img class="ws-teacher-photo" src="<?php echo esc_url($photo); ?>" alt="<?php echo esc_attr($teacher['full_name'] ?? ''); ?>">
                        <?php endif; ?>
                        <div>
                            <div class="ws-teacher-name"><?php echo esc_html($teacher['full_name'] ?? ''); ?></div>
                            <?php if (!empty($bio)): ?>
                                <div class="ws-teacher-bio"><?php echo esc_html($bio); ?></div>
                            <?php endif; ?>
                            <?php if (!empty($teacher['themes'])): ?>
                                <div class="ws-teacher-themes">
                                    <?php foreach ($teacher['themes'] as $theme): ?>
                                        <span class="ws-teacher-theme"><?php echo esc_html($theme); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</div>
