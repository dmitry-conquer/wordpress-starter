<?php

namespace SiteTheme;

if (!defined("ABSPATH")) {
  exit();
}

final class Login
{
  private const STYLE_HANDLE = "site-theme-login";
  private const STYLE_PATH = "/assets/login/login.css";
  private const LOGO_PATH = "/assets/login/login-logo.png";

  public static function register(): void
  {
    add_action("login_enqueue_scripts", [self::class, "enqueue_styles"]);
    add_filter("login_headerurl", [self::class, "logo_url"]);
    add_filter("login_headertext", [self::class, "logo_text"]);
  }

  public static function enqueue_styles(): void
  {
    $theme_directory = get_template_directory();
    $theme_uri = get_template_directory_uri();
    $style_path = $theme_directory . self::STYLE_PATH;

    if (!is_readable($style_path)) {
      return;
    }

    wp_enqueue_style(self::STYLE_HANDLE, $theme_uri . self::STYLE_PATH, [], self::asset_version($style_path));

    $logo_path = $theme_directory . self::LOGO_PATH;

    if (!is_readable($logo_path)) {
      return;
    }

    $logo_url = add_query_arg("ver", self::asset_version($logo_path), $theme_uri . self::LOGO_PATH);
    wp_add_inline_style(self::STYLE_HANDLE, sprintf(':root { --theme-login-logo: url("%s"); }', esc_url($logo_url)));
  }

  public static function logo_url(string $default_url): string
  {
    return home_url("/") ?: $default_url;
  }

  public static function logo_text(string $default_text): string
  {
    $site_name = get_bloginfo("name");
    return $site_name !== "" ? $site_name : $default_text;
  }

  private static function asset_version(string $path): string
  {
    $modified = filemtime($path);
    if ($modified !== false) {
      return (string) $modified;
    }

    return wp_get_theme()->get("Version") ?: "1.0.0";
  }
}
