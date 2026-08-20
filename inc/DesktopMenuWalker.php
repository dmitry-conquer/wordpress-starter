<?php

namespace SiteTheme;

if (!defined("ABSPATH")) {
  exit();
}

final class DesktopMenuWalker extends \Walker_Nav_Menu
{
  private const ROW_D1 = "flex items-center transition-colors hover:bg-stone-50";
  private const LINK_D0 = "flex items-center px-4 py-2 text-sm text-stone-600 transition-colors hover:text-stone-900 focus-visible:rounded-lg focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500";
  private const LINK_D0_ACTIVE = "flex items-center px-4 py-2 text-sm font-medium text-stone-900 transition-colors hover:text-stone-600 focus-visible:rounded-lg focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500";
  private const LINK_D1 = "flex items-center px-4 py-2.5 text-sm text-stone-600 transition-colors hover:bg-stone-50 hover:text-stone-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-inset";
  private const LINK_D1_ACTIVE = "relative flex items-center px-4 py-2.5 text-sm font-medium text-stone-900 transition-colors hover:bg-stone-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-inset";
  private const LINK_D1_PARENT = "flex min-w-0 flex-1 items-center py-2.5 pl-4 text-sm text-stone-600 transition-colors hover:text-stone-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-inset";
  private const BUTTON_D0 = "flex items-center gap-1 px-4 py-2 text-sm text-stone-600 transition-colors hover:text-stone-900 focus-visible:rounded-lg focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500";
  private const BUTTON_D1 = "mr-1 flex size-8 shrink-0 items-center justify-center text-stone-400 transition-colors hover:text-stone-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-inset";
  private const ACTIVE_BAR_D1 = "absolute top-1/2 left-0 h-4 w-0.5 -translate-y-1/2 rounded-r bg-amber-500";
  private const CHEVRON_DOWN = "h-4 w-4 shrink-0 transition-transform duration-200";
  private const CHEVRON_RIGHT = "h-4 w-4 shrink-0 transition-transform duration-150";

  private string $submenu_id = "";

  public function start_lvl(&$output, $depth = 0, $args = null): void
  {
    $motion =
      $depth === 0
        ? ' x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-1" class="absolute top-full left-0 z-50 pt-2"'
        : ' x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 translate-x-2" x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-2" class="absolute top-0 left-full z-50 pl-2"';
    $ul_class = $depth === 0 ? "min-w-44" : "min-w-48";
    $id = $this->submenu_id ? ' id="' . esc_attr($this->submenu_id) . '"' : "";

    $output .= "<div" . $id . ' x-show="open" x-cloak' . $motion . ">\n";
    $output .= '<ul role="list" class="' . esc_attr($ul_class . " rounded-xl bg-white py-1.5 shadow-xl ring-1 shadow-stone-900/8 ring-stone-100") . "\">\n";
  }

  public function end_lvl(&$output, $depth = 0, $args = null): void
  {
    $output .= "</ul>\n</div>\n";
  }

