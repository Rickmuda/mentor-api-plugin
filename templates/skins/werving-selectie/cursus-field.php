<?php
/**
 * Skin: werving-selectie - Losse cursusvelden ("Decisive hiring", executive/beslis-thema)
 * Verwacht: $course (array), $api_url (string), $mentor_field (titel|prijs|omschrijving|afbeelding|thema|inschrijven)
 * Wordt geladen vanuit MentorShortcodes::display_cursus_field() wanneer skin "werving-selectie" actief is.
 */
defined('ABSPATH') or die('No script kiddies please!');

if (!wp_style_is('mentor-werving-selectie-fonts', 'enqueued')) {
    wp_enqueue_style(
        'mentor-werving-selectie-fonts',
        'https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600&display=swap',
        array(),
        null
    );
}

// Fontnaam-quotes als HTML-entiteit, zodat ze het double-quoted style="..."-attribuut
// niet voortijdig afbreken (anders valt alles na font-family weg).
$ws_head = '&quot;Space Grotesk&quot;, &quot;Segoe UI&quot;, sans-serif';
$ws_body = '&quot;Inter&quot;, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, sans-serif';

switch ($mentor_field) {

    case 'titel':
        echo '<h1 style="font-family:' . $ws_head . ';font-size:clamp(2.2rem,1.4rem+2.6vw,3.4rem);font-weight:700;letter-spacing:-0.02em;line-height:1.05;color:#14181F;margin:0;">'
            . esc_html($course['title'] ?? '') . '</h1>';
        break;

    case 'thema':
        $subject = $course['subject']['title'] ?? '';
        if ($subject === '') break;
        echo '<span style="display:inline-flex;align-items:center;gap:10px;font-family:' . $ws_body . ';font-size:12px;font-weight:600;letter-spacing:0.16em;text-transform:uppercase;color:#FF4F36;">'
            . '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true">'
            . '<path d="M5 12h12m0 0l-5-5m5 5l-5 5" stroke="#FF4F36" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>'
            . '</svg>'
            . esc_html($subject) . '</span>';
        break;

    case 'afbeelding':
        $img = $course['image'] ?? ($course['image_card_medium'] ?? '');
        if (empty($img)) break;
        echo '<div style="position:relative;width:100%;aspect-ratio:4/4.4;">'
            . '<span aria-hidden="true" style="position:absolute;left:-18px;bottom:-18px;width:44%;height:44%;background:#FF4F36;border-radius:8px;opacity:0.16;z-index:0;"></span>'
            . '<span aria-hidden="true" style="position:absolute;top:-16px;right:-16px;width:50%;height:50%;border:1px solid #FF4F36;border-radius:8px;opacity:0.5;z-index:0;"></span>'
            . '<img src="' . esc_url($img) . '" alt="' . esc_attr($course['title'] ?? '') . '" '
            . 'style="position:relative;z-index:1;width:100%;height:100%;object-fit:cover;border-radius:8px;box-shadow:0 24px 50px -28px rgba(20,24,31,0.28);display:block;">'
            . '</div>';
        break;

    case 'omschrijving':
        echo '<div style="font-family:' . $ws_body . ';font-size:17px;line-height:1.85;color:#5A5E66;">'
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

        $h  = '<div style="position:relative;overflow:hidden;font-family:' . $ws_body . ';background:#fff;border:1px solid #E2DED6;border-left:4px solid #FF4F36;border-radius:8px;box-shadow:0 6px 18px -10px rgba(20,24,31,0.18);padding:24px 28px;display:inline-block;min-width:240px;">';
        $h .= '<div style="position:relative;font-size:11px;font-weight:600;letter-spacing:0.16em;text-transform:uppercase;color:#5A5E66;margin-bottom:6px;">Investering</div>';
        $h .= '<div style="position:relative;font-family:' . $ws_head . ';font-size:2.3rem;font-weight:700;color:#14181F;line-height:1;letter-spacing:-0.02em;">&euro;' . esc_html($price) . '</div>';
        $h .= '<div style="position:relative;font-size:13px;color:#5A5E66;margin-top:6px;">' . ($incl_vat ? 'incl. btw' : 'excl. btw') . '</div>';
        if (!empty($total) && $total !== $price) {
            $h .= '<div style="position:relative;font-size:13px;color:#5A5E66;margin-top:16px;padding-top:16px;border-top:1px solid #E2DED6;">Totaal incl. materiaal: <strong style="color:#14181F;">&euro;' . esc_html($total) . '</strong></div>';
        }
        if ($disc_perc > 0) {
            $h .= '<div style="position:relative;margin-top:14px;"><span style="display:inline-block;background:#FCE4DF;color:#E23A22;font-size:12px;font-weight:600;padding:5px 13px;border-radius:4px;">' . esc_html($disc_name ?: $disc_perc . '% korting') . '</span></div>';
        }
        $h .= '</div>';
        echo $h; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- alle dynamische waarden hierboven via esc_html()
        break;

    case 'inschrijven':
        // Maat-matchen met .ws-btn uit werving-selectie zodat hero-Inschrijven en de
        // ghost-knop "Hoe het werkt" als paar uitlijnen.
        $link = add_query_arg('startenrolment', '1', $course['link_to_mentor'] ?? '#');
        echo '<a href="' . esc_url($link) . '" '
            . 'style="display:inline-flex;align-items:center;gap:10px;font-family:' . $ws_body . ';padding:14px 28px;border-radius:6px;font-size:15px;font-weight:600;color:#fff;text-decoration:none;background:#FF4F36;box-shadow:0 14px 26px -12px rgba(255,79,54,0.6);transition:background .15s ease,transform .15s ease,box-shadow .15s ease;" '
            . 'onmouseover="this.style.background=\'#E23A22\';this.style.transform=\'translateY(-1px)\';this.style.boxShadow=\'0 18px 30px -10px rgba(255,79,54,0.7)\'" '
            . 'onmouseout="this.style.background=\'#FF4F36\';this.style.transform=\'\';this.style.boxShadow=\'0 14px 26px -12px rgba(255,79,54,0.6)\'">'
            . 'Inschrijven '
            . '<svg style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.4"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg></a>';
        break;
}
