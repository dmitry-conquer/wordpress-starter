<?php

namespace SiteTheme;

if (!defined("ABSPATH")) {
  exit();
}

final class Utils
{
  public static function register() {}

  public static function bg_style($image_id, $size = "full")
  {
    $attachment_id = absint($image_id);
    if (!$attachment_id) {
      return "";
    }

    $url = wp_get_attachment_image_url($attachment_id, $size);
    if (!$url) {
      return "";
    }

    return sprintf('style="%s"', esc_attr(sprintf("background-image:url('%s')", esc_url($url))));
  }

  public static function icon(string $name, string $class = "size-6"): string
  {
    if (!in_array($name, ["phone", "chevron", "arrow-up-right", "arrow-right", "menu", "plus", "minus", "home", "shield-tick", "check-circle", "pin", "shield-tick-small", "star"], true)) {
      return "";
    }
    $url = get_template_directory_uri() . "/assets/icons/" . $name . ".svg";
    return sprintf('<span aria-hidden="true" class="inline-block shrink-0 bg-current %s" style="mask: url(%s) center / contain no-repeat"></span>', esc_attr($class), esc_url($url));
  }

  public static function asterisk_to_span($text)
  {
    if (empty($text) || !is_string($text)) {
      return $text;
    }
    return preg_replace("/\*([^*]+?)\*/", '<span>$1</span>', $text);
  }

  public static function render_button(array $link, string $type = "link", string $class = "", string $event = "open-contact-form"): void
  {
    if (empty($link["title"])) {
      return;
    }

    if ($type === "dialog") {
      printf('<button x-data type="button" aria-haspopup="dialog" @click="$dispatch(\'%s\')" class="%s">%s</button>', esc_attr($event), esc_attr($class), esc_html($link["title"]));
    } else {
      if (empty($link["url"])) {
        return;
      }

      $target = !empty($link["target"]) ? (string) $link["target"] : "_self";

      printf(
        '<a href="%s" target="%s"%s class="%s">%s</a>',
        esc_url($link["url"]),
        esc_attr($target),
        $target === "_blank" ? ' rel="noopener"' : "",
        esc_attr($class),
        esc_html($link["title"]),
      );
    }
  }
}
