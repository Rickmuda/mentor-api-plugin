<?php
/**
 * Skin: feedback - Cursusdetailpagina ("Organisch & groei", feedback-thema)
 * Verwacht dezelfde variabelen als templates/cursus-detail.php:
 *   $course (array), $tracks, $api_url
 */
defined('ABSPATH') or die('No script kiddies please!');

if (empty($course) || !is_array($course)) {
    echo '<p>Cursus niet gevonden.</p>';
    return;
}

if (!wp_style_is('mentor-feedback-fonts', 'enqueued')) {
    wp_enqueue_style(
        'mentor-feedback-fonts',
        'https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700;9..144,800&family=Inter:wght@400;500;600;700&display=swap',
        array(),
        null
    );
}

$instance_id = 'fb-detail-' . wp_unique_id();

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
    --fb-bg: #FBFAF4;
    --fb-surface: #FFFFFF;
    --fb-ink: #1E2D1E;
    --fb-body: #5B6A5B;
    --fb-green: #4e7847;
    --fb-green-dark: #3A5C35;
    --fb-leaf: #91d66b;
    --fb-leaf-soft: #E5F2D6;
    --fb-line: #E5E8DE;
    --fb-shadow: 0 22px 50px -22px rgba(30, 45, 30, 0.30);
    --fb-shadow-sm: 0 4px 14px -6px rgba(78, 120, 71, 0.18);

    background: transparent;
    color: var(--fb-ink);
    font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    -webkit-font-smoothing: antialiased;
}
#<?php echo esc_attr($instance_id); ?> * { box-sizing: border-box; }
#<?php echo esc_attr($instance_id); ?> .fb-wrap { max-width: 100%; margin: 0; padding: 0; }

#<?php echo esc_attr($instance_id); ?> .fb-label {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    color: var(--fb-green);
    margin: 0 0 22px 0;
}
#<?php echo esc_attr($instance_id); ?> .fb-label svg { width: 18px; height: 18px; }

#<?php echo esc_attr($instance_id); ?> .fb-hero {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 64px;
    align-items: center;
}
#<?php echo esc_attr($instance_id); ?> .fb-title {
    font-family: "Fraunces", Georgia, serif;
    font-optical-sizing: auto;
    font-variation-settings: "opsz" 144;
    font-size: clamp(2.6rem, 1.5rem + 3.4vw, 4.2rem);
    font-weight: 700;
    line-height: 1;
    letter-spacing: -0.025em;
    color: var(--fb-ink);
    margin: 0 0 30px 0;
}
#<?php echo esc_attr($instance_id); ?> .fb-title em {
    font-style: italic;
    font-weight: 600;
    color: var(--fb-green);
}

/* Blob-masked hero image */
#<?php echo esc_attr($instance_id); ?> .fb-hero-media {
    position: relative;
}
#<?php echo esc_attr($instance_id); ?> .fb-img-frame {
    position: relative;
    width: 100%;
    aspect-ratio: 4 / 4.4;
}
#<?php echo esc_attr($instance_id); ?> .fb-img-frame::before {
    /* Soft leaf-shaped blob behind the image */
    content: "";
    position: absolute;
    inset: -30px -22px -10px -10px;
    background: var(--fb-leaf);
    border-radius: 56% 44% 62% 38% / 50% 58% 42% 50%;
    opacity: 0.55;
    z-index: 0;
}
#<?php echo esc_attr($instance_id); ?> .fb-img-frame::after {
    /* Second deep-green blob, offset */
    content: "";
    position: absolute;
    inset: 16px -32px -22px 24px;
    background: var(--fb-green);
    border-radius: 48% 52% 36% 64% / 60% 40% 60% 40%;
    opacity: 0.18;
    z-index: 0;
}
#<?php echo esc_attr($instance_id); ?> .fb-img {
    position: relative;
    z-index: 1;
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 56% 44% 62% 38% / 50% 58% 42% 50%;
    box-shadow: var(--fb-shadow);
    display: block;
}

