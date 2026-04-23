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
│   └── reviews.php                # Reviews en beoordelingen
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

Tailwind CSS wordt gebruikt voor de trainingtrack-template. Overige templates gebruiken scoped CSS.

```bash
npm install
npx tailwindcss -i ./assets/css/styles.css -o ./assets/css/mentor-plugin.css --watch
```
