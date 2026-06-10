# Mentor Plugin voor WordPress

WordPress plugin die integreert met het Mentor platform. Toon cursussen, categorieën, startdata en reviews op je website.

## Installatie

1. Download de laatste release via de [releases pagina](https://github.com/MarkVergunst/mentor-api-plugin/releases)
2. Ga naar `Plugins > Nieuwe Plugin > Plugin Uploaden` in WordPress
3. Upload het `.zip`-bestand en activeer de plugin

## Configuratie

Ga naar **Mentor Plugin > Instellingen** en vul in:

| Instelling | Beschrijving |
|---|---|
| **API URL** | De basis-URL van je Mentor omgeving |
| **Knoptekst** | Label van de "Meer info"-knop op cursuskaartjes (leeg = "Meer info") |
| **Prijzen verbergen op overzichten** | Verbergt de prijs op de cursuslijst-blocks/shortcodes |
| **Klantthema overnemen** | Kleuren en lettertype uit Mentor overnemen |
| **Cache duur** | Hoe lang API-responses gecached worden (standaard 15 min) |

Onder **Mentor Plugin > Cursuslinks** kun je per cursus een eigen "Meer info"-URL instellen, bijvoorbeeld om door te linken naar je eigen trainingspagina in plaats van naar Mentor.

## Shortcodes

### Overzichten

| Shortcode | Beschrijving |
|---|---|
| `[mentor_courses]` | Cursussen als kaartjes met afbeelding, prijs en review-score |
| `[mentor_categories]` | Categorieën met zoekfunctie |
| `[mentor_startdata id=X]` | Startdata-agenda met locatie- en beschikbaarheidsfilters |
| `[display_coursegroup_wc id=X]` | Trainingsmomenten met dagplanning-modal |
| `[mentor_reviews]` | Alle reviews van de organisatie |
| `[mentor_reviews id=X]` | Reviews voor een specifieke cursus |

### Cursusdetailpagina

| Shortcode | Beschrijving |
|---|---|
| `[mentor_cursus_detail id=X]` | Complete detailpagina (alles in een) |
| `[mentor_cursus_titel]` | Cursustitel |
| `[mentor_cursus_thema]` | Thema / onderwerp |
| `[mentor_cursus_afbeelding]` | Cursusafbeelding |
| `[mentor_cursus_prijs]` | Prijs (incl. korting) |
| `[mentor_cursus_omschrijving]` | Volledige beschrijving |
| `[mentor_cursus_docenten]` | Docentenkaartjes met foto en bio |
| `[mentor_cursus_inschrijven]` | Inschrijfknop |
| `[mentor_cursus_reviews]` | Reviews voor deze cursus |

De losse veld-shortcodes ondersteunen `id=X` als attribuut, of lezen `?cursus_id=X` uit de URL.

## Gutenberg blocks

Alle shortcodes zijn ook beschikbaar als Gutenberg-blocks onder de categorie **Mentor** in de block-inserter. Ze renderen via `ServerSideRender` dus je ziet live preview in de editor.

| Block | Wrapt shortcode | Attributen |
|---|---|---|
| Mentor: Cursussen | `[mentor_courses]` | — |
| Mentor: Categorieën | `[mentor_categories]` | — |
| Mentor: Trainingstracks | `[display_coursegroup_wc]` | id |
| Mentor: Startdata | `[mentor_startdata]` | id |
| Mentor: Reviews | `[mentor_reviews]` | id |
| Mentor: Cursusdetail | `[mentor_cursus_detail]` | id |
| Mentor: Cursusveld | `[mentor_cursus_*]` | id + `field` (titel / prijs / omschrijving / afbeelding / thema / docenten / inschrijven / reviews) |

Het **Cursusveld**-block vervangt de losse `[mentor_cursus_titel]`, `[mentor_cursus_prijs]` etc.: één block met een dropdown voor het gewenste veld.

De ID-velden zijn optioneel — leeg laten leest `?cursus_id=X` uit de URL (handig voor een universele detailpagina).

## Shortcode Builder

Ga naar **Mentor Plugin > Shortcode Builder** om shortcodes visueel samen te stellen met live preview.

## Stijlen (skins)

Een cursus kan een eigen, aparte styling krijgen los van de standaard-templates — handig wanneer een cursus een eigen website heeft (bijv. een dedicated site per cursus). Een skin is een set alternatieve templates onder `templates/skins/<slug>/` die dezelfde data tonen als de standaard-templates, maar met eigen layout en CSS.

Meegeleverd: **`leiderschap`** — een "Fris & professioneel" stijl (zachte achtergrond `#F7F8FA`, fris blauw accent `#2F6BFF`, Plus Jakarta Sans-koppen + Inter-body, ruime afgeronde kaarten en pill-knoppen) voor de cursusdetailpagina, startdata-agenda en reviews.

Een skin activeren kan op twee manieren:

| Manier | Wanneer |
|---|---|
| **Globaal** via **Mentor Plugin → Instellingen → Stijl** | Op een site die volledig in één stijl moet (zet de dropdown op de gewenste skin; alle shortcodes/blocks volgen automatisch). |
| **Per shortcode** met `style="<slug>"` | Voor losse pagina's of testen, bijv. `[mentor_cursus_detail id=12 style=leiderschap]`. Overschrijft de globale instelling. |

Ondersteund door de attributen: `mentor_cursus_detail`, `mentor_startdata`, `mentor_reviews` en `mentor_cursus_reviews`. Ontbreekt een skin-bestand, dan valt de plugin automatisch terug op het standaard-template.

**Een nieuwe cursus-skin toevoegen:** maak een map `templates/skins/<slug>/` aan met `cursus-detail.php`, `startdata.php` en/of `reviews.php`. De map verschijnt vanzelf als optie in de Stijl-dropdown. Skin-slugs mogen alleen kleine letters, cijfers, `-` en `_` bevatten.

## Architectuur

De plugin is opgebouwd uit vijf klassen:

| Klasse | Bestand | Verantwoordelijkheid |
|---|---|---|
| `MentorApi` | `includes/class-mentor-api.php` | API calls, caching, review data |
| `MentorShortcodes` | `includes/class-mentor-shortcodes.php` | Alle shortcode handlers |
| `MentorTheme` | `includes/class-mentor-theme.php` | Klantthema (kleuren, fonts) |
| `MentorAdmin` | `includes/class-mentor-admin.php` | Instellingen, shortcode builder, cursuslinks, AJAX |
| `MentorBlocks` | `includes/class-mentor-blocks.php` | Gutenberg-blocks (wrappers rond de shortcodes) |

Het hoofdbestand (`mentor-api-plugin.php`) laadt de klassen en verbindt ze.

## Mappenstructuur

```
mentor-plugin/
├── mentor-api-plugin.php          # Hoofdbestand (bootstrap)
├── uninstall.php                  # Cleanup bij deinstallatie
├── includes/
│   ├── class-mentor-api.php       # API client + caching
│   ├── class-mentor-shortcodes.php # Shortcode handlers
│   ├── class-mentor-theme.php     # Klantthema injectie
│   ├── class-mentor-admin.php     # Admin pagina's + AJAX
│   ├── class-mentor-blocks.php    # Gutenberg-blocks registratie
│   └── template-functions.php     # Template loaders + helpers
├── templates/
│   ├── course.php                 # Cursuskaartjes
│   ├── course-list.php            # Cursuslijst (alternatief)
│   ├── category.php               # Categorieën
│   ├── cursus-detail.php          # Cursusdetailpagina
│   ├── cursus-docenten.php        # Docentenkaartjes
│   ├── startdata.php              # Startdata-agenda
│   ├── trainingtrack.php          # Trainingsmomenten met modal
│   ├── reviews.php                # Reviews en beoordelingen
│   └── skins/                     # Alternatieve styling per cursus
│       └── leiderschap/           # Skin "leiderschap" (Fris & professioneel)
│           ├── cursus-detail.php
│           ├── startdata.php
│           └── reviews.php
├── assets/
│   ├── css/
│   │   ├── mentor-plugin.css      # Gecompileerde Tailwind CSS
│   │   └── styles.css             # Tailwind bron
│   └── js/
│       └── blocks.js              # Block-registratie (vanilla JS, geen build)
├── tailwind.config.js
└── package.json
```

## Ontwikkeling

### Eerste keer opzetten (dev-omgeving)

Benodigdheden:

- **Node.js** 18+ (incl. npm) - alleen nodig om de Tailwind-CSS te bouwen.
- Een lokale **WordPress-installatie** (bijv. Local, XAMPP of Docker) om de plugin in te draaien.

Stappen:

1. Clone de repository:
   ```bash
   git clone <gitlab-repo-url> mentor-api-plugin
   cd mentor-api-plugin
   ```
2. Plaats (of symlink) de map in je WordPress-install onder `wp-content/plugins/mentor-api-plugin`
   en activeer de plugin via **Plugins** in wp-admin.
3. Installeer de build-dependencies:
   ```bash
   npm install
   ```
4. Vul de API-instellingen in onder **Mentor Plugin > Instellingen** (zie [Configuratie](#configuratie)).

### CSS bouwen

Tailwind CSS wordt gebruikt voor de trainingtrack-template; overige templates gebruiken scoped CSS.
De bron is `assets/css/styles.css`, de gecompileerde output is `assets/css/mentor-plugin.css`
(deze wordt gecommit zodat de plugin werkt zonder build-stap op de server).

```bash
npm run watch   # her-bouwt automatisch tijdens het ontwikkelen
npm run build   # eenmalige, geminificeerde productie-build
```
