<?php

/**
 * Theme functions and definitions
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

// Import theme classes
use SiteTheme\Autoloader;
use SiteTheme\Assets;
use SiteTheme\Cleanup;
use SiteTheme\DevIndicator;
use SiteTheme\Login;
use SiteTheme\Setup;
use SiteTheme\Menu;
use SiteTheme\Shortcodes;
use SiteTheme\Utils;

// Load autoloader for class management
require_once get_template_directory() . "/inc/Autoloader.php";

// Initialize theme components
Autoloader::register();
Assets::register();
Setup::register();
Cleanup::register();
Login::register();
Menu::register();
Shortcodes::register();
Utils::register();

// Show the development status indicator only in the source theme.
if (is_readable(get_template_directory() . "/package.json") && is_readable(get_template_directory() . "/vite.config.js")) {
  DevIndicator::register();
}
