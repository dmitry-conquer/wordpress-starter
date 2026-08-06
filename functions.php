<?php

/**
 * WP Starter Theme functions and definitions
 *
 * This theme uses a class-based approach for code organization.
 * All functionality is organized into classes located in the /inc/ directory.
 *
 * IMPORTANT: Avoid adding code directly to this file. Instead:
 * - Add new functionality to existing classes in /inc/
 * - Create new classes in /inc/ for new features
 * - Use the autoloader system for proper class loading
 */

if (!defined("ABSPATH")) {
  exit();
}

// Define theme constants
define("WP_THEME_STARTER_DIR", get_template_directory());
define("WP_THEME_STARTER_URI", get_template_directory_uri());
define("WP_THEME_STARTER_VERSION", wp_get_theme()->get("Version") ?: "1.0.0");

// Import theme classes
use WP_Theme_Starter\Autoloader;
use WP_Theme_Starter\Assets;
use WP_Theme_Starter\DevIndicator;
use WP_Theme_Starter\Setup;
use WP_Theme_Starter\Editor;
use WP_Theme_Starter\Menu;
use WP_Theme_Starter\Shortcodes;
use WP_Theme_Starter\Utils;

// Load autoloader for class management
require_once WP_THEME_STARTER_DIR . "/inc/Autoloader.php";

// Initialize theme components
Autoloader::register();
Assets::register();
Setup::register();
Editor::register();
Menu::register();
Shortcodes::register();
Utils::register();

// Show the development status indicator only in the source theme.
if (is_readable(WP_THEME_STARTER_DIR . "/package.json") && is_readable(WP_THEME_STARTER_DIR . "/vite.config.js")) {
  DevIndicator::register();
}