  public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0): void
  {
    $classes = empty($item->classes) ? [] : (array) $item->classes;
    $current = in_array("current-menu-item", $classes, true);
    $active = $current || in_array("current-menu-ancestor", $classes, true);
    $has_children = in_array("menu-item-has-children", $classes, true);
    $title = $this->title($item, $args, $depth);

    $output .= $this->open_li($has_children);

    if ($has_children) {
      $this->submenu_id = "desktop-submenu-" . (int) $item->ID;

      if ($depth === 0) {
        $output .= $this->toggle_button($title, $this->submenu_id, $depth);
        return;
      }

      $output .= '<div class="' . esc_attr(self::ROW_D1) . "\">\n";
      $output .= $this->link($item, $title, self::LINK_D1_PARENT, $current);
      $output .= $this->toggle_button($title, $this->submenu_id, $depth);
      $output .= "</div>\n";
      return;
    }

    $class = $depth === 0 ? ($active ? self::LINK_D0_ACTIVE : self::LINK_D0) : ($active ? self::LINK_D1_ACTIVE : self::LINK_D1);
    $prefix = $depth === 1 && $active ? $this->active_bar() : "";

    $output .= $this->link($item, $title, $class, $current, $prefix);
  }

  public function end_el(&$output, $item, $depth = 0, $args = null): void
  {
    $output .= "</li>\n";
  }

  private function open_li(bool $has_children): string
  {
    if (!$has_children) {
      return "<li>\n";
    }

    return '<li x-data="{ open: false }" class="relative"' .
      ' @pointerenter="$event.pointerType !== \'touch\' && (open = true)"' .
      ' @pointerleave="if ($event.pointerType !== \'touch\' && !$el.contains(document.activeElement)) open = false"' .
      ' @click.outside="open = false"' .
      ' @focusout="$event.currentTarget.contains($event.relatedTarget) || (open = false)"' .
      ' @keydown.escape="if (open) { open = false; $refs.toggle.focus(); $event.stopPropagation() }">' .
      "\n";
  }

  private function link($item, string $title, string $class, bool $current, string $prefix = ""): string
  {
    $attrs = [
      "href" => !empty($item->url) ? esc_url($item->url) : "#",
      "class" => $class,
    ];

    if (!empty($item->target)) {
      $attrs["target"] = $item->target;
    }

    if (!empty($item->attr_title)) {
      $attrs["title"] = $item->attr_title;
    }

    if (!empty($item->xfn) || ($item->target ?? "") === "_blank") {
      $attrs["rel"] = trim(($item->xfn ?? "") . " noopener");
    }

    if ($current) {
      $attrs["aria-current"] = "page";
    }

    return "<a" . $this->attrs($attrs) . ">" . $prefix . esc_html($title) . "</a>\n";
  }

  private function toggle_button(string $title, string $controls, int $depth): string
  {
    $class = $depth === 0 ? self::BUTTON_D0 : self::BUTTON_D1;
    $icon = $depth === 0 ? $this->chevron_down() : $this->chevron_right();
    $label = $depth === 0 ? esc_html($title) : "";
    $aria_label = $depth === 0 ? "" : ' aria-label="' . esc_attr(sprintf("Toggle %s submenu", $title)) . '"';

    return '<button x-ref="toggle" type="button" class="' .
      esc_attr($class) .
      '" :aria-expanded="open.toString()" aria-controls="' .
      esc_attr($controls) .
      '"' .
      $aria_label .
      ' @click="open = !open">' .
      $label .
      $icon .
      "</button>\n";
  }

  private function title($item, $args, int $depth): string
  {
    $title = apply_filters("the_title", $item->title, $item->ID);
    return apply_filters("nav_menu_item_title", $title, $item, $args, $depth);
  }

  private function attrs(array $attrs): string
  {
    $html = "";

    foreach ($attrs as $name => $value) {
      if ($value !== "") {
        $html .= sprintf(' %s="%s"', esc_attr($name), esc_attr($value));
      }
    }

    return $html;
  }

  private function chevron_down(): string
  {
    return '<svg class="' .
      esc_attr(self::CHEVRON_DOWN) .
      '" :style="open ? \'transform: rotate(180deg)\' : \'\'" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">' .
      '<path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>' .
      "</svg>";
  }

  private function chevron_right(): string
  {
    return '<svg class="' .
      esc_attr(self::CHEVRON_RIGHT) .
      '" :style="open ? \'transform: rotate(90deg)\' : \'\'" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">' .
      '<path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 011.06 0l4.25 4.25a.75.75 0 010 1.06l-4.25 4.25a.75.75 0 01-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 010-1.06z" clip-rule="evenodd"/>' .
      "</svg>";
  }

  private function active_bar(): string
  {
    return '<span class="' . esc_attr(self::ACTIVE_BAR_D1) . '" aria-hidden="true"></span>';
  }
}
