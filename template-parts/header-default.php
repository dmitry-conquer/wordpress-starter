<div x-data="{ open: false }" @keydown.escape.window="open = false">
  <header class="sticky top-0 z-30 flex w-full items-center justify-between gap-8 border-b border-stone-100 bg-white px-4 py-3 lg:px-8">
    <a href="#" aria-label="Back to home" class="shrink-0"> Logo </a>

    <nav class="hidden lg:flex" aria-label="Main navigation">
      <?php wp_nav_menu([
        "theme_location" => "header_menu",
        "container" => false,
        "menu_class" => "flex items-center",
        "fallback_cb" => false,
        "depth" => 3,
        "walker" => new \SiteTheme\DesktopMenuWalker(),
      ]); ?>
    </nav>

    <div class="flex shrink-0 items-center gap-3">
      <a
        href="#"
        class="hidden items-center gap-1.5 rounded-xl bg-stone-900 px-5 py-2.5 text-sm font-medium text-white transition-colors hover:bg-stone-800 focus-visible:ring-2 focus-visible:ring-stone-900 focus-visible:ring-offset-2 focus-visible:outline-none lg:flex"
      >
        Discuss a Project
        <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
          <path
            fill-rule="evenodd"
            d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.29a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z"
            clip-rule="evenodd"
          />
        </svg>
      </a>

      <button
        type="button"
        class="flex h-11 w-11 flex-col items-center justify-center gap-1.5 rounded-xl text-stone-700 transition-colors hover:bg-stone-100 focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:outline-none lg:hidden"
        :aria-expanded="open.toString()"
        aria-controls="mobile-menu"
        aria-label="Open mobile menu"
        @click="open = true"
      >
        <svg aria-hidden="true" class="size-10" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path fill="currentColor" d="M4 17.27v-1h16v1zm0-4.77v-1h16v1zm0-4.77v-1h16v1z" />
        </svg>
      </button>
    </div>
  </header>

  <div
    x-cloak
    class="fixed inset-0 z-40 bg-black transition-opacity duration-300"
    :class="open ? 'opacity-50 pointer-events-auto' : 'opacity-0 pointer-events-none'"
    aria-hidden="true"
    @click="open = false"
  ></div>

  <div
    id="mobile-menu"
    x-cloak
    class="fixed top-0 right-0 bottom-0 z-50 flex w-full max-w-sm flex-col bg-white shadow-2xl transition-transform duration-300 ease-in-out"
    :style="open ? 'transform: translateX(0)' : 'transform: translateX(100%)'"
    :aria-hidden="(!open).toString()"
    :inert="!open"
    role="dialog"
    aria-modal="true"
    aria-label="Mobile menu"
    x-trap.noscroll="open"
  >
    <div class="flex h-16 shrink-0 items-center justify-between border-b border-stone-100 px-5">
      <span class="font-display text-lg font-medium text-stone-900" aria-hidden="true">Logo</span>

      <button
        type="button"
        class="flex h-9 w-9 items-center justify-center rounded-lg border border-stone-200 text-stone-500 transition-colors hover:border-stone-300 hover:bg-stone-50 hover:text-stone-700 focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:outline-none"
        aria-label="Close mobile menu"
        @click="open = false"
      >
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" aria-hidden="true">
          <path d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>

    <nav class="flex-1 overflow-y-auto overscroll-contain py-2" aria-label="Mobile navigation">
      <?php wp_nav_menu([
        "theme_location" => "header_menu",
        "container" => false,
        "menu_class" => "w-full",
        "fallback_cb" => false,
        "depth" => 3,
        "walker" => new \SiteTheme\MobileMenuWalker(),
      ]); ?>
    </nav>

    <div class="shrink-0 border-t border-stone-100 p-4">
      <a
        href="#"
        class="flex w-full items-center justify-center rounded-xl bg-stone-900 px-4 py-3 text-sm font-medium text-white transition-colors hover:bg-stone-800 focus-visible:ring-2 focus-visible:ring-stone-900 focus-visible:ring-offset-2 focus-visible:outline-none"
      >
        Discuss a Project →
      </a>
    </div>
  </div>
</div>
