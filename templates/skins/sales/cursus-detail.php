<?php
/**
 * Skin: sales - Cursusdetailpagina ("Sales-cockpit", data-dashboard-thema)
 * Verwacht dezelfde variabelen als templates/cursus-detail.php:
 *   $course (array), $tracks, $api_url
 */
defined('ABSPATH') or die('No script kiddies please!');

if (empty($course) || !is_array($course)) {
    echo '<p>Cursus niet gevonden.</p>';
    return;
}

if (!wp_style_is('mentor-sales-fonts', 'enqueued')) {
    wp_enqueue_style(
        'mentor-sales-fonts',
        'https://fonts.googleapis.com/css2?family=Sora:wght@500;600;700;800&family=Inter:wght@400;500;600&family=IBM+Plex+Mono:wght@500;600&display=swap',
        array(),
        null
    );
}

$instance_id = 'sc-detail-' . wp_unique_id();

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
    --sc-bg: #F4F7F6;
    --sc-surface: #FFFFFF;
    --sc-ink: #0E1626;
    --sc-body: #5B6675;
    --sc-emerald: #19C37D;
    --sc-emerald-dk: #0E8F5C;
    --sc-emerald-soft: #D7F0E4;
    --sc-line: #DDE4E3;
    --sc-shadow: 0 22px 50px -22px rgba(14, 22, 38, 0.30);
    --sc-shadow-sm: 0 4px 14px -6px rgba(14, 22, 38, 0.18);

    background: transparent;
    color: var(--sc-ink);
    font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    -webkit-font-smoothing: antialiased;
}
#<?php echo esc_attr($instance_id); ?> * { box-sizing: border-box; }
#<?php echo esc_attr($instance_id); ?> .sc-wrap { max-width: 100%; margin: 0; padding: 0; }

#<?php echo esc_attr($instance_id); ?> .sc-label {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    font-family: "IBM Plex Mono", ui-monospace, monospace;
    font-size: 13px;
    font-weight: 600;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    color: var(--sc-emerald-dk);
    margin: 0 0 22px 0;
}
#<?php echo esc_attr($instance_id); ?> .sc-label svg { width: 18px; height: 18px; }

#<?php echo esc_attr($instance_id); ?> .sc-hero {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 64px;
    align-items: center;
}
#<?php echo esc_attr($instance_id); ?> .sc-title {
    font-family: "Sora", -apple-system, sans-serif;
    font-size: clamp(2.6rem, 1.5rem + 3.4vw, 4.2rem);
    font-weight: 800;
    line-height: 1.04;
    letter-spacing: -0.025em;
    color: var(--sc-ink);
    margin: 0 0 30px 0;
}
#<?php echo esc_attr($instance_id); ?> .sc-title em {
    font-style: normal;
    font-weight: 700;
    color: var(--sc-emerald-dk);
}

/* Crisp dashboard hero image frame */
#<?php echo esc_attr($instance_id); ?> .sc-hero-media {
    position: relative;
}
#<?php echo esc_attr($instance_id); ?> .sc-img-frame {
    position: relative;
    width: 100%;
    aspect-ratio: 4 / 4.4;
}
#<?php echo esc_attr($instance_id); ?> .sc-img-frame::before {
    /* Soft emerald panel behind the image */
    content: "";
    position: absolute;
    inset: -22px -22px 22px 22px;
    background: var(--sc-emerald-soft);
    border-radius: 16px;
    z-index: 0;
}
#<?php echo esc_attr($instance_id); ?> .sc-img-frame::after {
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
#<?php echo esc_attr($instance_id); ?> .sc-img {
    position: relative;
    z-index: 1;
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 16px;
    box-shadow: var(--sc-shadow);
    display: block;
}

