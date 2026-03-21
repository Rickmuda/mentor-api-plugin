=== Mentor Plugin ===
Contributors: markvergunst
Tags: cursussen, training, mentor, catalogus, reviews
Requires at least: 5.6
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 2.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Toon cursussen, categorieën, startdata en reviews van het Mentor platform op je WordPress website.

== Description ==

De Mentor Plugin koppelt je WordPress website aan het [Mentor](https://poweredbymentor.nl) platform. Toon je cursuscatalogus, trainingsdata, docentprofielen en cursistreviews met behulp van eenvoudige shortcodes.

**Functies:**

* Cursussen als kaartjes met afbeelding, prijs en reviewscore

* Categorieën met zoekfunctie
* Startdata-agenda met locatie- en beschikbaarheidsfilters
* Reviews en beoordelingen van cursisten
* Complete cursusdetailpagina's
* Visuele Shortcode Builder in het adminpaneel
* Thema-overname (kleuren en lettertype uit je Mentor omgeving)
* Instelbare cache voor API-responses

**Beschikbare shortcodes:**

* `[mentor_courses]` – Cursusoverzicht als kaartjes
* `[mentor_categories]` – Categorieoverzicht
* `[mentor_startdata id=X]` – Startdata voor een specifieke cursus
* `[display_coursegroup_wc id=X]` – Trainingsmomenten met dagplanning-modal
* `[mentor_reviews]` – Alle reviews of `[mentor_reviews id=X]` voor een specifieke cursus
* `[mentor_cursus_detail id=X]` – Complete cursusdetailpagina

**Samengestelde detail-shortcodes:**

* `[mentor_cursus_titel]`, `[mentor_cursus_prijs]`, `[mentor_cursus_afbeelding]`
* `[mentor_cursus_omschrijving]`, `[mentor_cursus_docenten]`, `[mentor_cursus_inschrijven]`
* `[mentor_cursus_thema]`, `[mentor_cursus_reviews]`

**Let op:** Deze plugin maakt verbinding met het Mentor SaaS-platform via de API. Een Mentor-account is vereist. Data wordt opgehaald van de door jou ingestelde Mentor-omgevings-URL. Op de dienst zijn de NL Digital voorwaarden van toepassing.

== Installation ==

1. Upload de `mentor-plugin` map naar `/wp-content/plugins/`
2. Activeer de plugin via het menu 'Plugins' in WordPress
3. Ga naar **Mentor Plugin > Instellingen** en vul je Mentor API URL in
4. Schakel optioneel thema-overname in om je Mentor-huisstijl over te nemen
5. Gebruik de **Shortcode Builder** of voeg shortcodes handmatig toe aan je pagina's

== Frequently Asked Questions ==

= Heb ik een Mentor-account nodig? =

Ja. Deze plugin toont data van het Mentor platform. Je hebt een actieve Mentor-omgeving nodig met een geldige API URL.

= Hoe werkt de cache? =

API-responses worden gecached met WordPress transients. De standaard cacheduur is 15 minuten en is instelbaar. Je kunt de cache handmatig legen via de instellingenpagina.

= Kan ik het uiterlijk aanpassen? =

Ja. Schakel "Klantthema overnemen" in bij de instellingen om automatisch de kleuren en het lettertype van je Mentor-omgeving toe te passen. Je kunt de stijlen ook overschrijven met eigen CSS.

== Screenshots ==

1. Cursusoverzicht als kaartjes
2. Startdata-agenda met filters
3. Cursusdetailpagina
4. Shortcode Builder in het adminpaneel
5. Instellingenpagina

== Changelog ==

= 2.0.1 =
* Voorbereid voor WordPress.org plugin directory
* GPL-licentie en readme.txt toegevoegd
* Verbeterde output escaping in templates
* Plugin header uitgebreid met verplichte velden

= 2.0.0 =
* Volledige herschrijving van de plugin
* Shortcode Builder met live preview toegevoegd
* Samengestelde cursusdetail-shortcodes toegevoegd
* Reviews en beoordelingen toegevoegd
* Thema-overname (kleuren en lettertype) toegevoegd
* Instelbare cacheduur toegevoegd
* Verbeterde API-foutafhandeling

= 1.0.0 =
* Eerste release

== Upgrade Notice ==

= 2.0.1 =
Grote update met nieuwe shortcodes, Shortcode Builder en thema-ondersteuning. Controleer je bestaande shortcodes na het updaten.
