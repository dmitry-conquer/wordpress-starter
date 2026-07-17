<?php

namespace WP_Theme_Starter;

if (!defined("ABSPATH")) {
  exit();
}

final class Assets
{
  private const ENTRY = "src/scripts/main.ts";

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
    if (!defined("WP_THEME_STARTER_VITE_DEV_SERVER") || !WP_THEME_STARTER_VITE_DEV_SERVER) {
      return "";
    }

    $url = is_string(WP_THEME_STARTER_VITE_DEV_SERVER) ? WP_THEME_STARTER_VITE_DEV_SERVER : "http://localhost:5173";

    return untrailingslashit(esc_url_raw($url));
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
