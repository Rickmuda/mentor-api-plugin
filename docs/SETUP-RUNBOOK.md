# Setup-runbook: mentor-api-plugin tot werkende dev-omgeving

Stap-voor-stap plan om dit project naar de huidige werkende staat te brengen.
Geschreven zodat een andere Claude-client (of developer) het van begin tot eind kan volgen.

## Doel: wat is "dit punt"?

De eindstaat die we reproduceren:

- Git-repo gekoppeld aan de fork `origin` en het origineel `upstream`.
- Default-branch heet `main` (lokaal en op GitHub).
- Referentie-commit: `f457ea5` op `main`, in sync met `origin/main`.
- Plugin-versie overal gelijk op `2.1.0`.
- Dev-omgeving werkt: `npm run build` (Tailwind) en `npm run wp:start` (wp-env/Docker)
  starten een WordPress met de plugin actief op http://localhost:8888.

Remotes in de eindstaat:

| Remote | URL |
|---|---|
| `origin` | https://github.com/Rickmuda/mentor-api-plugin |
| `upstream` | https://github.com/MarkVergunst/mentor-api-plugin |

## Vereisten

- **Git**
- **Node.js 18+** (getest met v22.11.0) en npm
- **Docker Desktop** (draaiend) - alleen nodig voor de wp-env testomgeving
- Optioneel: **GitHub-toegang** tot de fork (push) en `gh` CLI of een werkende git-credential

## Route A: snelste weg via clone (aanbevolen)

Al het werk staat op GitHub, dus een verse omgeving is een kwestie van clonen plus dev-setup.

```bash
# 1. Clone de fork
git clone https://github.com/Rickmuda/mentor-api-plugin.git
cd mentor-api-plugin

# 2. Koppel het origineel als upstream (voor latere updates)
git remote add upstream https://github.com/MarkVergunst/mentor-api-plugin.git
git fetch upstream

# 3. Installeer dev-dependencies (Tailwind + wp-env)
npm install

# 4. Bouw de CSS
npm run build

# 5. Start de WordPress-testomgeving (vereist draaiende Docker)
npm run wp:start
```

Daarna:

- Site: http://localhost:8888
- Admin: http://localhost:8888/wp-admin (gebruiker `admin`, wachtwoord `password`)
- Vul onder **Mentor Plugin > Instellingen** de Mentor **API-URL** in. Zonder geldige
  API-URL tonen de shortcodes/blocks geen cursusdata. De waarde is de basis-URL van de
  Mentor-omgeving (klant-specifiek, niet in de code aanwezig). Op te halen uit een bestaande
  live cursus-site met: `wp option get mentor_courses_api_url`.

Verificatie: zie de checklist onderaan.

## Route B: vanaf nul opbouwen (alleen een kale werkmap, geen git)

Volg dit alleen als er nog geen git-historie is en je de eindstaat handmatig moet reconstrueren.

### B1. Git initialiseren

```bash
git init
```

### B2. .gitignore aanmaken

```
node_modules/
.wp-env-override.json
.DS_Store
Thumbs.db
desktop.ini
*.log
.env
.env.*
```

### B3. Dev-omgevingsbestanden toevoegen

- `.gitattributes` met `* text=auto eol=lf` plus `*.png/*.webp/... binary` (forceert LF, lost
  CRLF-waarschuwingen op Windows op).
- `.editorconfig` met `end_of_line = lf`, `charset = utf-8`, final newline + trim trailing.
- `package.json` scripts:
  ```json
  "scripts": {
    "build": "tailwindcss -i ./assets/css/styles.css -o ./assets/css/mentor-plugin.css --minify",
    "watch": "tailwindcss -i ./assets/css/styles.css -o ./assets/css/mentor-plugin.css --watch",
    "wp:start": "wp-env start",
    "wp:stop": "wp-env stop",
    "wp:clean": "wp-env clean all",
    "wp:destroy": "wp-env destroy"
  }
  ```