#<?php echo esc_attr($instance_id); ?> .fb-price {
    background: var(--fb-surface);
    border: 1.5px solid var(--fb-line);
    border-radius: 28px 38px 28px 38px / 38px 28px 38px 28px;
    box-shadow: var(--fb-shadow-sm);
    padding: 24px 28px;
    margin: 0 0 30px 0;
    display: inline-block;
    min-width: 240px;
    position: relative;
    overflow: hidden;
}
#<?php echo esc_attr($instance_id); ?> .fb-price::before {
    content: "";
    position: absolute;
    bottom: -24px;
    right: -24px;
    width: 80px;
    height: 80px;
    background: var(--fb-leaf-soft);
    border-radius: 50% 30% 60% 40% / 40% 60% 30% 50%;
    opacity: 0.7;
}
#<?php echo esc_attr($instance_id); ?> .fb-price > * { position: relative; }
#<?php echo esc_attr($instance_id); ?> .fb-price-label {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--fb-body);
    margin-bottom: 6px;
}
#<?php echo esc_attr($instance_id); ?> .fb-price-amount {
    font-family: "Fraunces", Georgia, serif;
    font-variation-settings: "opsz" 96;
    font-size: 2.3rem;
    font-weight: 700;
    color: var(--fb-ink);
    line-height: 1;
    letter-spacing: -0.02em;
}
#<?php echo esc_attr($instance_id); ?> .fb-price-vat {
    font-size: 13px;
    color: var(--fb-body);
    margin-top: 6px;
}
#<?php echo esc_attr($instance_id); ?> .fb-price-total {
    font-size: 13px;
    color: var(--fb-body);
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px solid var(--fb-line);
}
#<?php echo esc_attr($instance_id); ?> .fb-price-total strong { color: var(--fb-ink); }
#<?php echo esc_attr($instance_id); ?> .fb-discount {
    display: inline-block;
    margin-top: 14px;
    font-size: 12px;
    font-weight: 700;
    color: var(--fb-green);
    background: var(--fb-leaf-soft);
    padding: 5px 13px;
    border-radius: 9999px;
}

#<?php echo esc_attr($instance_id); ?> .fb-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: var(--fb-green);
    color: #fff;
    font-size: 14px;
    font-weight: 700;
    text-decoration: none;
    border-radius: 9999px;
    box-shadow: 0 8px 20px -10px rgba(78, 120, 71, 0.55);
    transition: background 0.15s ease, transform 0.15s ease;
}
@media (hover: hover) and (pointer: fine) {
    #<?php echo esc_attr($instance_id); ?> .fb-btn:hover {
        background: var(--fb-green-dark);
        transform: translateY(-1px);
    }
}
#<?php echo esc_attr($instance_id); ?> .fb-btn:active { transform: translateY(0); }
#<?php echo esc_attr($instance_id); ?> .fb-btn svg { width: 14px; height: 14px; }

/* Wavy divider between hero and body */
#<?php echo esc_attr($instance_id); ?> .fb-divider {
    margin: 80px 0 56px 0;
    color: var(--fb-leaf);
    opacity: 0.7;
}
#<?php echo esc_attr($instance_id); ?> .fb-divider svg {
    display: block;
    width: 100%;
    height: 22px;
}

#<?php echo esc_attr($instance_id); ?> .fb-body {
    max-width: 68ch;
    margin: 0;
    font-size: 17px;
    line-height: 1.85;
    color: var(--fb-body);
}
#<?php echo esc_attr($instance_id); ?> .fb-body p { margin: 0 0 20px 0; }
#<?php echo esc_attr($instance_id); ?> .fb-body h2,
#<?php echo esc_attr($instance_id); ?> .fb-body h3 {
    font-family: "Fraunces", Georgia, serif;
    color: var(--fb-ink);
    font-weight: 700;
    letter-spacing: -0.01em;
}
#<?php echo esc_attr($instance_id); ?> .fb-body strong { color: var(--fb-ink); }

#<?php echo esc_attr($instance_id); ?> .fb-section-title {
    font-family: "Fraunces", Georgia, serif;
    font-variation-settings: "opsz" 96;
    font-size: clamp(1.8rem, 1.3rem + 1.6vw, 2.6rem);
    font-weight: 700;
    letter-spacing: -0.02em;
    color: var(--fb-ink);
    margin: 88px 0 36px 0;
    display: inline-flex;
    align-items: baseline;
    gap: 14px;
}
#<?php echo esc_attr($instance_id); ?> .fb-section-title em {
    font-style: italic;
    font-weight: 600;
    color: var(--fb-green);
}
#<?php echo esc_attr($instance_id); ?> .fb-teachers {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    align-items: start;
    gap: 28px;
}
#<?php echo esc_attr($instance_id); ?> .fb-teacher {
    position: relative;
    background: var(--fb-surface);
    border: 1.5px solid var(--fb-line);
    border-radius: 36px 48px 36px 48px / 48px 36px 48px 36px;
    box-shadow: var(--fb-shadow-sm);
    padding: 28px;
    display: flex;
    gap: 18px;
    align-items: flex-start;
    overflow: hidden;
}
#<?php echo esc_attr($instance_id); ?> .fb-teacher:nth-child(even) {
    border-radius: 48px 36px 48px 36px / 36px 48px 36px 48px;
}
#<?php echo esc_attr($instance_id); ?> .fb-teacher-photo {
    width: 72px;
    height: 72px;
    border-radius: 60% 40% 55% 45% / 45% 55% 45% 55%;
    object-fit: cover;
    flex-shrink: 0;
    position: relative;
    z-index: 1;
}
#<?php echo esc_attr($instance_id); ?> .fb-teacher-name {
    font-family: "Fraunces", Georgia, serif;
    font-variation-settings: "opsz" 48;
    font-size: 18px;
    font-weight: 700;
    color: var(--fb-ink);
    margin: 4px 0 8px 0;
    position: relative;
    z-index: 1;
}
#<?php echo esc_attr($instance_id); ?> .fb-teacher-bio {
    font-size: 14px;
    line-height: 1.65;
    color: var(--fb-body);
    position: relative;
    z-index: 1;
}
#<?php echo esc_attr($instance_id); ?> .fb-teacher-themes {
    display: flex;
    flex-wrap: wrap;
    gap: 7px;
    margin-top: 14px;
    position: relative;
    z-index: 1;
}
#<?php echo esc_attr($instance_id); ?> .fb-teacher-theme {
    font-size: 11px;
    font-weight: 700;
    padding: 4px 12px;
    background: var(--fb-leaf-soft);
    color: var(--fb-green);
    border-radius: 9999px;
}

