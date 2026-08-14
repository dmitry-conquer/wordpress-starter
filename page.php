<?php get_header(); ?>

<main id="main-content" class="relative isolate flex grow items-center overflow-hidden bg-stone-50 px-6 py-24">
  <div class="absolute top-0 left-1/2 -z-10 size-96 -translate-x-1/2 rounded-full bg-blue-200/40 blur-3xl" aria-hidden="true"></div>

  <div class="mx-auto w-full max-w-3xl text-center">
    <p class="mb-6 text-xs font-semibold tracking-[0.25em] text-stone-500 uppercase">Coming soon</p>

    <p class="mb-4 text-sm text-stone-500">
      <?php echo esc_html(get_bloginfo("name")); ?>
    </p>

    <h1 class="text-4xl font-semibold tracking-tight text-stone-950 sm:text-6xl">Something new is taking shape.</h1>

    <p class="mx-auto mt-6 max-w-xl text-base leading-7 text-stone-600 sm:text-lg">We’re building a thoughtful digital experience. Please check back soon.</p>
  </div>
</main>

<?php get_footer();
