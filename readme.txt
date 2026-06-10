=== Mentor Integration ===
Contributors: markvergunst
Tags: courses, training, mentor, catalog, reviews
Requires at least: 5.6
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 2.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Display courses, categories, start dates and reviews from the Mentor platform on your WordPress website.

== Description ==

The Mentor Integration plugin connects your WordPress website to the [Mentor](https://poweredbymentor.nl) platform. Display your course catalog, training dates, instructor profiles and student reviews using simple shortcodes.

**Features:**

* Courses displayed as cards with image, price and review score
* Categories with search functionality
* Start date calendar with location and availability filters
* Student reviews and ratings
* Complete course detail pages
* Visual Shortcode Builder in the admin panel
* Theme inheritance (colors and font from your Mentor environment)
* Configurable cache for API responses

**Available shortcodes:**

* `[mentor_courses]` – Course overview as cards
* `[mentor_categories]` – Category overview
* `[mentor_startdata id=X]` – Start dates for a specific course
* `[display_coursegroup_wc id=X]` – Training sessions with day schedule modal
* `[mentor_reviews]` – All reviews or `[mentor_reviews id=X]` for a specific course
* `[mentor_cursus_detail id=X]` – Complete course detail page

**Composite detail shortcodes:**

* `[mentor_cursus_titel]`, `[mentor_cursus_prijs]`, `[mentor_cursus_afbeelding]`
* `[mentor_cursus_omschrijving]`, `[mentor_cursus_docenten]`, `[mentor_cursus_inschrijven]`
* `[mentor_cursus_thema]`, `[mentor_cursus_reviews]`

**Note:** This plugin connects to the Mentor SaaS platform via its API. A Mentor account is required. Data is fetched from your configured Mentor environment URL. NL Digital terms of service apply to the service.

== Installation ==

1. Upload the `mentor-plugin` folder to `/wp-content/plugins/`
2. Activate the plugin via the 'Plugins' menu in WordPress
3. Go to **Mentor Integration > Settings** and enter your Mentor API URL
4. Optionally enable theme inheritance to adopt your Mentor branding
5. Use the **Shortcode Builder** or add shortcodes manually to your pages

== Frequently Asked Questions ==

= Do I need a Mentor account? =

Yes. This plugin displays data from the Mentor platform. You need an active Mentor environment with a valid API URL.

= How does the cache work? =

API responses are cached using WordPress transients. The default cache duration is 15 minutes and is configurable. You can manually clear the cache via the settings page.

= Can I customize the appearance? =

Yes. Enable "Inherit client theme" in the settings to automatically apply the colors and font from your Mentor environment. You can also override the styles with your own CSS.

== Screenshots ==

1. Course overview as cards
2. Start date calendar with filters
3. Course detail page
4. Shortcode Builder in the admin panel
5. Settings page

== Changelog ==

= 2.1.0 =
* Added Gutenberg blocks for all shortcodes (server-side rendered)
* Added granular course-field shortcodes and the field block
* Added admin options and improved output escaping
* Added per-course skin system (templates/skins/<slug>/) for dedicated course sites

= 2.0.1 =
* Prepared for WordPress.org plugin directory
* Added GPL license and readme.txt
* Improved output escaping in templates
* Extended plugin header with required fields

= 2.0.0 =
* Complete rewrite of the plugin
* Added Shortcode Builder with live preview
* Added composite course detail shortcodes
* Added reviews and ratings
* Added theme inheritance (colors and font)
* Added configurable cache duration
* Improved API error handling

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 2.1.0 =
Adds Gutenberg blocks, granular shortcodes and a per-course skin system. Existing shortcodes keep working.

= 2.0.1 =
Major update with new shortcodes, Shortcode Builder and theme support. Check your existing shortcodes after updating.
