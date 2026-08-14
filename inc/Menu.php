<?php

namespace SiteTheme;

if (!defined("ABSPATH")) {
  exit();
}

final class Menu
{
  public static function register(): void
  {
    add_action("after_setup_theme", [self::class, "register_menus"]);
    add_filter("nav_menu_link_attributes", [self::class, "footer_link_attributes"], 10, 4);
    add_filter("nav_menu_item_attributes", [self::class, "footer_item_attributes"], 10, 4);
  }

  public static function register_menus(): void
  {
    register_nav_menus([
      "header_menu" => "Header menu",
      "footer_menu" => "Footer mobile menu",
      "footer_locations_menu" => "Footer locations menu",
      "footer_quick_links_menu" => "Footer quick links menu",
    ]);
  }

  public static function footer_link_attributes(array $attributes, $item, $args, int $depth): array
  {
    if (self::is_footer_location($args->theme_location ?? "")) {
      $attributes["class"] = "block py-1 text-sm text-zinc-400 transition-colors duration-300 hover:text-slate-500";
    }

    return $attributes;
  }

  public static function footer_item_attributes(array $attributes, $item, $args, int $depth): array
  {
    if (self::is_footer_location($args->theme_location ?? "")) {
      $attributes["class"] = "leading-none";
    }

    return $attributes;
  }

  private static function is_footer_location(string $location): bool
  {
    return in_array($location, ["footer_menu", "footer_locations_menu", "footer_quick_links_menu"], true);
  }
}
