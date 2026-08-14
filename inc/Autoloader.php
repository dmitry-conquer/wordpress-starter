<?php
namespace SiteTheme;

if (!defined("ABSPATH")) {
  exit();
}

final class Autoloader
{
  public static function register()
  {
    spl_autoload_register(function ($class) {
      $prefix = "SiteTheme\\";
      if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
      }
      $base_path = get_template_directory() . "/inc/";
      $parsed_class = substr($class, strlen($prefix));
      $file = $base_path . str_replace("\\", "/", $parsed_class) . ".php";
      if (file_exists($file)) {
        require $file;
      }
    });
  }
}
