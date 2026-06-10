<?php
defined('ABSPATH') or die('No script kiddies please!');

$instance_id = 'mentor-detail-' . wp_unique_id();

if (empty($course) || !is_array($course)) {
    echo '<p>Cursus niet gevonden.</p>';
    return;
}

$title = $course['title'] ?? '';
$description = $course['description'] ?? '';
$image = $course['image'] ?? ($course['image_card_medium'] ?? '');
$price = $course['price'] ?? '';
$total_price = $course['total_price'] ?? '';
$subject = $course['subject']['title'] ?? '';
$link = $course['link_to_mentor'] ?? '#';
$has_physical = $course['has_physical_lesson'] ?? false;
$discount = $course['discount'] ?? [];
$discount_perc = $discount['discount_perc'] ?? 0;
$discount_name = $discount['discount_name'] ?? '';
$teachers = $course['teachers'] ?? [];
?>

<style>
#<?php echo esc_attr($instance_id); ?> .mcd-hero {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
    margin-bottom: 40px;
    align-items: start;
}
@media (max-width: 768px) {
    #<?php echo esc_attr($instance_id); ?> .mcd-hero {
        grid-template-columns: 1fr;
    }
}
#<?php echo esc_attr($instance_id); ?> .mcd-img {
    width: 100%;
    border-radius: 16px;
    object-fit: cover;
    max-height: 400px;
}
#<?php echo esc_attr($instance_id); ?> .mcd-subject {
    font-size: 13px;
    font-weight: 600;
    color: var(--color-primary, #417AB3);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
}
#<?php echo esc_attr($instance_id); ?> .mcd-title {
    font-size: 2rem;
    font-weight: 800;
    color: var(--color-body-text, #1f2937);
    margin: 0 0 20px 0;
    line-height: 1.2;
}
#<?php echo esc_attr($instance_id); ?> .mcd-description {
    font-size: 15px;
    line-height: 1.7;
    color: #4b5563;
    margin-bottom: 24px;
}
#<?php echo esc_attr($instance_id); ?> .mcd-description p {
    margin: 0 0 12px 0;
}
#<?php echo esc_attr($instance_id); ?> .mcd-cta-row {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 20px;
    margin-bottom: 24px;
}
#<?php echo esc_attr($instance_id); ?> .mcd-price-block {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 16px 20px;
    margin-bottom: 24px;
    display: inline-block;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
}
#<?php echo esc_attr($instance_id); ?> .mcd-cta-row .mcd-price-block {
    margin-bottom: 0;
}
#<?php echo esc_attr($instance_id); ?> .mcd-price-label {
    font-size: 12px;
    color: #9ca3af;
    margin-bottom: 4px;
}
#<?php echo esc_attr($instance_id); ?> .mcd-price {
    font-size: 1.35rem;
    font-weight: 700;
    color: var(--color-body-text, #1f2937);
}
#<?php echo esc_attr($instance_id); ?> .mcd-price-vat {
    font-size: 12px;
    color: #9ca3af;
    margin-top: 2px;
}
#<?php echo esc_attr($instance_id); ?> .mcd-price-total {
    margin-top: 10px;
    padding-top: 10px;
    border-top: 1px solid #e5e7eb;
    font-size: 12px;
    color: #9ca3af;
}
#<?php echo esc_attr($instance_id); ?> .mcd-price-total strong {
    color: var(--color-body-text, #1f2937);
}
#<?php echo esc_attr($instance_id); ?> .mcd-discount {
    display: inline-block;
    background: #dcfce7;
    color: #166534;
    font-size: 12px;
    font-weight: 600;
    padding: 3px 10px;
    border-radius: 9999px;
    margin-top: 8px;
}
#<?php echo esc_attr($instance_id); ?> .mcd-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 14px 32px;
    border-radius: 9999px;
    font-size: 15px;
    font-weight: 700;
    color: #fff;
    text-decoration: none;
    background-color: var(--color-primary, #417AB3);
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.12);
    transition: opacity 0.2s, box-shadow 0.2s, transform 0.2s;
}
@media (hover: hover) and (pointer: fine) {
    #<?php echo esc_attr($instance_id); ?> .mcd-btn:hover {
        opacity: 0.9;
        transform: translateY(-1px);
        box-shadow: 0 8px 22px rgba(0, 0, 0, 0.18);
    }
    #<?php echo esc_attr($instance_id); ?> .mcd-btn:hover svg {
        transform: translateX(4px);
    }
}
#<?php echo esc_attr($instance_id); ?> .mcd-btn:active {
    opacity: 0.85;
    transform: scale(0.98);
}
#<?php echo esc_attr($instance_id); ?> .mcd-btn svg {
    width: 18px;
    height: 18px;
    margin-left: 10px;
    transition: transform 0.2s ease;
}
#<?php echo esc_attr($instance_id); ?> .mcd-cta-actions {
    display: flex;
    flex-direction: column;
    gap: 10px;
    align-items: stretch;
}
#<?php echo esc_attr($instance_id); ?> .mcd-btn-website {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 12px 28px;
    border-radius: 9999px;
    font-size: 14px;
    font-weight: 700;
    text-decoration: none;
    color: var(--color-primary, #417AB3);
    background: #fff;
    border: 1.5px solid var(--color-primary, #417AB3);
    transition: background 0.2s, color 0.2s, box-shadow 0.2s, transform 0.2s;
}
#<?php echo esc_attr($instance_id); ?> .mcd-btn-website svg {
    width: 16px;
    height: 16px;
    margin-left: 8px;
    transition: transform 0.2s ease;
}
@media (hover: hover) and (pointer: fine) {
    #<?php echo esc_attr($instance_id); ?> .mcd-btn-website:hover {
        background: var(--color-primary, #417AB3);
        color: #fff;
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.14);
    }
    #<?php echo esc_attr($instance_id); ?> .mcd-btn-website:hover svg {
        transform: translateX(4px);
    }
}
#<?php echo esc_attr($instance_id); ?> .mcd-btn-website:active {
    transform: scale(0.98);
}
#<?php echo esc_attr($instance_id); ?> .mcd-btn-website--disabled {
    color: #9ca3af;
    background: #f3f4f6;
    border-color: #e5e7eb;
    cursor: not-allowed;
    pointer-events: none;
}
#<?php echo esc_attr($instance_id); ?> .mcd-section-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--color-body-text, #1f2937);
    margin: 48px 0 24px 0;
}
#<?php echo esc_attr($instance_id); ?> .mcd-teacher-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
}
#<?php echo esc_attr($instance_id); ?> .mcd-teacher-card {
    background: #fff;
    border-radius: 16px;
    border: 1px solid #e5e7eb;
    padding: 24px;
    display: flex;
    gap: 16px;
    align-items: flex-start;
}
#<?php echo esc_attr($instance_id); ?> .mcd-teacher-photo {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    object-fit: cover;
    flex-shrink: 0;
}
#<?php echo esc_attr($instance_id); ?> .mcd-teacher-name {
    font-size: 16px;
    font-weight: 700;
    color: var(--color-body-text, #1f2937);
    margin: 0 0 4px 0;
}
#<?php echo esc_attr($instance_id); ?> .mcd-teacher-themes {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    margin-top: 8px;
}
#<?php echo esc_attr($instance_id); ?> .mcd-teacher-theme {
    font-size: 11px;
    font-weight: 600;
    padding: 3px 8px;
    border-radius: 9999px;
    background: #f3f4f6;
    color: #6b7280;
}
#<?php echo esc_attr($instance_id); ?> .mcd-teacher-bio {
    font-size: 13px;
    color: #6b7280;
    margin-top: 6px;
    line-height: 1.5;
}

