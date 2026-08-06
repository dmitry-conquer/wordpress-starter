<?php

namespace WP_Theme_Starter;

if (!defined("ABSPATH")) {
  exit();
}

final class Assets
{
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
      wp_enqueue_script("wp-theme-starter-vite-client", $dev_server . "/@vite/client", [], null, false);
      wp_enqueue_script("wp-theme-starter-app", $dev_server . "/" . self::ENTRY, [], null, true);
      return;
    }

    $manifest = self::manifest();
    $entry = $manifest[self::ENTRY] ?? null;

    if (!is_array($entry) || empty($entry["file"])) {
      if (defined("WP_DEBUG") && WP_DEBUG) {
        trigger_error("WP Theme Starter assets are missing. Run `pnpm build`.", E_USER_WARNING);
      }
      return;
    }

    foreach ($entry["css"] ?? [] as $index => $css) {
      wp_enqueue_style("wp-theme-starter-app" . ($index ? "-" . $index : ""), WP_THEME_STARTER_URI . "/assets/build/" . ltrim($css, "/"), [], WP_THEME_STARTER_VERSION);
    }

    wp_enqueue_script("wp-theme-starter-app", WP_THEME_STARTER_URI . "/assets/build/" . ltrim($entry["file"], "/"), [], WP_THEME_STARTER_VERSION, true);
  }

  public static function module_script(string $tag, string $handle, string $src): string
  {
    if (!in_array($handle, ["wp-theme-starter-vite-client", "wp-theme-starter-app"], true)) {
      return $tag;
    }

    return sprintf('<script type="module" src="%s"></script>' . "\n", esc_url($src));
  }

  public static function dev_server(): string
  {
    // package-theme.mjs excludes these files from release/, so automatic
    // detection can only run from the working source theme.
    if (!self::is_source_theme() || !self::is_dev_server_running(self::DEFAULT_DEV_SERVER)) {
      return "";
    }

    return self::DEFAULT_DEV_SERVER;
  }

  private static function is_source_theme(): bool
  {
    return is_readable(WP_THEME_STARTER_DIR . "/package.json") && is_readable(WP_THEME_STARTER_DIR . "/vite.config.js");
  }

  private static function is_dev_server_running(string $url): bool
  {
    static $checked = [];

    if (array_key_exists($url, $checked)) {
      return $checked[$url];
    }

    $response = wp_remote_get($url . "/__theme-dev-status", [
      "timeout" => 0.5,
      "redirection" => 0,
    ]);

    if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
      return $checked[$url] = false;
    }

    $payload = json_decode((string) wp_remote_retrieve_body($response), true);
    return $checked[$url] = is_array($payload) && ($payload["status"] ?? "") === "ready" && ($payload["service"] ?? "") === "theme-vite-dev-server";
  }

  private static function manifest(): array
  {
    $path = WP_THEME_STARTER_DIR . "/assets/build/.vite/manifest.json";
    if (!is_readable($path)) {
      return [];
    }

    $manifest = json_decode((string) file_get_contents($path), true);
    return is_array($manifest) ? $manifest : [];
  }
}
