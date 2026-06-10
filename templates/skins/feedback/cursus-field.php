<?php
/**
 * Skin: feedback - Losse cursusvelden ("Organisch & groei", feedback-thema)
 * Verwacht: $course (array), $api_url (string), $mentor_field (titel|prijs|omschrijving|afbeelding|thema|inschrijven)
 * Wordt geladen vanuit MentorShortcodes::display_cursus_field() wanneer skin "feedback" actief is.
 */
defined('ABSPATH') or die('No script kiddies please!');

if (!wp_style_is('mentor-feedback-fonts', 'enqueued')) {
    wp_enqueue_style(
        'mentor-feedback-fonts',
        'https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700;9..144,800&family=Inter:wght@400;500;600;700&display=swap',
        array(),
        null
    );
}

// Fontnaam-quotes als HTML-entiteit, zodat ze het double-quoted style="..."-attribuut
// niet voortijdig afbreken (anders valt alles na font-family weg).
$fb_head = '&quot;Fraunces&quot;, Georgia, serif';
$fb_body = '&quot;Inter&quot;, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, sans-serif';

switch ($mentor_field) {

    case 'titel':
        echo '<h1 style="font-family:' . $fb_head . ';font-optical-sizing:auto;font-variation-settings:\'opsz\' 144;font-size:clamp(2.2rem,1.4rem+2.6vw,3.4rem);font-weight:700;letter-spacing:-0.02em;line-height:1.05;color:#1E2D1E;margin:0;">'
            . esc_html($course['title'] ?? '') . '</h1>';
        break;

    case 'thema':
        $subject = $course['subject']['title'] ?? '';
        if ($subject === '') break;
        echo '<span style="display:inline-flex;align-items:center;gap:10px;font-family:' . $fb_body . ';font-size:13px;font-weight:700;letter-spacing:0.04em;text-transform:uppercase;color:#4e7847;">'
            . '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">'
            . '<path d="M12 2C8 6 8 12 12 13C16 12 16 6 12 2Z" fill="#91d66b"/>'
            . '<path d="M12 13V22" stroke="#4e7847" stroke-width="2" stroke-linecap="round"/>'
            . '</svg>'
            . esc_html($subject) . '</span>';
        break;

    case 'afbeelding':
        $img = $course['image'] ?? ($course['image_card_medium'] ?? '');
        if (empty($img)) break;
        echo '<div style="position:relative;width:100%;aspect-ratio:4/4.4;">'
            . '<span aria-hidden="true" style="position:absolute;inset:-28px -22px -10px -10px;background:#91d66b;border-radius:56% 44% 62% 38% / 50% 58% 42% 50%;opacity:0.55;z-index:0;"></span>'
            . '<span aria-hidden="true" style="position:absolute;inset:18px -32px -22px 22px;background:#4e7847;border-radius:48% 52% 36% 64% / 60% 40% 60% 40%;opacity:0.16;z-index:0;"></span>'
            . '<img src="' . esc_url($img) . '" alt="' . esc_attr($course['title'] ?? '') . '" '
            . 'style="position:relative;z-index:1;width:100%;height:100%;object-fit:cover;border-radius:56% 44% 62% 38% / 50% 58% 42% 50%;box-shadow:0 22px 50px -22px rgba(30,45,30,0.30);display:block;">'
            . '</div>';
        break;

    case 'omschrijving':
        echo '<div style="font-family:' . $fb_body . ';font-size:17px;line-height:1.85;color:#5B6A5B;">'
            . wp_kses_post($course['description'] ?? '') . '</div>';
        break;

    case 'prijs':
        $incl_vat = !empty($course['show_prices_including_vat']);
        $price = $course['price'] ?? '';
        $total = $course['total_price'] ?? '';
        $discount = $course['discount'] ?? [];
        $disc_perc = $discount['discount_perc'] ?? 0;
        $disc_name = $discount['discount_name'] ?? '';

        $price_num = (float) str_replace(['.', ','], ['', '.'], (string) $price);
        $total_num = (float) str_replace(['.', ','], ['', '.'], (string) $total);
        if ($price_num <= 0 && $total_num <= 0) break;

        $h  = '<div style="position:relative;overflow:hidden;font-family:' . $fb_body . ';background:#fff;border:1.5px solid #E5E8DE;border-radius:28px 38px 28px 38px / 38px 28px 38px 28px;box-shadow:0 4px 14px -6px rgba(78,120,71,0.18);padding:24px 28px;display:inline-block;min-width:240px;">';
        $h .= '<span aria-hidden="true" style="position:absolute;bottom:-24px;right:-24px;width:80px;height:80px;background:#E5F2D6;border-radius:50% 30% 60% 40% / 40% 60% 30% 50%;opacity:0.7;"></span>';
        $h .= '<div style="position:relative;font-size:11px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#5B6A5B;margin-bottom:6px;">Investering</div>';
        $h .= '<div style="position:relative;font-family:' . $fb_head . ';font-variation-settings:\'opsz\' 96;font-size:2.3rem;font-weight:700;color:#1E2D1E;line-height:1;letter-spacing:-0.02em;">&euro;' . esc_html($price) . '</div>';
        $h .= '<div style="position:relative;font-size:13px;color:#5B6A5B;margin-top:6px;">' . ($incl_vat ? 'incl. btw' : 'excl. btw') . '</div>';
        if (!empty($total) && $total !== $price) {
            $h .= '<div style="position:relative;font-size:13px;color:#5B6A5B;margin-top:16px;padding-top:16px;border-top:1px solid #E5E8DE;">Totaal incl. materiaal: <strong style="color:#1E2D1E;">&euro;' . esc_html($total) . '</strong></div>';
        }
        if ($disc_perc > 0) {
            $h .= '<div style="position:relative;margin-top:14px;"><span style="display:inline-block;background:#E5F2D6;color:#4e7847;font-size:12px;font-weight:700;padding:5px 13px;border-radius:9999px;">' . esc_html($disc_name ?: $disc_perc . '% korting') . '</span></div>';
        }
        $h .= '</div>';
        echo $h; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- alle dynamische waarden hierboven via esc_html()
        break;

    case 'inschrijven':
        // Maat-matchen met .fb-btn uit feedback-site.php zodat hero-Inschrijven en de
        // ghost-knop "Hoe het werkt" als paar uitlijnen.
        $link = add_query_arg('startenrolment', '1', $course['link_to_mentor'] ?? '#');
        echo '<a href="' . esc_url($link) . '" '
            . 'style="display:inline-flex;align-items:center;gap:10px;font-family:' . $fb_body . ';padding:14px 28px;border-radius:9999px;font-size:15px;font-weight:700;color:#fff;text-decoration:none;background:#4e7847;box-shadow:0 14px 26px -12px rgba(78,120,71,0.55);transition:background .15s ease,transform .15s ease,box-shadow .15s ease;" '
            . 'onmouseover="this.style.background=\'#3A5C35\';this.style.transform=\'translateY(-1px)\';this.style.boxShadow=\'0 18px 30px -10px rgba(78,120,71,0.65)\'" '
            . 'onmouseout="this.style.background=\'#4e7847\';this.style.transform=\'\';this.style.boxShadow=\'0 14px 26px -12px rgba(78,120,71,0.55)\'">'
            . 'Inschrijven '
            . '<svg style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.4"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg></a>';
        break;
}