/* Mobile: smaller headings, tighter padding */
@media (max-width: 640px) {
    #<?php echo esc_attr($instance_id); ?> > div {
        padding: 24px 0;
    }
    #<?php echo esc_attr($instance_id); ?> .mcd-title {
        font-size: 1.5rem;
    }
    #<?php echo esc_attr($instance_id); ?> .mcd-section-title {
        font-size: 1.25rem;
        margin: 32px 0 16px 0;
    }
}
</style>

<div class="tailwind-scope tw container mx-auto px-4" id="<?php echo esc_attr($instance_id); ?>">
    <div style="padding: 40px 0;">

        <!-- Hero: afbeelding + info -->
        <div class="mcd-hero">
            <?php if (!empty($image)): ?>
                <img class="mcd-img" src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>">
            <?php endif; ?>

            <div>
                <?php if (!empty($subject)): ?>
                    <div class="mcd-subject"><?php echo esc_html($subject); ?></div>
                <?php endif; ?>

                <h1 class="mcd-title"><?php echo esc_html($title); ?></h1>

                <div class="mcd-cta-row">
                <!-- Prijs -->
                <?php
                $price_num = (float) str_replace(['.', ','], ['', '.'], $price);
                $total_num = (float) str_replace(['.', ','], ['', '.'], $total_price);
                if ($price_num > 0 || $total_num > 0):
                ?>
                <div class="mcd-price-block">
                    <div class="mcd-price-label">Cursusprijs</div>
                    <div class="mcd-price">&euro;<?php echo esc_html($price); ?></div>
                    <?php if ($course['show_prices_including_vat'] ?? false): ?>
                        <div class="mcd-price-vat">incl. btw</div>
                    <?php endif; ?>
                    <?php if (!empty($total_price) && $total_price !== $price): ?>
                        <div class="mcd-price-total">Totaal incl. materiaal: <strong>&euro;<?php echo esc_html($total_price); ?></strong></div>
                    <?php endif; ?>
                    <?php if ($discount_perc > 0): ?>
                        <div class="mcd-discount"><?php echo esc_html($discount_name ?: $discount_perc . '% korting'); ?></div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <div class="mcd-cta-actions">
                    <a href="<?php echo esc_url($link); ?>" class="mcd-btn">
                        Inschrijven
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                        </svg>
                    </a>

                    <?php $course_website = mentor_get_course_website($course); ?>
                    <?php if (!empty($course_website)): ?>
                        <a href="<?php echo esc_url($course_website); ?>" class="mcd-btn-website" target="_blank" rel="noopener">
                            Bekijk cursuswebsite
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                            </svg>
                        </a>
                    <?php else: ?>
                        <span class="mcd-btn-website mcd-btn-website--disabled" aria-disabled="true" title="Nog geen cursus-website ingesteld">
                            Bekijk cursuswebsite
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                            </svg>
                        </span>
                    <?php endif; ?>
                </div>
                </div>
            </div>
        </div>

        <!-- Beschrijving -->
        <?php if (!empty($description)): ?>
            <div class="mcd-description">
                <?php echo wp_kses_post($description); ?>
            </div>
        <?php endif; ?>

        <!-- Docenten -->
        <?php if (!empty($teachers)): ?>
            <h2 class="mcd-section-title">Docenten</h2>
            <div class="mcd-teacher-grid">
                <?php foreach ($teachers as $teacher):
                    $photo = $teacher['profile_picture_lg'] ?? $teacher['profile_picture'] ?? '';
                    if (!empty($photo) && strpos($photo, 'http') !== 0) {
                        $photo = rtrim($api_url, '/') . '/' . ltrim($photo, '/');
                        if (strpos($photo, '..') !== false) $photo = '';
                    }
                    $bio = wp_strip_all_tags($teacher['summary'] ?? '');
                    if (mb_strlen($bio) > 150) {
                        $bio = mb_substr($bio, 0, 150) . '...';
                    }
                ?>
                    <div class="mcd-teacher-card">
                        <?php if (!empty($photo) && strpos($photo, 'gravatar') === false): ?>
                            <img class="mcd-teacher-photo" src="<?php echo esc_url($photo); ?>" alt="<?php echo esc_attr($teacher['full_name'] ?? ''); ?>">
                        <?php endif; ?>
                        <div>
                            <div class="mcd-teacher-name"><?php echo esc_html($teacher['full_name'] ?? ''); ?></div>
                            <?php if (!empty($bio)): ?>
                                <div class="mcd-teacher-bio"><?php echo esc_html($bio); ?></div>
                            <?php endif; ?>
                            <?php if (!empty($teacher['themes'])): ?>
                                <div class="mcd-teacher-themes">
                                    <?php foreach ($teacher['themes'] as $theme): ?>
                                        <span class="mcd-teacher-theme"><?php echo esc_html($theme); ?></span>
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
