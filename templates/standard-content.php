<?php

/**
 * Template Name: Standard Content
 */

get_header(); ?>

<main id="main-content" class="grow">
  <?php while (have_posts()):
    the_post(); ?>
    <article class="mx-auto w-full max-w-3xl px-6 py-16 sm:py-20 lg:py-24">
      <header class="mb-10">
        <h1 class="text-4xl font-semibold tracking-tight text-stone-950 sm:text-5xl">
          <?php the_title(); ?>
        </h1>
      </header>

      <div class="prose">
        <?php the_content(); ?>
      </div>
    </article>
  <?php
  endwhile; ?>
</main>

<?php get_footer();
