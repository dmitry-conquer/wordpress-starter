<?php

namespace SiteTheme;

if (!defined("ABSPATH")) {
  exit();
}

final class Setup
{
  public static function register()
  {
    add_action("after_setup_theme", [self::class, "setup_theme"]);
  }

  public static function setup_theme()
  {
    add_theme_support("post-thumbnails");
    add_theme_support("title-tag");
    add_theme_support("html5", ["search-form", "gallery", "caption", "style", "script"]);
    add_theme_support("custom-logo", [
      "height" => 100,
      "width" => 400,
      "flex-height" => true,
      "flex-width" => true,
    ]);
  }
}
