<?php
/**
 * Skin: time-management - Losse cursusvelden ("Calm focus", planner-thema)
 * Verwacht: $course (array), $api_url (string), $mentor_field (titel|prijs|omschrijving|afbeelding|thema|inschrijven)
 * Wordt geladen vanuit MentorShortcodes::display_cursus_field() wanneer skin "time-management" actief is.
 */
defined('ABSPATH') or die('No script kiddies please!');

if (!wp_style_is('mentor-time-management-fonts', 'enqueued')) {
    wp_enqueue_style(
        'mentor-time-management-fonts',
        'https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;600;700&display=swap',
        array(),
        null
    );
}

// Fontnaam-quotes als HTML-entiteit, zodat ze het double-quoted style="..."-attribuut
// niet voortijdig afbreken (anders valt alles na font-family weg).
$tm_head = '&quot;Outfit&quot;, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, sans-serif';
$tm_body = '&quot;Inter&quot;, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, sans-serif';
$tm_mono = '&quot;JetBrains Mono&quot;, ui-monospace, SFMono-Regular, monospace';

switch ($mentor_field) {

    case 'titel':
        echo '<h1 style="font-family:' . $tm_head . ';font-size:clamp(2.2rem,1.4rem+2.6vw,3.4rem);font-weight:700;letter-spacing:-0.03em;line-height:1.05;color:#1A1F36;margin:0;">'
            . esc_html($course['title'] ?? '') . '</h1>';
        break;

    case 'thema':
        $subject = $course['subject']['title'] ?? '';
        if ($subject === '') break;
        echo '<span style="display:inline-flex;align-items:center;gap:12px;font-family:' . $tm_mono . ';font-size:12px;font-weight:600;letter-spacing:0.06em;text-transform:uppercase;color:#2D3FB5;">'
            . '<span style="width:26px;height:2px;background:#2D3FB5;display:inline-block;"></span>'
            . esc_html($subject) . '</span>';
        break;

    case 'afbeelding':
        $img = $course['image'] ?? ($course['image_card_medium'] ?? '');
        if (empty($img)) break;
        echo '<div style="position:relative;width:100%;">'
            . '<span aria-hidden="true" style="position:absolute;inset:-18px -14px -8px 14px;background:#FBE9C8;border-radius:22px;z-index:0;opacity:0.7;"></span>'
            . '<img src="' . esc_url($img) . '" alt="' . esc_attr($course['title'] ?? '') . '" '
            . 'style="position:relative;z-index:1;width:100%;aspect-ratio:4/3;object-fit:cover;border-radius:22px;box-shadow:0 22px 50px -22px rgba(26,31,54,0.30);display:block;">'
            . '</div>';
        break;

    case 'omschrijving':
        echo '<div style="font-family:' . $tm_body . ';font-size:17px;line-height:1.8;color:#525A75;">'
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

        $h  = '<div style="position:relative;overflow:hidden;font-family:' . $tm_body . ';background:#fff;border:1.5px solid #E7E2D5;border-radius:18px;box-shadow:0 4px 14px -6px rgba(26,31,54,0.10);padding:24px 28px;display:inline-block;min-width:240px;">';
        $h .= '<div style="font-family:' . $tm_mono . ';font-size:11px;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:#525A75;margin-bottom:8px;">Investering</div>';
        $h .= '<div style="font-family:' . $tm_head . ';font-size:2.3rem;font-weight:700;color:#1A1F36;line-height:1;letter-spacing:-0.02em;">&euro;' . esc_html($price) . '</div>';
        $h .= '<div style="font-size:13px;color:#525A75;margin-top:6px;">' . ($incl_vat ? 'incl. btw' : 'excl. btw') . '</div>';
        if (!empty($total) && $total !== $price) {
            $h .= '<div style="font-size:13px;color:#525A75;margin-top:16px;padding-top:16px;border-top:1px solid #E7E2D5;">Totaal incl. materiaal: <strong style="color:#1A1F36;">&euro;' . esc_html($total) . '</strong></div>';
        }
        if ($disc_perc > 0) {
            $h .= '<div style="margin-top:14px;"><span style="display:inline-block;background:#FBE9C8;color:#B5720A;font-family:' . $tm_mono . ';font-size:11px;font-weight:600;padding:5px 11px;border-radius:6px;letter-spacing:0.04em;">' . esc_html($disc_name ?: $disc_perc . '% korting') . '</span></div>';
        }
        $h .= '</div>';
        echo $h; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- alle dynamische waarden hierboven via esc_html()
        break;

    case 'inschrijven':
        $link = add_query_arg('startenrolment', '1', $course['link_to_mentor'] ?? '#');
        echo '<a href="' . esc_url($link) . '" '
            . 'style="display:inline-flex;align-items:center;gap:10px;font-family:' . $tm_body . ';padding:14px 28px;border-radius:9999px;font-size:15px;font-weight:700;color:#fff;text-decoration:none;background:#2D3FB5;box-shadow:0 14px 26px -12px rgba(45,63,181,0.55);transition:background .15s ease,transform .15s ease,box-shadow .15s ease;" '
            . 'onmouseover="this.style.background=\'#1F2C8A\';this.style.transform=\'translateY(-1px)\';this.style.boxShadow=\'0 18px 30px -10px rgba(45,63,181,0.65)\'" '
            . 'onmouseout="this.style.background=\'#2D3FB5\';this.style.transform=\'\';this.style.boxShadow=\'0 14px 26px -12px rgba(45,63,181,0.55)\'">'
            . 'Inschrijven '
            . '<svg style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.4"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg></a>';
        break;
}
