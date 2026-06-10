<?php
/**
 * Skin: time-management - Cursusdetailpagina ("Calm focus", planner-thema)
 * Verwacht dezelfde variabelen als templates/cursus-detail.php:
 *   $course (array), $tracks, $api_url
 *
 * Self-contained: gebruikt eigen styles zodat de skin werkt zonder de time-management
 * mu-plugin (bv. wanneer een andere site skin=time-management activeert).
 */
defined('ABSPATH') or die('No script kiddies please!');

if (empty($course) || !is_array($course)) {
    echo '<p>Cursus niet gevonden.</p>';
    return;
}

if (!wp_style_is('mentor-time-management-fonts', 'enqueued')) {
    wp_enqueue_style(
        'mentor-time-management-fonts',
        'https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;600;700&display=swap',
        array(),
        null
    );
}

$instance_id = 'tm-detail-' . wp_unique_id();

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
$enrol_link = add_query_arg('startenrolment', '1', $link);
?>

<style>
#<?php echo esc_attr($instance_id); ?> {
    --tm-ink: #1A1F36;
    --tm-body: #525A75;
    --tm-muted: #8A8F9F;
    --tm-indigo: #2D3FB5;
    --tm-indigo-dark: #1F2C8A;
    --tm-indigo-soft: #E5E8FA;
    --tm-amber: #E8930C;
    --tm-amber-soft: #FBE9C8;
    --tm-line: #E7E2D5;
    --tm-line-soft: #F0EBDF;
    --tm-surface: #FFFFFF;
    --tm-shadow: 0 22px 50px -22px rgba(26, 31, 54, 0.30);

    color: var(--tm-ink);
    font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    -webkit-font-smoothing: antialiased;
}
#<?php echo esc_attr($instance_id); ?> * { box-sizing: border-box; }

#<?php echo esc_attr($instance_id); ?> .tmd-hero {
    display: grid; grid-template-columns: 1.05fr 0.95fr;
    gap: clamp(36px, 5vw, 64px); align-items: center;
    padding: clamp(48px, 6vw, 80px) 0;
}
@media (max-width: 900px) {
    #<?php echo esc_attr($instance_id); ?> .tmd-hero { grid-template-columns: 1fr; }
}
#<?php echo esc_attr($instance_id); ?> .tmd-eyebrow {
    display: inline-flex; align-items: center; gap: 12px;
    font-family: "JetBrains Mono", ui-monospace, SFMono-Regular, monospace;
    font-size: 12px; font-weight: 600; letter-spacing: 0.06em; text-transform: uppercase;
    color: var(--tm-indigo); margin-bottom: 20px;
}
#<?php echo esc_attr($instance_id); ?> .tmd-eyebrow::before {
    content: ""; width: 24px; height: 2px; background: var(--tm-indigo);
}
#<?php echo esc_attr($instance_id); ?> .tmd-title {
    font-family: "Outfit", sans-serif; font-weight: 700;
    font-size: clamp(2rem, 1.4rem + 2.4vw, 3.2rem); line-height: 1.05;
    letter-spacing: -0.03em; color: var(--tm-ink); margin: 0 0 20px;
}
#<?php echo esc_attr($instance_id); ?> .tmd-desc {
    font-size: 1.05rem; line-height: 1.75; color: var(--tm-body); margin: 0 0 26px;
}
#<?php echo esc_attr($instance_id); ?> .tmd-actions { display: flex; flex-wrap: wrap; gap: 14px; align-items: center; }

#<?php echo esc_attr($instance_id); ?> .tmd-image-wrap { position: relative; }
#<?php echo esc_attr($instance_id); ?> .tmd-image-wrap::before {
    content: ""; position: absolute; inset: -18px -14px -8px 14px;
    background: var(--tm-amber-soft); border-radius: 22px; z-index: 0; opacity: 0.7;
}
#<?php echo esc_attr($instance_id); ?> .tmd-image {
    position: relative; z-index: 1; width: 100%; aspect-ratio: 4/3; object-fit: cover;
    border-radius: 22px; box-shadow: var(--tm-shadow); display: block;
}
#<?php echo esc_attr($instance_id); ?> .tmd-image-ph {
    position: relative; z-index: 1; width: 100%; aspect-ratio: 4/3;
    border-radius: 22px; background: linear-gradient(135deg, var(--tm-indigo-soft) 0%, #C7CCEC 100%);
    display: flex; align-items: center; justify-content: center;
    color: var(--tm-indigo); font-family: "Outfit", sans-serif; font-size: 3rem; font-weight: 700;
}

