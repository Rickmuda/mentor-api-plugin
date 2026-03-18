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
| **Klantthema overnemen** | Kleuren en lettertype uit Mentor overnemen |
| **Cache duur** | Hoe lang API-responses gecached worden (standaard 15 min) |

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

## Shortcode Builder

Ga naar **Mentor Plugin > Shortcode Builder** om shortcodes visueel samen te stellen met live preview.

## Architectuur

De plugin is opgebouwd uit vier klassen:

| Klasse | Bestand | Verantwoordelijkheid |
|---|---|---|
| `MentorApi` | `includes/class-mentor-api.php` | API calls, caching, review data |
| `MentorShortcodes` | `includes/class-mentor-shortcodes.php` | Alle shortcode handlers |
| `MentorTheme` | `includes/class-mentor-theme.php` | Klantthema (kleuren, fonts) |
| `MentorAdmin` | `includes/class-mentor-admin.php` | Instellingen, shortcode builder, AJAX |

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
│   └── template-functions.php     # Template loaders + star helper
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
│   └── css/
│       ├── mentor-plugin.css      # Gecompileerde Tailwind CSS
│       └── styles.css             # Tailwind bron
├── tailwind.config.js
└── package.json
```

## Ontwikkeling

Tailwind CSS wordt gebruikt voor de trainingtrack-template. Overige templates gebruiken scoped CSS.

```bash
npm install
npx tailwindcss -i ./assets/css/styles.css -o ./assets/css/mentor-plugin.css --watch
```
