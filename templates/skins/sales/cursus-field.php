<?php
/**
 * Skin: sales - Losse cursusvelden ("Sales-cockpit", data-dashboard-thema)
 * Verwacht: $course (array), $api_url (string), $mentor_field (titel|prijs|omschrijving|afbeelding|thema|inschrijven)
 * Wordt geladen vanuit MentorShortcodes::display_cursus_field() wanneer skin "sales" actief is.
 */
defined('ABSPATH') or die('No script kiddies please!');

if (!wp_style_is('mentor-sales-fonts', 'enqueued')) {
    wp_enqueue_style(
        'mentor-sales-fonts',
        'https://fonts.googleapis.com/css2?family=Sora:wght@500;600;700;800&family=Inter:wght@400;500;600&family=IBM+Plex+Mono:wght@500;600&display=swap',
        array(),
        null
    );
}

// Fontnaam-quotes als HTML-entiteit, zodat ze het double-quoted style="..."-attribuut
// niet voortijdig afbreken (anders valt alles na font-family weg).
$sc_head = '&quot;Sora&quot;, -apple-system, sans-serif';
$sc_body = '&quot;Inter&quot;, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, sans-serif';
$sc_mono = '&quot;IBM Plex Mono&quot;, ui-monospace, monospace';

switch ($mentor_field) {

    case 'titel':
        echo '<h1 style="font-family:' . $sc_head . ';font-size:clamp(2.2rem,1.4rem+2.6vw,3.4rem);font-weight:800;letter-spacing:-0.02em;line-height:1.05;color:#0E1626;margin:0;">'
            . esc_html($course['title'] ?? '') . '</h1>';
        break;

    case 'thema':
        $subject = $course['subject']['title'] ?? '';
        if ($subject === '') break;
        echo '<span style="display:inline-flex;align-items:center;gap:10px;font-family:' . $sc_mono . ';font-size:13px;font-weight:600;letter-spacing:0.04em;text-transform:uppercase;color:#0E8F5C;">'
            . '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">'
            . '<path d="M4 16l5-5 4 4 7-7" stroke="#0E8F5C" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>'
            . '<path d="M16 8h4v4" stroke="#0E8F5C" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>'
            . '</svg>'
            . esc_html($subject) . '</span>';
        break;

    case 'afbeelding':
        $img = $course['image'] ?? ($course['image_card_medium'] ?? '');
        if (empty($img)) break;
        echo '<div style="position:relative;width:100%;aspect-ratio:4/4.4;">'
            . '<span aria-hidden="true" style="position:absolute;inset:-22px -22px 22px 22px;background:#D7F0E4;border-radius:16px;z-index:0;"></span>'
            . '<span aria-hidden="true" style="position:absolute;top:-22px;left:-22px;width:86px;height:4px;background:#19C37D;border-radius:9999px;z-index:2;"></span>'
            . '<img src="' . esc_url($img) . '" alt="' . esc_attr($course['title'] ?? '') . '" '
            . 'style="position:relative;z-index:1;width:100%;height:100%;object-fit:cover;border-radius:16px;box-shadow:0 22px 50px -22px rgba(14,22,38,0.30);display:block;">'
            . '</div>';
        break;

    case 'omschrijving':
        echo '<div style="font-family:' . $sc_body . ';font-size:17px;line-height:1.85;color:#5B6675;">'
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

        $h  = '<div style="position:relative;overflow:hidden;font-family:' . $sc_body . ';background:#fff;border:1px solid #DDE4E3;border-top:3px solid #19C37D;border-radius:14px;box-shadow:0 4px 14px -6px rgba(14,22,38,0.18);padding:24px 28px;display:inline-block;min-width:240px;">';
        $h .= '<div style="position:relative;font-family:' . $sc_mono . ';font-size:11px;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:#5B6675;margin-bottom:6px;">Investering</div>';
        $h .= '<div style="position:relative;font-family:' . $sc_mono . ';font-size:2.3rem;font-weight:600;color:#0E1626;line-height:1;letter-spacing:-0.02em;">&euro;' . esc_html($price) . '</div>';
        $h .= '<div style="position:relative;font-family:' . $sc_mono . ';font-size:13px;color:#5B6675;margin-top:6px;">' . ($incl_vat ? 'incl. btw' : 'excl. btw') . '</div>';
        if (!empty($total) && $total !== $price) {
            $h .= '<div style="position:relative;font-size:13px;color:#5B6675;margin-top:16px;padding-top:16px;border-top:1px solid #DDE4E3;">Totaal incl. materiaal: <strong style="font-family:' . $sc_mono . ';color:#0E1626;">&euro;' . esc_html($total) . '</strong></div>';
        }
        if ($disc_perc > 0) {
            $h .= '<div style="position:relative;margin-top:14px;"><span style="display:inline-block;font-family:' . $sc_mono . ';background:#D7F0E4;color:#0E8F5C;font-size:12px;font-weight:600;padding:5px 13px;border-radius:8px;">' . esc_html($disc_name ?: $disc_perc . '% korting') . '</span></div>';
        }
        $h .= '</div>';
        echo $h; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- alle dynamische waarden hierboven via esc_html()
        break;

    case 'inschrijven':
        // Maat-matchen met .sc-btn uit sales-site.php zodat hero-Inschrijven en de
        // ghost-knop "Hoe het werkt" als paar uitlijnen. Emerald fill, donkere navy tekst.
        $link = add_query_arg('startenrolment', '1', $course['link_to_mentor'] ?? '#');
        echo '<a href="' . esc_url($link) . '" '
            . 'style="display:inline-flex;align-items:center;gap:10px;font-family:' . $sc_body . ';padding:14px 28px;border-radius:12px;font-size:15px;font-weight:700;color:#0E1626;text-decoration:none;background:#19C37D;box-shadow:0 14px 26px -12px rgba(25,195,125,0.55);transition:background .15s ease,transform .15s ease,box-shadow .15s ease;" '
            . 'onmouseover="this.style.background=\'#0E8F5C\';this.style.color=\'#fff\';this.style.transform=\'translateY(-1px)\';this.style.boxShadow=\'0 18px 30px -10px rgba(25,195,125,0.65)\'" '
            . 'onmouseout="this.style.background=\'#19C37D\';this.style.color=\'#0E1626\';this.style.transform=\'\';this.style.boxShadow=\'0 14px 26px -12px rgba(25,195,125,0.55)\'">'
            . 'Inschrijven '
            . '<svg style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.4"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg></a>';
        break;
}
