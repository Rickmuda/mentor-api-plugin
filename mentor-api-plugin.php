<?php
/*
Plugin Name: Mentor Plugin
Description: Toon cursussen, categorieën en trainingsmomenten van Mentor op je WordPress website.
Version: 2.0
Author: Mark Vergunst
*/

defined('ABSPATH') or die('No script kiddies please!');

define('MENTOR_PLUGIN_VERSION', '2.0');
define('MENTOR_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('MENTOR_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once MENTOR_PLUGIN_DIR . 'includes/template-functions.php';
require_once MENTOR_PLUGIN_DIR . 'includes/class-mentor-api.php';
require_once MENTOR_PLUGIN_DIR . 'includes/class-mentor-shortcodes.php';
require_once MENTOR_PLUGIN_DIR . 'includes/class-mentor-theme.php';
require_once MENTOR_PLUGIN_DIR . 'includes/class-mentor-admin.php';

$mentor_api = new MentorApi();
new MentorShortcodes($mentor_api);
new MentorTheme($mentor_api);
new MentorAdmin($mentor_api);
