<?php

namespace SiteTheme;

if (!defined("ABSPATH")) {
  exit();
}

final class Assets
{
  private const APP_HANDLE = "site-theme-app";
  private const VITE_CLIENT_HANDLE = "site-theme-vite-client";
  private const ENTRY = "src/scripts/main.ts";
  private const DEFAULT_DEV_SERVER = "http://localhost:5173";

  public static function register(): void
  {
    add_action("wp_enqueue_scripts", [self::class, "enqueue_assets"]);
    add_filter("script_loader_tag", [self::class, "module_script"], 10, 3);
  }

  public static function enqueue_assets(): void
  {
    $dev_server = self::dev_server();

    if ($dev_server !== "") {
      wp_enqueue_script(self::VITE_CLIENT_HANDLE, $dev_server . "/@vite/client", [], null, false);
      wp_enqueue_script(self::APP_HANDLE, $dev_server . "/" . self::ENTRY, [], null, true);
      return;
    }

    $manifest = self::manifest();
    $entry = $manifest[self::ENTRY] ?? null;

    if (!is_array($entry) || empty($entry["file"])) {
      if (defined("WP_DEBUG") && WP_DEBUG) {
        trigger_error("Theme assets are missing. Run `pnpm build`.", E_USER_WARNING);
      }
      return;
    }

    $theme_uri = get_template_directory_uri();
    $version = self::theme_version();

    foreach ($entry["css"] ?? [] as $index => $css) {
      wp_enqueue_style(self::APP_HANDLE . ($index ? "-" . $index : ""), $theme_uri . "/assets/build/" . ltrim($css, "/"), [], $version);
    }

    wp_enqueue_script(self::APP_HANDLE, $theme_uri . "/assets/build/" . ltrim($entry["file"], "/"), [], $version, true);
  }

  public static function module_script(string $tag, string $handle, string $src): string
  {
    if (!in_array($handle, [self::VITE_CLIENT_HANDLE, self::APP_HANDLE], true)) {
      return $tag;
    }

    return sprintf('<script type="module" src="%s"></script>' . "\n", esc_url($src));
  }

  public static function dev_server(): string
  {
    return self::is_source_theme() && self::vite_is_running() ? self::DEFAULT_DEV_SERVER : "";
  }

  private static function is_source_theme(): bool
  {
    $theme_directory = get_template_directory();
    return is_readable($theme_directory . "/package.json") && is_readable($theme_directory . "/vite.config.js");
  }

  private static function vite_is_running(): bool
  {
    static $running = null;

    if ($running !== null) {
      return $running;
    }

    $response = wp_remote_get(self::DEFAULT_DEV_SERVER . "/__theme-dev-status", [
      "timeout" => 0.5,
      "redirection" => 0,
    ]);

    if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
      return $running = false;
    }

    $payload = json_decode((string) wp_remote_retrieve_body($response), true);
    return $running = is_array($payload) && ($payload["status"] ?? "") === "ready" && ($payload["service"] ?? "") === "theme-vite-dev-server";
  }

  private static function manifest(): array
  {
    $path = get_template_directory() . "/assets/build/.vite/manifest.json";
    if (!is_readable($path)) {
      return [];
    }

    $manifest = json_decode((string) file_get_contents($path), true);
    return is_array($manifest) ? $manifest : [];
  }

  private static function theme_version(): string
  {
    return wp_get_theme()->get("Version") ?: "1.0.0";
  }
}