#<?php echo esc_attr($instance_id); ?> .sc-price {
    background: var(--sc-surface);
    border: 1px solid var(--sc-line);
    border-top: 3px solid var(--sc-emerald);
    border-radius: 14px;
    box-shadow: var(--sc-shadow-sm);
    padding: 24px 28px;
    margin: 0 0 30px 0;
    display: inline-block;
    min-width: 240px;
    position: relative;
    overflow: hidden;
}
#<?php echo esc_attr($instance_id); ?> .sc-price > * { position: relative; }
#<?php echo esc_attr($instance_id); ?> .sc-price-label {
    font-family: "IBM Plex Mono", ui-monospace, monospace;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--sc-body);
    margin-bottom: 6px;
}
#<?php echo esc_attr($instance_id); ?> .sc-price-amount {
    font-family: "IBM Plex Mono", ui-monospace, monospace;
    font-size: 2.3rem;
    font-weight: 600;
    color: var(--sc-ink);
    line-height: 1;
    letter-spacing: -0.02em;
}
#<?php echo esc_attr($instance_id); ?> .sc-price-vat {
    font-family: "IBM Plex Mono", ui-monospace, monospace;
    font-size: 13px;
    color: var(--sc-body);
    margin-top: 6px;
}
#<?php echo esc_attr($instance_id); ?> .sc-price-total {
    font-size: 13px;
    color: var(--sc-body);
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px solid var(--sc-line);
}
#<?php echo esc_attr($instance_id); ?> .sc-price-total strong {
    color: var(--sc-ink);
    font-family: "IBM Plex Mono", ui-monospace, monospace;
}
#<?php echo esc_attr($instance_id); ?> .sc-discount {
    display: inline-block;
    margin-top: 14px;
    font-family: "IBM Plex Mono", ui-monospace, monospace;
    font-size: 12px;
    font-weight: 600;
    color: var(--sc-emerald-dk);
    background: var(--sc-emerald-soft);
    padding: 5px 13px;
    border-radius: 8px;
}

#<?php echo esc_attr($instance_id); ?> .sc-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 11px 22px;
    background: var(--sc-emerald);
    color: var(--sc-ink);
    font-size: 14px;
    font-weight: 700;
    text-decoration: none;
    border-radius: 12px;
    box-shadow: 0 8px 20px -10px rgba(25, 195, 125, 0.55);
    transition: background 0.15s ease, transform 0.15s ease;
}
@media (hover: hover) and (pointer: fine) {
    #<?php echo esc_attr($instance_id); ?> .sc-btn:hover {
        background: var(--sc-emerald-dk);
        color: #fff;
        transform: translateY(-1px);
    }
}
#<?php echo esc_attr($instance_id); ?> .sc-btn:active { transform: translateY(0); }
#<?php echo esc_attr($instance_id); ?> .sc-btn svg { width: 14px; height: 14px; }

/* Thin emerald rule divider between hero and body */
#<?php echo esc_attr($instance_id); ?> .sc-divider {
    margin: 80px 0 56px 0;
    color: var(--sc-emerald);
}
#<?php echo esc_attr($instance_id); ?> .sc-divider svg {
    display: block;
    width: 100%;
    height: 16px;
}

#<?php echo esc_attr($instance_id); ?> .sc-body {
    max-width: 68ch;
    margin: 0;
    font-size: 17px;
    line-height: 1.85;
    color: var(--sc-body);
}
#<?php echo esc_attr($instance_id); ?> .sc-body p { margin: 0 0 20px 0; }
#<?php echo esc_attr($instance_id); ?> .sc-body h2,
#<?php echo esc_attr($instance_id); ?> .sc-body h3 {
    font-family: "Sora", -apple-system, sans-serif;
    color: var(--sc-ink);
    font-weight: 700;
    letter-spacing: -0.01em;
}
#<?php echo esc_attr($instance_id); ?> .sc-body strong { color: var(--sc-ink); }

#<?php echo esc_attr($instance_id); ?> .sc-section-title {
    font-family: "Sora", -apple-system, sans-serif;
    font-size: clamp(1.8rem, 1.3rem + 1.6vw, 2.6rem);
    font-weight: 700;
    letter-spacing: -0.02em;
    color: var(--sc-ink);
    margin: 88px 0 36px 0;
    display: inline-flex;
    align-items: baseline;
    gap: 14px;
}
#<?php echo esc_attr($instance_id); ?> .sc-section-title em {
    font-style: normal;
    font-weight: 700;
    color: var(--sc-emerald-dk);
}
#<?php echo esc_attr($instance_id); ?> .sc-teachers {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    align-items: start;
    gap: 28px;
}
#<?php echo esc_attr($instance_id); ?> .sc-teacher {
    position: relative;
    background: var(--sc-surface);
    border: 1px solid var(--sc-line);
    border-top: 3px solid var(--sc-emerald);
    border-radius: 14px;
    box-shadow: var(--sc-shadow-sm);
    padding: 28px;
    display: flex;
    gap: 18px;
    align-items: flex-start;
    overflow: hidden;
}
#<?php echo esc_attr($instance_id); ?> .sc-teacher-photo {
    width: 72px;
    height: 72px;
    border-radius: 12px;
    object-fit: cover;
    flex-shrink: 0;
    position: relative;
    z-index: 1;
}
#<?php echo esc_attr($instance_id); ?> .sc-teacher-name {
    font-family: "Sora", -apple-system, sans-serif;
    font-size: 18px;
    font-weight: 700;
    color: var(--sc-ink);
    margin: 4px 0 8px 0;
    position: relative;
    z-index: 1;
}
#<?php echo esc_attr($instance_id); ?> .sc-teacher-bio {
    font-size: 14px;
    line-height: 1.65;
    color: var(--sc-body);
    position: relative;
    z-index: 1;
}
#<?php echo esc_attr($instance_id); ?> .sc-teacher-themes {
    display: flex;
    flex-wrap: wrap;
    gap: 7px;
    margin-top: 14px;
    position: relative;
    z-index: 1;
}
#<?php echo esc_attr($instance_id); ?> .sc-teacher-theme {
    font-family: "IBM Plex Mono", ui-monospace, monospace;
    font-size: 11px;
    font-weight: 600;
    padding: 4px 12px;
    background: var(--sc-emerald-soft);
    color: var(--sc-emerald-dk);
    border-radius: 8px;
}

