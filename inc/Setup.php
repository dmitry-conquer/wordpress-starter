<?php

namespace WP_Theme_Starter;

if (!defined("ABSPATH")) {
  exit();
}

final class Setup
{
  public static function register()
  {
    add_action("after_setup_theme", [self::class, "setup_theme"]);
    add_action("wp_enqueue_scripts", [self::class, "remove_block_css"]);
    add_action("login_head", [self::class, "custom_login_styles"]);
    add_action("login_headerurl", [self::class, "custom_login_logo_url"]);
    add_action("login_headertext", [self::class, "custom_login_logo_url_title"]);
    add_filter("wp_img_tag_add_auto_sizes", "__return_false");
    self::disable_comments();
  }

  public static function remove_block_css()
  {
    wp_dequeue_style("wp-block-library");
    wp_dequeue_style("wp-block-library-theme");
    wp_dequeue_style("wc-block-style");
    wp_dequeue_style("storefront-gutenberg-blocks");
  }

  public static function setup_theme()
  {
    add_theme_support("post-thumbnails");
    add_theme_support("title-tag");
    add_theme_support("html5", ["search-form", "comment-form", "comment-list", "gallery", "caption", "style", "script"]);
    add_theme_support("responsive-embeds");
    add_theme_support("custom-logo", [
      "height" => 100,
      "width" => 400,
      "flex-height" => true,
      "flex-width" => true,
    ]);
  }

  public static function custom_login_styles()
  {
    echo '<style>
			body.login {
					background: linear-gradient(357deg, rgb(70 182 191 / 67%) 0%, rgb(255 255 255 / 82%) 100%);
			}
			.login h1 a {
					background-image: url("");
					background-size: contain;
					width: 100%;
					height: 80px;
			}
			.login form {
					border-radius: 10px;
					box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
			}
	</style>';
  }

  public static function custom_login_logo_url()
  {
    return home_url();
  }

  public static function custom_login_logo_url_title()
  {
    return get_bloginfo("name");
  }

  public static function disable_comments()
  {
    // Disable comments and trackbacks for all post types.
    add_action(
      "init",
      function () {
        foreach (get_post_types([], "names") as $post_type) {
          remove_post_type_support($post_type, "comments");
          remove_post_type_support($post_type, "trackbacks");
        }

        remove_action("admin_bar_menu", "wp_admin_bar_comments_menu", 60);
      },
      100,
    );

    // Close comments and pingbacks on the frontend.
    add_filter("comments_open", "__return_false", 20);
    add_filter("pings_open", "__return_false", 20);

    // Hide existing comments without deleting them.
    add_filter("comments_array", "__return_empty_array", 10);

    // Remove comments from the WordPress administration interface.
    add_action(
      "admin_menu",
      function () {
        remove_menu_page("edit-comments.php");
      },
      999,
    );

    add_action("wp_dashboard_setup", function () {
      remove_meta_box("dashboard_recent_comments", "dashboard", "normal");
    });

    // Redirect direct access to the comments administration page.
    add_action("admin_init", function () {
      global $pagenow;

      if ("edit-comments.php" === $pagenow) {
        wp_safe_redirect(admin_url());
        exit();
      }
    });
  }
}
