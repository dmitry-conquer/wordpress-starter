export const TEMPLATE_PACKAGE_NAME = "wordpress-starter";

export const replacements = [
  {
    token: "WP_Theme_Starter",
    value: "namespace",
    files: [
      "functions.php",
      "inc/Assets.php",
      "inc/Autoloader.php",
      "inc/DesktopMenuWalker.php",
      "inc/DevIndicator.php",
      "inc/Menu.php",
      "inc/MobileMenuWalker.php",
      "inc/Setup.php",
      "inc/Shortcodes.php",
      "inc/Utils.php",
      "template-parts/header-default.php",
    ],
  },
  {
    token: "WP_THEME_STARTER",
    value: "constantPrefix",
    files: ["functions.php", "inc/Assets.php", "inc/Autoloader.php", "inc/DevIndicator.php"],
  },
  {
    token: "wp-theme-starter",
    value: "slug",
    files: ["inc/Assets.php"],
  },
  {
    token: "WP Theme Starter",
    value: "name",
    files: ["README.md", "inc/Assets.php", "style.css"],
  },
  {
    token: "WP Starter Theme",
    value: "name",
    files: ["functions.php"],
  },
  {
    token: TEMPLATE_PACKAGE_NAME,
    value: "slug",
    files: ["README.md", "package.json"],
  },
];