@media (max-width: 860px) {
    #<?php echo esc_attr($instance_id); ?> .sc-hero {
        grid-template-columns: 1fr;
        gap: 40px;
    }
    #<?php echo esc_attr($instance_id); ?> .sc-hero-media { order: -1; }
}
@media (max-width: 640px) {
    #<?php echo esc_attr($instance_id); ?> .sc-wrap { padding: 52px 20px; }
    #<?php echo esc_attr($instance_id); ?> .sc-body { margin-top: 48px; font-size: 16px; }
}
</style>

<div id="<?php echo esc_attr($instance_id); ?>">
    <div class="sc-wrap">

        <div class="sc-hero">
            <div class="sc-hero-info">
                <?php if (!empty($subject)): ?>
                    <p class="sc-label">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M4 16l5-5 4 4 7-7" stroke="#0E8F5C" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M16 8h4v4" stroke="#0E8F5C" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <?php echo esc_html($subject); ?>
                    </p>
                <?php endif; ?>

                <h1 class="sc-title"><?php echo esc_html($title); ?></h1>

                <?php if ($price_num > 0 || $total_num > 0): ?>
                <div class="sc-price">
                    <div class="sc-price-label">Investering</div>
                    <div class="sc-price-amount">&euro;<?php echo esc_html($price); ?></div>
                    <div class="sc-price-vat"><?php echo $incl_vat ? 'incl. btw' : 'excl. btw'; ?></div>
                    <?php if (!empty($total_price) && $total_price !== $price): ?>
                        <div class="sc-price-total">Totaal incl. materiaal: <strong>&euro;<?php echo esc_html($total_price); ?></strong></div>
                    <?php endif; ?>
                    <?php if ($discount_perc > 0): ?>
                        <div class="sc-discount"><?php echo esc_html($discount_name ?: $discount_perc . '% korting'); ?></div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <div>
                    <a href="<?php echo esc_url($link); ?>" class="sc-btn">
                        Inschrijven
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                        </svg>
                    </a>
                </div>
            </div>

            <?php if (!empty($image)): ?>
            <div class="sc-hero-media">
                <div class="sc-img-frame">
                    <img class="sc-img" src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>">
                </div>
            </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($description)): ?>
            <div class="sc-divider" aria-hidden="true">
                <svg preserveAspectRatio="none" viewBox="0 0 1200 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <line x1="0" y1="8" x2="1200" y2="8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <rect x="0" y="4" width="86" height="8" rx="4" fill="currentColor"/>
                </svg>
            </div>
            <div class="sc-body"><?php echo wp_kses_post($description); ?></div>
        <?php endif; ?>

        <?php if (!empty($teachers)): ?>
            <h2 class="sc-section-title">Onze <em>trainers</em></h2>
            <div class="sc-teachers">
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
                    <div class="sc-teacher">
                        <?php if (!empty($photo) && strpos($photo, 'gravatar') === false): ?>
                            <img class="sc-teacher-photo" src="<?php echo esc_url($photo); ?>" alt="<?php echo esc_attr($teacher['full_name'] ?? ''); ?>">
                        <?php endif; ?>
                        <div>
                            <div class="sc-teacher-name"><?php echo esc_html($teacher['full_name'] ?? ''); ?></div>
                            <?php if (!empty($bio)): ?>
                                <div class="sc-teacher-bio"><?php echo esc_html($bio); ?></div>
                            <?php endif; ?>
                            <?php if (!empty($teacher['themes'])): ?>
                                <div class="sc-teacher-themes">
                                    <?php foreach ($teacher['themes'] as $theme): ?>
                                        <span class="sc-teacher-theme"><?php echo esc_html($theme); ?></span>
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
