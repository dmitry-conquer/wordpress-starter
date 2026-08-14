<?php

namespace SiteTheme;

if (!defined("ABSPATH")) {
  exit();
}

final class Cleanup
{
  public static function register(): void
  {
    add_filter("use_block_editor_for_post_type", "__return_false", 100);
    add_filter("use_widgets_block_editor", "__return_false", 100);
    add_filter("gutenberg_can_edit_post_type", "__return_false", 100);

    self::remove_block_style_hooks();
    add_action("wp_enqueue_scripts", [self::class, "remove_default_styles"], 100);
    add_filter("wp_img_tag_add_auto_sizes", "__return_false");

    add_action("init", [self::class, "disable_comment_support"], 100);
    add_filter("comments_open", "__return_false", 20);
    add_filter("pings_open", "__return_false", 20);
    add_filter("comments_array", "__return_empty_array", 10);
    add_action("admin_menu", [self::class, "remove_comments_menu"], 999);
    add_action("wp_dashboard_setup", [self::class, "remove_comments_dashboard_widget"]);
    add_action("admin_init", [self::class, "redirect_comments_admin"]);
  }

  public static function remove_default_styles(): void
  {
    $styles = [
      "wp-block-library",
      "wp-block-library-theme",
      "global-styles",
      "classic-theme-styles",
      "core-block-supports",
      "wp-global-styles-placeholder",
      "wc-block-style",
      "storefront-gutenberg-blocks",
    ];

    foreach ($styles as $style) {
      wp_dequeue_style($style);
    }
  }

  public static function disable_comment_support(): void
  {
    foreach (get_post_types([], "names") as $post_type) {
      remove_post_type_support($post_type, "comments");
      remove_post_type_support($post_type, "trackbacks");
    }

    remove_action("admin_bar_menu", "wp_admin_bar_comments_menu", 60);
  }

  public static function remove_comments_menu(): void
  {
    remove_menu_page("edit-comments.php");
  }

  public static function remove_comments_dashboard_widget(): void
  {
    remove_meta_box("dashboard_recent_comments", "dashboard", "normal");
  }

  public static function redirect_comments_admin(): void
  {
    global $pagenow;

    if ("edit-comments.php" === $pagenow) {
      wp_safe_redirect(admin_url());
      exit();
    }
  }

  private static function remove_block_style_hooks(): void
  {
    remove_action("wp_enqueue_scripts", "wp_common_block_scripts_and_styles");
    remove_action("wp_enqueue_scripts", "wp_enqueue_classic_theme_styles");
    remove_action("enqueue_block_assets", "wp_enqueue_classic_theme_styles");
    remove_action("enqueue_block_assets", "wp_enqueue_registered_block_scripts_and_styles");
    remove_action("enqueue_block_assets", "enqueue_block_styles_assets", 30);
    remove_action("wp_enqueue_scripts", "wp_enqueue_global_styles");
    remove_action("wp_footer", "wp_enqueue_global_styles", 1);
    remove_action("wp_enqueue_scripts", "wp_enqueue_stored_styles");
    remove_action("wp_footer", "wp_enqueue_stored_styles", 1);
    remove_action("wp_default_styles", "wp_load_classic_theme_block_styles_on_demand", 0);
    remove_action("wp_body_open", "wp_global_styles_render_svg_filters");
    remove_action("wp_enqueue_scripts", ["WP_Duotone", "output_block_styles"], 9);
    remove_action("wp_enqueue_scripts", ["WP_Duotone", "output_global_styles"], 11);
    remove_action("wp_footer", ["WP_Duotone", "output_footer_assets"], 10);
  }
}
