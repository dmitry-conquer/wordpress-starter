<footer class="mt-auto border-t border-stone-200 bg-stone-50">
  <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-8 text-sm text-stone-500 sm:flex-row sm:items-center sm:justify-between lg:px-8">
    <p>&copy; <?php echo esc_html(wp_date("Y")); ?> <?php echo esc_html(get_bloginfo("name")); ?></p>
    <?php wp_nav_menu([
      "theme_location" => "footer_menu",
      "container" => "nav",
      "container_aria_label" => "Footer navigation",
      "menu_class" => "flex flex-wrap gap-x-5 gap-y-2",
      "fallback_cb" => false,
      "depth" => 1,
    ]); ?>
  </div>
</footer>
