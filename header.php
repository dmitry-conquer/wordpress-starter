<!doctype html>
<html <?php language_attributes(); ?> class="scrollbar-gutter-stable">

<head>
  <meta charset="<?php bloginfo("charset"); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
  <?php wp_body_open(); ?>
  <div class="flex min-h-screen w-full flex-col overflow-clip">
    <a class="sr-only focus:fixed focus:top-4 focus:left-4 focus:z-100 focus:not-sr-only focus:rounded-md focus:bg-white focus:px-4 focus:py-2 focus:text-stone-900 focus:shadow-lg" href="#main-content">Skip to content</a>

    <?php get_template_part("template-parts/header", "default"); ?>