#<?php echo esc_attr($instance_id); ?> .tmd-price {
    background: var(--tm-surface); border: 1.5px solid var(--tm-line);
    border-radius: 18px; box-shadow: 0 4px 14px -6px rgba(26,31,54,0.10);
    padding: 22px 26px; display: inline-block; min-width: 220px;
}
#<?php echo esc_attr($instance_id); ?> .tmd-price-label {
    font-family: "JetBrains Mono", monospace; font-size: 11px; font-weight: 600;
    letter-spacing: 0.08em; text-transform: uppercase; color: var(--tm-body);
    margin-bottom: 8px;
}
#<?php echo esc_attr($instance_id); ?> .tmd-price-num {
    font-family: "Outfit", sans-serif; font-size: 2rem; font-weight: 700;
    color: var(--tm-ink); line-height: 1; letter-spacing: -0.02em;
}
#<?php echo esc_attr($instance_id); ?> .tmd-price-vat {
    font-size: 13px; color: var(--tm-body); margin-top: 6px;
}
#<?php echo esc_attr($instance_id); ?> .tmd-price-total {
    font-size: 13px; color: var(--tm-body); margin-top: 14px; padding-top: 14px;
    border-top: 1px solid var(--tm-line);
}
#<?php echo esc_attr($instance_id); ?> .tmd-price-total strong { color: var(--tm-ink); }
#<?php echo esc_attr($instance_id); ?> .tmd-discount {
    display: inline-block; margin-top: 12px;
    background: var(--tm-amber-soft); color: #B5720A;
    font-family: "JetBrains Mono", monospace; font-size: 11px; font-weight: 600;
    padding: 5px 11px; border-radius: 6px; letter-spacing: 0.04em;
}

#<?php echo esc_attr($instance_id); ?> .tmd-btn {
    display: inline-flex; align-items: center; gap: 10px;
    padding: 13px 26px; border-radius: 9999px;
    background: var(--tm-indigo); color: #fff !important;
    font-size: 15px; font-weight: 700; text-decoration: none;
    box-shadow: 0 14px 26px -12px rgba(45,63,181,0.55);
    transition: background .15s, transform .15s;
}
#<?php echo esc_attr($instance_id); ?> .tmd-btn:hover { background: var(--tm-indigo-dark); transform: translateY(-1px); }
#<?php echo esc_attr($instance_id); ?> .tmd-btn svg { width: 16px; height: 16px; }

#<?php echo esc_attr($instance_id); ?> .tmd-subject {
    display: inline-block;
    font-family: "JetBrains Mono", monospace; font-size: 12px; font-weight: 600;
    color: var(--tm-amber); letter-spacing: 0.06em; text-transform: uppercase;
    margin-bottom: 12px;
}
</style>

<div id="<?php echo esc_attr($instance_id); ?>">
    <div class="tmd-hero">
        <div class="tmd-content">
            <span class="tmd-eyebrow">Training</span>
            <?php if (!empty($subject)): ?>
                <div class="tmd-subject">// <?php echo esc_html($subject); ?></div>
            <?php endif; ?>
            <h1 class="tmd-title"><?php echo esc_html($title); ?></h1>
            <?php if (!empty($description)): ?>
                <div class="tmd-desc"><?php echo wp_kses_post($description); ?></div>
            <?php endif; ?>

            <?php if ($price_num > 0 || $total_num > 0): ?>
            <div class="tmd-price">
                <div class="tmd-price-label">Investering</div>
                <div class="tmd-price-num">&euro;<?php echo esc_html($price); ?></div>
                <div class="tmd-price-vat"><?php echo $incl_vat ? 'incl. btw' : 'excl. btw'; ?></div>
                <?php if (!empty($total_price) && $total_price !== $price): ?>
                    <div class="tmd-price-total">Totaal incl. materiaal: <strong>&euro;<?php echo esc_html($total_price); ?></strong></div>
                <?php endif; ?>
                <?php if ($discount_perc > 0): ?>
                    <div><span class="tmd-discount"><?php echo esc_html($discount_name ?: $discount_perc . '% korting'); ?></span></div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <div class="tmd-actions" style="margin-top: 26px;">
                <a class="tmd-btn" href="<?php echo esc_url($enrol_link); ?>">
                    Inschrijven
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                    </svg>
                </a>
            </div>
        </div>
        <div class="tmd-image-wrap">
            <?php if (!empty($image)): ?>
                <img class="tmd-image" src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>">
            <?php else: ?>
                <div class="tmd-image-ph" aria-hidden="true">TM</div>
            <?php endif; ?>
        </div>
    </div>
</div>
