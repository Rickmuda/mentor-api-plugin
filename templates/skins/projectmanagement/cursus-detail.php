<?php
/**
 * Skin: leiderschap — Cursusdetailpagina ("Fris & professioneel", zodan-stijl)
 * Verwacht dezelfde variabelen als templates/cursus-detail.php:
 *   $course (array), $tracks, $api_url
 */
defined('ABSPATH') or die('No script kiddies please!');

if (empty($course) || !is_array($course)) {
    echo '<p>Cursus niet gevonden.</p>';
    return;
}

// Google Fonts: Plus Jakarta Sans (koppen) + Inter (body) — eenmalig.
if (!wp_style_is('mentor-leiderschap-fonts', 'enqueued')) {
    wp_enqueue_style(
        'mentor-leiderschap-fonts',
        'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap',
        array(),
        null
    );
}

$instance_id = 'lc-detail-' . wp_unique_id();

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
    --lc-bg: #F7F8FA;
    --lc-surface: #FFFFFF;
    --lc-ink: #16202C;
    --lc-body: #586575;
    --lc-blue: #D97706;
    --lc-blue-dark: #B45309;
    --lc-blue-soft: #FEF3C7;
    --lc-line: #E6EAF0;
    --lc-shadow: 0 18px 40px -20px rgba(22, 32, 44, 0.25);
    --lc-shadow-sm: 0 2px 6px rgba(22, 32, 44, 0.06);

    background: transparent;
    color: var(--lc-ink);
    font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    -webkit-font-smoothing: antialiased;
}
#<?php echo esc_attr($instance_id); ?> * { box-sizing: border-box; }
#<?php echo esc_attr($instance_id); ?> .lc-wrap {
    max-width: 100%;
    margin: 0;
    padding: 0;
}
#<?php echo esc_attr($instance_id); ?> .lc-label {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    font-weight: 600;
    letter-spacing: 0.02em;
    color: var(--lc-blue);
    margin: 0 0 18px 0;
}
#<?php echo esc_attr($instance_id); ?> .lc-label::before {
    content: "";
    width: 9px;
    height: 9px;
    border-radius: 50%;
    background: var(--lc-blue);
}
#<?php echo esc_attr($instance_id); ?> .lc-hero {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 56px;
    align-items: center;
}
#<?php echo esc_attr($instance_id); ?> .lc-title {
    font-family: "Plus Jakarta Sans", "Inter", sans-serif;
    font-size: clamp(2.4rem, 1.5rem + 3vw, 3.8rem);
    font-weight: 800;
    line-height: 1.05;
    letter-spacing: -0.02em;
    color: var(--lc-ink);
    margin: 0 0 28px 0;
}
#<?php echo esc_attr($instance_id); ?> .lc-img {
    width: 100%;
    aspect-ratio: 4 / 3;
    object-fit: cover;
    border-radius: 28px;
    box-shadow: var(--lc-shadow);
}
#<?php echo esc_attr($instance_id); ?> .lc-price {
    background: var(--lc-surface);
    border: 1px solid var(--lc-line);
    border-radius: 20px;
    box-shadow: var(--lc-shadow-sm);
    padding: 22px 26px;
    margin: 0 0 28px 0;
    display: inline-block;
    min-width: 220px;
}
#<?php echo esc_attr($instance_id); ?> .lc-price-label {
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: var(--lc-body);
    margin-bottom: 6px;
}
#<?php echo esc_attr($instance_id); ?> .lc-price-amount {
    font-family: "Plus Jakarta Sans", "Inter", sans-serif;
    font-size: 2.1rem;
    font-weight: 800;
    color: var(--lc-ink);
    line-height: 1.05;
}
#<?php echo esc_attr($instance_id); ?> .lc-price-vat {
    font-size: 13px;
    color: var(--lc-body);
    margin-top: 4px;
}
#<?php echo esc_attr($instance_id); ?> .lc-price-total {
    font-size: 13px;
    color: var(--lc-body);
    margin-top: 14px;
    padding-top: 14px;
    border-top: 1px solid var(--lc-line);
}
#<?php echo esc_attr($instance_id); ?> .lc-price-total strong { color: var(--lc-ink); }
#<?php echo esc_attr($instance_id); ?> .lc-discount {
    display: inline-block;
    margin-top: 14px;
    font-size: 12px;
    font-weight: 700;
    color: var(--lc-blue);
    background: var(--lc-blue-soft);
    padding: 5px 12px;
    border-radius: 9999px;
}
#<?php echo esc_attr($instance_id); ?> .lc-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 16px 32px;
    background: var(--lc-blue);
    color: #fff;
    font-size: 15px;
    font-weight: 700;
    text-decoration: none;
    border-radius: 9999px;
    box-shadow: 0 12px 24px -10px rgba(217, 119, 6, 0.55);
    transition: background 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
}
@media (hover: hover) and (pointer: fine) {
    #<?php echo esc_attr($instance_id); ?> .lc-btn:hover {
        background: var(--lc-blue-dark);
        transform: translateY(-1px);
        box-shadow: 0 16px 28px -10px rgba(217, 119, 6, 0.6);
    }
}
#<?php echo esc_attr($instance_id); ?> .lc-btn:active { transform: translateY(0); }
#<?php echo esc_attr($instance_id); ?> .lc-btn svg { width: 18px; height: 18px; }
#<?php echo esc_attr($instance_id); ?> .lc-body {
    max-width: 68ch;
    margin: 72px 0 0 0;
    font-size: 17px;
    line-height: 1.8;
    color: var(--lc-body);
}
#<?php echo esc_attr($instance_id); ?> .lc-body p { margin: 0 0 18px 0; }
#<?php echo esc_attr($instance_id); ?> .lc-body h2,
#<?php echo esc_attr($instance_id); ?> .lc-body h3 {
    font-family: "Plus Jakarta Sans", "Inter", sans-serif;
    color: var(--lc-ink);
    font-weight: 700;
}
#<?php echo esc_attr($instance_id); ?> .lc-section-title {
    font-family: "Plus Jakarta Sans", "Inter", sans-serif;
    font-size: 1.9rem;
    font-weight: 800;
    letter-spacing: -0.01em;
    color: var(--lc-ink);
    margin: 80px 0 28px 0;
}
#<?php echo esc_attr($instance_id); ?> .lc-teachers {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 24px;
}
#<?php echo esc_attr($instance_id); ?> .lc-teacher {
    background: var(--lc-surface);
    border: 1px solid var(--lc-line);
    border-radius: 20px;
    box-shadow: var(--lc-shadow-sm);
    padding: 28px;
    display: flex;
    gap: 18px;
    align-items: flex-start;
}
#<?php echo esc_attr($instance_id); ?> .lc-teacher-photo {
    width: 68px;
    height: 68px;
    border-radius: 50%;
    object-fit: cover;
    flex-shrink: 0;
}
#<?php echo esc_attr($instance_id); ?> .lc-teacher-name {
    font-family: "Plus Jakarta Sans", "Inter", sans-serif;
    font-size: 17px;
    font-weight: 700;
    color: var(--lc-ink);
    margin: 2px 0 6px 0;
}
#<?php echo esc_attr($instance_id); ?> .lc-teacher-bio {
    font-size: 14px;
    line-height: 1.6;
    color: var(--lc-body);
}
#<?php echo esc_attr($instance_id); ?> .lc-teacher-themes {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: 12px;
}
#<?php echo esc_attr($instance_id); ?> .lc-teacher-theme {
    font-size: 11px;
    font-weight: 600;
    padding: 4px 11px;
    background: var(--lc-blue-soft);
    color: var(--lc-blue);
    border-radius: 9999px;
}

