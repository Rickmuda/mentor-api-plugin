<?php
/**
 * Skin: leiderschap — Losse cursusvelden ("Fris & professioneel")
 * Verwacht: $course (array), $api_url (string), $mentor_field (titel|prijs|omschrijving|afbeelding|thema|inschrijven)
 * Wordt geladen vanuit MentorShortcodes::display_cursus_field() wanneer skin "leiderschap" actief is.
 */
defined('ABSPATH') or die('No script kiddies please!');

if (!wp_style_is('mentor-leiderschap-fonts', 'enqueued')) {
    wp_enqueue_style(
        'mentor-leiderschap-fonts',
        'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap',
        array(),
        null
    );
}

// Fontnaam-quotes als HTML-entiteit, zodat ze het double-quoted style="..."-attribuut
// niet voortijdig afbreken (anders valt alles na font-family weg).
$lc_head = '&quot;Plus Jakarta Sans&quot;, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, sans-serif';
$lc_body = '&quot;Inter&quot;, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, sans-serif';

switch ($mentor_field) {

    case 'titel':
        echo '<h1 style="font-family:' . $lc_head . ';font-size:clamp(2rem,1.4rem+2vw,3rem);font-weight:800;letter-spacing:-0.02em;line-height:1.1;color:#16202C;margin:0;">'
            . esc_html($course['title'] ?? '') . '</h1>';
        break;

    case 'thema':
        $subject = $course['subject']['title'] ?? '';
        if ($subject === '') break;
        echo '<span style="display:inline-flex;align-items:center;gap:8px;font-family:' . $lc_body . ';font-size:13px;font-weight:600;color:#D97706;">'
            . '<span style="width:8px;height:8px;border-radius:50%;background:#D97706;"></span>'
            . esc_html($subject) . '</span>';
        break;

    case 'afbeelding':
        $img = $course['image'] ?? ($course['image_card_medium'] ?? '');
        if (empty($img)) break;
        echo '<img src="' . esc_url($img) . '" alt="' . esc_attr($course['title'] ?? '') . '" '
            . 'style="width:100%;aspect-ratio:4/3;object-fit:cover;border-radius:24px;box-shadow:0 18px 40px -20px rgba(22,32,44,0.25);display:block;">';
        break;

    case 'omschrijving':
        echo '<div style="font-family:' . $lc_body . ';font-size:17px;line-height:1.8;color:#586575;">'
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

        $h  = '<div style="font-family:' . $lc_body . ';background:#fff;border:1px solid #E6EAF0;border-radius:20px;box-shadow:0 2px 6px rgba(22,32,44,0.06);padding:22px 26px;display:inline-block;min-width:220px;text-align:center;">';
        if ($disc_perc > 0) {
            $h .= '<div style="margin-bottom:14px;"><span style="display:inline-block;background:#FEF3C7;color:#D97706;font-size:12px;font-weight:700;padding:5px 12px;border-radius:9999px;">' . esc_html($disc_name ?: $disc_perc . '% korting') . '</span></div>';
        }
        $h .= '<div style="font-size:12px;font-weight:600;letter-spacing:0.06em;text-transform:uppercase;color:#586575;margin-bottom:6px;">Investering</div>';
        $h .= '<div style="font-family:' . $lc_head . ';font-size:2.1rem;font-weight:800;color:#16202C;line-height:1.05;">&euro;' . esc_html($price) . '</div>';
        $h .= '<div style="font-size:13px;color:#586575;margin-top:4px;">' . ($incl_vat ? 'incl. btw' : 'excl. btw') . '</div>';
        if (!empty($total) && $total !== $price) {
            $h .= '<div style="font-size:13px;color:#586575;margin-top:14px;padding-top:14px;border-top:1px solid #E6EAF0;">Totaal incl. materiaal: <strong style="color:#16202C;">&euro;' . esc_html($total) . '</strong></div>';
        }
        $h .= '</div>';
        echo $h; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- alle dynamische waarden hierboven via esc_html()
        break;

    case 'inschrijven':
        $link = add_query_arg('startenrolment', '1', $course['link_to_mentor'] ?? '#');
        echo '<a href="' . esc_url($link) . '" '
            . 'style="display:inline-flex;align-items:center;gap:9px;font-family:' . $lc_body . ';padding:14px 28px;border-radius:12px;font-size:15px;font-weight:700;color:#fff;text-decoration:none;background-color:#D97706;box-shadow:0 6px 16px -8px rgba(217,119,6,0.40);transition:background-color .2s,box-shadow .2s;" '
            . 'onmouseover="this.style.backgroundColor=\'#B45309\'" onmouseout="this.style.backgroundColor=\'#D97706\'">'
            . 'Inschrijven '
            . '<svg style="width:18px;height:18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg></a>';
        break;
}