### B4. Branch hernoemen naar main

```bash
git branch -m master main
```

### B5. Remotes koppelen en fork ophalen

```bash
git remote add origin https://github.com/Rickmuda/mentor-api-plugin.git
git remote add upstream https://github.com/MarkVergunst/mentor-api-plugin.git
git fetch origin
```

### B6. Lokaal werk netjes bovenop de fork-historie zetten (niet-destructief)

De lokale werkmap is nieuwer dan de fork maar heeft een losstaande historie. Zet het lokale
werk als delta bovenop de fork-tip in plaats van de fork te overschrijven:

```bash
# Commit eerst het lokale werk in een verse historie (bijv. 1 of 2 commits).
git add -A && git commit -m "Initial commit"

# Verplaats de branch-pointer naar de fork-tip, behoud werk als staged delta:
git reset --soft origin/master

# Controleer dat de fork-tip nu een voorouder wordt na committen:
git commit -m "v2.1.x: cursus-specifieke skins + dev-omgeving"
git merge-base --is-ancestor origin/master main && echo "fast-forward push mogelijk"
```

Belangrijk: dit bewaart de PR-historie van de fork. Gebruik geen force-push.

### B7. Versie gelijktrekken naar 2.1.0

Pas aan zodat alles `2.1.0` is:

- `mentor-api-plugin.php`: header `Version:` en `define('MENTOR_PLUGIN_VERSION', ...)`
- `readme.txt`: `Stable tag:` plus een `= 2.1.0 =` changelog- en upgrade-notice-entry
- `package.json`: `version`

Historische `2.0.1`-changelog-entries laten staan.

### B8. wp-env (Docker) toevoegen

```bash
npm install --save-dev @wordpress/env
```

`.wp-env.json`:

```json
{
  "core": null,
  "plugins": [ "." ],
  "port": 8888,
  "testsEnvironment": false,
  "config": {
    "WP_DEBUG": true,
    "WP_DEBUG_LOG": true,
    "WP_DEBUG_DISPLAY": true
  }
}
```

`testsEnvironment: false` voorkomt dat wp-env een tweede (test-)WordPress start en de
bijbehorende deprecation-warning.

### B9. Pushen

Branchnaam `main` aanhouden, op GitHub `main` als default instellen (via de GitHub-webinterface,
Branches > rename, of `gh api`). Daarna:

```bash
git push -u origin main
git fetch origin --prune
```

## Verificatie-checklist

```bash
# Repo in sync, op main:
git status -sb            # verwacht: ## main...origin/main, schoon

# Remotes correct:
git remote -v             # origin = Rickmuda, upstream = MarkVergunst

# Versie consistent:
grep -n "2.1.0" mentor-api-plugin.php package.json   # header + constante + package

# Build werkt:
npm run build             # geen errors, assets/css/mentor-plugin.css bijgewerkt

# WordPress + plugin draaien:
npm run wp:start
curl -s -o /dev/null -w "%{http_code}\n" http://localhost:8888    # verwacht 200
npx wp-env run cli wp plugin list                                  # mentor... = active, 2.1.0
```

Eindstaat bereikt wanneer: HTTP 200, plugin `active` op versie `2.1.0`, en `git status` schoon
op `main` in sync met `origin/main`.

## Aandachtspunten

- **OneDrive:** de werkmap kan in een OneDrive-map staan; dan wordt `.git` mee-gesynct. Werkt,
  maar bij zeldzame lock-conflicten is dat meestal de oorzaak.
- **API-URL:** niet in de code aanwezig; per Mentor-omgeving instellen in wp-admin. Op te halen
  uit een bestaande live site via `wp option get mentor_courses_api_url`.
- **Docker:** `npm run wp:start` vereist een draaiende Docker Desktop. Eerste start downloadt
  de WordPress/MySQL-images (kan enkele minuten duren).
- **Niet force-pushen** naar de fork: het lokale werk hoort als fast-forward bovenop de
  bestaande fork-historie te komen.