@media (max-width: 860px) {
    #<?php echo esc_attr($instance_id); ?> .lc-hero {
        grid-template-columns: 1fr;
        gap: 36px;
    }
    #<?php echo esc_attr($instance_id); ?> .lc-hero-media { order: -1; }
}
@media (max-width: 640px) {
    #<?php echo esc_attr($instance_id); ?> .lc-wrap { padding: 52px 20px; }
    #<?php echo esc_attr($instance_id); ?> .lc-body { margin-top: 48px; font-size: 16px; }
    #<?php echo esc_attr($instance_id); ?> .lc-img { border-radius: 22px; }
}
</style>

<div id="<?php echo esc_attr($instance_id); ?>">
    <div class="lc-wrap">

        <div class="lc-hero">
            <div class="lc-hero-info">
                <?php if (!empty($subject)): ?>
                    <p class="lc-label"><?php echo esc_html($subject); ?></p>
                <?php endif; ?>

                <h1 class="lc-title"><?php echo esc_html($title); ?></h1>

                <?php if ($price_num > 0 || $total_num > 0): ?>
                <div class="lc-price">
                    <div class="lc-price-label">Investering</div>
                    <div class="lc-price-amount">&euro;<?php echo esc_html($price); ?></div>
                    <div class="lc-price-vat"><?php echo $incl_vat ? 'incl. btw' : 'excl. btw'; ?></div>
                    <?php if (!empty($total_price) && $total_price !== $price): ?>
                        <div class="lc-price-total">Totaal incl. materiaal: <strong>&euro;<?php echo esc_html($total_price); ?></strong></div>
                    <?php endif; ?>
                    <?php if ($discount_perc > 0): ?>
                        <div class="lc-discount"><?php echo esc_html($discount_name ?: $discount_perc . '% korting'); ?></div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <div>
                    <a href="<?php echo esc_url($link); ?>" class="lc-btn">
                        Inschrijven
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                        </svg>
                    </a>
                </div>
            </div>

            <?php if (!empty($image)): ?>
            <div class="lc-hero-media">
                <img class="lc-img" src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>">
            </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($description)): ?>
            <div class="lc-body"><?php echo wp_kses_post($description); ?></div>
        <?php endif; ?>

        <?php if (!empty($teachers)): ?>
            <h2 class="lc-section-title">Docenten</h2>
            <div class="lc-teachers">
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
                    <div class="lc-teacher">
                        <?php if (!empty($photo) && strpos($photo, 'gravatar') === false): ?>
                            <img class="lc-teacher-photo" src="<?php echo esc_url($photo); ?>" alt="<?php echo esc_attr($teacher['full_name'] ?? ''); ?>">
                        <?php endif; ?>
                        <div>
                            <div class="lc-teacher-name"><?php echo esc_html($teacher['full_name'] ?? ''); ?></div>
                            <?php if (!empty($bio)): ?>
                                <div class="lc-teacher-bio"><?php echo esc_html($bio); ?></div>
                            <?php endif; ?>
                            <?php if (!empty($teacher['themes'])): ?>
                                <div class="lc-teacher-themes">
                                    <?php foreach ($teacher['themes'] as $theme): ?>
                                        <span class="lc-teacher-theme"><?php echo esc_html($theme); ?></span>
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
