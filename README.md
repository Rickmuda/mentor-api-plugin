# Plug and Play Plugin voor Mentor

Dit is een **plug-and-play plugin** die WordPress integreert met Mentor-cursussen. Met deze plugin kun je eenvoudig de categorieën en cursussen van je Mentor API weergeven in WordPress. Perfect voor het weergeven van je cursussen zonder technische rompslomp.

## Inhoudsopgave
- [Installatie](#installatie)
- [Configuratie](#configuratie)
- [Gebruik](#gebruik)
- [Ontwikkeling](#ontwikkeling)

## Installatie

### Stap 1: Download de Plugin

In plaats van de bronbestanden te gebruiken, raden we aan de **laatste stabiele versie** van de plugin te downloaden via de [releases pagina](https://github.com/[your-repo]/releases). Daar vind je een kant-en-klare `.zip`-bestand dat eenvoudig geïnstalleerd kan worden.

1. **Download de laatste release:**
   - Ga naar de [releases pagina](https://github.com/MarkVergunst/mentor-api-plugin/releases).
   - Download de nieuwste versie van de plugin (`mentor-api-plugin.zip`).
   - (Optioneel) Hernoem deze naar `mentor-api-plugin.zip`

2. **Upload de plugin:**
   - Ga naar je WordPress admin panel.
   - Navigeer naar `Plugins > Nieuwe Plugin > Plugin Uploaden`.
   - Upload het zojuist gedownloade `.zip`-bestand.

3. **Activeer de plugin:**
   - Nadat de upload is voltooid, klik op 'Activeer Plugin'.

## Configuratie

Na activatie moet je enkele instellingen configureren:

1. Ga naar `Instellingen > Mentor Courses and Categories`.
2. Voer de volgende gegevens in:
   - **API-URL:** De URL van je Mentor API.
3. Klik op 'Opslaan' om de configuratie op te slaan.

## Gebruik

Je kunt de plugin eenvoudig gebruiken door de shortcodes in je berichten of pagina's te plaatsen:

- Toon alle categorieën:  
  ```[mentor_categories]```

- Toon een lijst met cursussen:  
  ```[mentor_courses]```

Deze shortcodes halen dynamisch de gegevens op van je Mentor API en tonen ze in WordPress.

## Ontwikkeling

Als je aanpassingen wilt maken of de stijl van de plugin wilt wijzigen, kun je gebruik maken van **Tailwind CSS**.

### Stappen om te ontwikkelen:

1. Zorg ervoor dat je Node.js geïnstalleerd hebt.
2. Gebruik de volgende Tailwind command-line om de stijlen in de gaten te houden en te compileren:
   ```bash
   npx tailwindcss -i ./styles.css -o ./output.css
