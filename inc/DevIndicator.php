<?php

namespace WP_Theme_Starter;

if (!defined("ABSPATH")) {
  exit();
}

final class DevIndicator
{
  private const DEFAULT_DEV_SERVER = "http://localhost:5173";

  public static function register(): void
  {
    add_action("wp_footer", [self::class, "render"], PHP_INT_MAX);
  }

  public static function render(): void
  {
    if (!self::should_render()) {
      return;
    }

    $dev_server = Assets::dev_server() ?: self::DEFAULT_DEV_SERVER;
    ?>
    <style>
      #theme-dev-indicator {
        --indicator-color: #dc2626;
        position: fixed;
        right: 16px;
        bottom: 16px;
        z-index: 2147483647;
        width: min(360px, calc(100vw - 32px));
        padding: 12px 14px;
        border: 1px solid #e7e5e4;
        border-radius: 8px;
        background: #fff;
        color: #1c1917;
        box-shadow: 0 8px 24px rgb(0 0 0 / 12%);
        font: 12px/1.4 ui-sans-serif, system-ui, sans-serif;
      }

      #theme-dev-indicator[data-status="active"] {
        --indicator-color: #16a34a;
      }

      #theme-dev-indicator[data-status="disabled"] {
        --indicator-color: #dc2626;
      }

      #theme-dev-indicator[data-collapsed="true"] {
        width: auto;
        padding: 6px 8px;
      }

      #theme-dev-indicator strong {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
      }

      #theme-dev-indicator strong::before {
        width: 8px;
        height: 8px;
        flex: 0 0 auto;
        border-radius: 999px;
        background: var(--indicator-color);
        content: "";
      }

      #theme-dev-indicator [data-dev-indicator-short-title] {
        display: none;
      }

      #theme-dev-indicator[data-collapsed="true"] [data-dev-indicator-full-title] {
        display: none;
      }

      #theme-dev-indicator[data-collapsed="true"] [data-dev-indicator-short-title] {
        display: inline;
      }

      #theme-dev-indicator details {
        margin-top: 10px;
        border-top: 1px solid #e7e5e4;
        padding-top: 8px;
      }

      #theme-dev-indicator[data-status="active"] details,
      #theme-dev-indicator[data-collapsed="true"] details {
        display: none;
      }

      #theme-dev-indicator [data-dev-indicator-collapse] {
        display: inline-grid;
        width: 18px;
        height: 18px;
        margin-left: auto;
        place-items: center;
        border: 0;
        border-radius: 3px;
        background: transparent;
        color: #a8a29e;
        cursor: pointer;
        font: inherit;
      }

      #theme-dev-indicator [data-dev-indicator-collapse]::before {
        content: "−";
        font-size: 16px;
        line-height: 1;
      }

      #theme-dev-indicator[data-collapsed="true"] [data-dev-indicator-collapse]::before {
        content: "+";
      }

      #theme-dev-indicator[data-collapsed="true"] [data-dev-indicator-collapse] {
        position: absolute;
        inset: 0;
        width: auto;
        height: auto;
        color: transparent;
      }

      #theme-dev-indicator[data-collapsed="true"] [data-dev-indicator-collapse]::before {
        display: none;
      }

      #theme-dev-indicator[data-collapsed="true"] [data-dev-indicator-collapse]:hover {
        background: transparent;
      }

      #theme-dev-indicator [data-dev-indicator-collapse]:hover {
        background: #f5f5f4;
      }

      #theme-dev-indicator [data-dev-indicator-collapse]:focus-visible {
        outline: 2px solid var(--indicator-color);
        outline-offset: 2px;
      }

      #theme-dev-indicator [data-dev-indicator-collapse]:disabled {
        cursor: default;
      }

      #theme-dev-indicator summary {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        border-radius: 4px;
        color: #57534e;
        cursor: pointer;
        font-weight: 600;
        list-style: none;
      }

      #theme-dev-indicator summary::-webkit-details-marker {
        display: none;
      }

      #theme-dev-indicator summary::after {
        width: 7px;
        height: 7px;
        flex: 0 0 auto;
        border-right: 1.5px solid #a8a29e;
        border-bottom: 1.5px solid #a8a29e;
        color: #a8a29e;
        content: "";
        transform: rotate(45deg);
        transition: transform 150ms ease;
      }

      #theme-dev-indicator details[open] summary::after {
        transform: rotate(225deg);
      }

      #theme-dev-indicator summary:focus-visible {
        outline: 2px solid var(--indicator-color);
        outline-offset: 2px;
      }

      #theme-dev-indicator [data-dev-indicator-help-content] {
        display: grid;
        gap: 8px;
        margin-top: 8px;
        color: #78716c;
      }

      #theme-dev-indicator [data-dev-indicator-help-content] p {
        margin: 0;
      }

      #theme-dev-indicator [data-dev-indicator-help-content] b {
        color: #44403c;
      }

      #theme-dev-indicator code {
        border-radius: 3px;
        background: #f5f5f4;
        padding: 1px 3px;
        color: #44403c;
        font: 11px/1.4 ui-monospace, SFMono-Regular, Consolas, monospace;
        overflow-wrap: anywhere;
      }

    </style>

    <aside id="theme-dev-indicator" data-status="disabled" aria-live="polite">
      <strong>
        <span data-dev-indicator-title><span data-dev-indicator-full-title>Development: disabled</span><span data-dev-indicator-short-title>Dev</span></span>
        <button type="button" data-dev-indicator-collapse aria-label="Collapse development status" aria-expanded="true"></button>
      </strong>
      <details data-dev-indicator-help>
        <summary>Setup help</summary>
        <div data-dev-indicator-help-content>
          <p><b>Vite server:</b> run the project’s <code>dev</code> script with your package manager.</p>
          <p><b>Theme assets:</b> the source theme switches to Vite automatically when the server is running.</p>
          <p>For a custom Vite URL, update the URL in <code>inc/Assets.php</code> and the Vite <code>server</code> settings.</p>
        </div>
      </details>
    </aside>

    <script>
      (() => {
        const indicator = document.querySelector("#theme-dev-indicator");
        const title = indicator?.querySelector("[data-dev-indicator-full-title]");
        const collapseButton = indicator?.querySelector("[data-dev-indicator-collapse]");
        const devServer = <?php echo wp_json_encode($dev_server); ?>;
        const collapsedStorageKey = "theme-dev-indicator-collapsed";
        let savedCollapsed = false;

        if (!indicator || !title || !collapseButton) return;

        const setCollapsed = (collapsed, persist = true) => {
          indicator.dataset.collapsed = String(collapsed);
          collapseButton.setAttribute("aria-expanded", String(!collapsed));
          collapseButton.setAttribute("aria-label", collapsed ? "Expand development status" : "Collapse development status");

          if (!persist) return;

          savedCollapsed = collapsed;
          try {
            window.localStorage.setItem(collapsedStorageKey, String(collapsed));
          } catch {
            // The indicator still works when storage is unavailable.
          }
        };

        try {
          savedCollapsed = window.localStorage.getItem(collapsedStorageKey) === "true";
        } catch {
          savedCollapsed = false;
        }
        setCollapsed(savedCollapsed, false);

        collapseButton.addEventListener("click", () => {
          if (indicator.dataset.locked === "true") return;
          setCollapsed(indicator.dataset.collapsed !== "true");
        });

        const update = running => {
          const status = running ? "active" : "disabled";
          indicator.dataset.status = status;
          title.textContent = running ? "Development: active" : "Development: disabled";
          indicator.dataset.locked = String(running);
          collapseButton.disabled = running;
          setCollapsed(running || savedCollapsed, false);
          if (running) {
            collapseButton.setAttribute("aria-label", "Development status is active");
          }
        };

        const checkServer = async () => {
          const controller = new AbortController();
          const timeout = window.setTimeout(() => controller.abort(), 1500);
          let running = false;

          try {
            const response = await fetch(`${devServer}/__theme-dev-status`, {
              cache: "no-store",
              signal: controller.signal,
            });
            const data = response.ok ? await response.json() : null;
            running = data?.service === "theme-vite-dev-server" && data?.status === "ready";
          } catch {
            running = false;
          } finally {
            window.clearTimeout(timeout);
          }

          update(running);
        };

        checkServer();
        window.setInterval(checkServer, 5000);
      })();
    </script>
    <?php
  }

  private static function should_render(): bool
  {
    if (!current_user_can("manage_options")) {
      return false;
    }

    // These source-only files are not copied into the packaged release theme.
    // Their presence explicitly distinguishes the development starter from a
    // packaged production theme, regardless of WordPress environment settings.
    return is_readable(WP_THEME_STARTER_DIR . "/package.json") && is_readable(WP_THEME_STARTER_DIR . "/vite.config.js");
  }
}