@media (max-width: 860px) {
    #<?php echo esc_attr($instance_id); ?> .fb-hero {
        grid-template-columns: 1fr;
        gap: 40px;
    }
    #<?php echo esc_attr($instance_id); ?> .fb-hero-media { order: -1; }
}
@media (max-width: 640px) {
    #<?php echo esc_attr($instance_id); ?> .fb-wrap { padding: 52px 20px; }
    #<?php echo esc_attr($instance_id); ?> .fb-body { margin-top: 48px; font-size: 16px; }
}
</style>

<div id="<?php echo esc_attr($instance_id); ?>">
    <div class="fb-wrap">

        <div class="fb-hero">
            <div class="fb-hero-info">
                <?php if (!empty($subject)): ?>
                    <p class="fb-label">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 2C8 6 8 12 12 13C16 12 16 6 12 2Z" fill="#91d66b"/>
                            <path d="M12 13V22" stroke="#4e7847" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        <?php echo esc_html($subject); ?>
                    </p>
                <?php endif; ?>

                <h1 class="fb-title"><?php echo esc_html($title); ?></h1>

                <?php if ($price_num > 0 || $total_num > 0): ?>
                <div class="fb-price">
                    <div class="fb-price-label">Investering</div>
                    <div class="fb-price-amount">&euro;<?php echo esc_html($price); ?></div>
                    <div class="fb-price-vat"><?php echo $incl_vat ? 'incl. btw' : 'excl. btw'; ?></div>
                    <?php if (!empty($total_price) && $total_price !== $price): ?>
                        <div class="fb-price-total">Totaal incl. materiaal: <strong>&euro;<?php echo esc_html($total_price); ?></strong></div>
                    <?php endif; ?>
                    <?php if ($discount_perc > 0): ?>
                        <div class="fb-discount"><?php echo esc_html($discount_name ?: $discount_perc . '% korting'); ?></div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <div>
                    <a href="<?php echo esc_url($link); ?>" class="fb-btn">
                        Inschrijven
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                        </svg>
                    </a>
                </div>
            </div>

            <?php if (!empty($image)): ?>
            <div class="fb-hero-media">
                <div class="fb-img-frame">
                    <img class="fb-img" src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>">
                </div>
            </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($description)): ?>
            <div class="fb-divider" aria-hidden="true">
                <svg preserveAspectRatio="none" viewBox="0 0 1200 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0 11 Q 50 0, 100 11 T 200 11 T 300 11 T 400 11 T 500 11 T 600 11 T 700 11 T 800 11 T 900 11 T 1000 11 T 1100 11 T 1200 11" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"/>
                </svg>
            </div>
            <div class="fb-body"><?php echo wp_kses_post($description); ?></div>
        <?php endif; ?>

        <?php if (!empty($teachers)): ?>
            <h2 class="fb-section-title">Onze <em>trainers</em></h2>
            <div class="fb-teachers">
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
                    <div class="fb-teacher">
                        <?php if (!empty($photo) && strpos($photo, 'gravatar') === false): ?>
                            <img class="fb-teacher-photo" src="<?php echo esc_url($photo); ?>" alt="<?php echo esc_attr($teacher['full_name'] ?? ''); ?>">
                        <?php endif; ?>
                        <div>
                            <div class="fb-teacher-name"><?php echo esc_html($teacher['full_name'] ?? ''); ?></div>
                            <?php if (!empty($bio)): ?>
                                <div class="fb-teacher-bio"><?php echo esc_html($bio); ?></div>
                            <?php endif; ?>
                            <?php if (!empty($teacher['themes'])): ?>
                                <div class="fb-teacher-themes">
                                    <?php foreach ($teacher['themes'] as $theme): ?>
                                        <span class="fb-teacher-theme"><?php echo esc_html($theme); ?></span>
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
