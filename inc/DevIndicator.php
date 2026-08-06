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

    $configured_url = Assets::dev_server();
    $dev_enabled = $configured_url !== "";
    $dev_server = $dev_enabled ? $configured_url : self::DEFAULT_DEV_SERVER;
    ?>
    <style>
      #theme-dev-indicator {
        --indicator-color: #64748b;
        position: fixed;
        right: 16px;
        bottom: 16px;
        z-index: 2147483647;
        width: min(440px, calc(100vw - 32px));
        padding: 12px 40px 12px 14px;
        border: 1px solid #e7e5e4;
        border-radius: 8px;
        background: #fff;
        color: #1c1917;
        box-shadow: 0 8px 24px rgb(0 0 0 / 12%);
        font: 12px/1.4 ui-sans-serif, system-ui, sans-serif;
      }

      #theme-dev-indicator[hidden] {
        display: none;
      }

      #theme-dev-indicator[data-status="active"] {
        --indicator-color: #16a34a;
      }

      #theme-dev-indicator[data-status="server-only"] {
        --indicator-color: #d97706;
      }

      #theme-dev-indicator[data-status="config-only"] {
        --indicator-color: #dc2626;
      }

      #theme-dev-indicator[data-status="disabled"] {
        --indicator-color: #64748b;
      }

      #theme-dev-indicator[data-view="collapsed"] {
        width: auto;
        padding: 8px 42px 8px 10px;
        border-radius: 999px;
      }

      #theme-dev-indicator[data-view="collapsed"] ul,
      #theme-dev-indicator[data-view="collapsed"] [data-dev-indicator-help],
      #theme-dev-indicator[data-view="collapsed"] [data-dev-indicator-title] {
        display: none;
      }

      #theme-dev-indicator [data-dev-indicator-short] {
        display: none;
      }

      #theme-dev-indicator[data-view="collapsed"] [data-dev-indicator-short] {
        display: inline;
      }

      #theme-dev-indicator[data-view="collapsed"] strong {
        margin: 0;
      }

      #theme-dev-indicator strong {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 2px;
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

      #theme-dev-indicator ul {
        display: grid;
        gap: 4px;
        margin: 8px 0 0;
        padding: 0;
        list-style: none;
      }

      #theme-dev-indicator li {
        display: grid;
        grid-template-columns: 8px 1fr auto;
        gap: 8px;
        align-items: center;
      }

      #theme-dev-indicator li::before {
        width: 6px;
        height: 6px;
        border-radius: 999px;
        background: #dc2626;
        content: "";
      }

      #theme-dev-indicator li[data-enabled="true"]::before {
        background: #16a34a;
      }

      #theme-dev-indicator [data-dev-value] {
        color: #78716c;
      }

      #theme-dev-indicator details {
        margin-top: 10px;
        border-top: 1px solid #e7e5e4;
        padding-top: 8px;
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

      #theme-dev-indicator [data-dev-indicator-config] {
        display: block;
        overflow-x: auto;
        white-space: nowrap;
      }

      #theme-dev-indicator [data-dev-indicator-collapse] {
        position: absolute;
        top: 8px;
        right: 8px;
        display: grid;
        width: 26px;
        height: 26px;
        padding: 0;
        border: 0;
        border-radius: 6px;
        background: transparent;
        color: #a8a29e;
        cursor: pointer;
        font: 18px/1 ui-sans-serif, system-ui, sans-serif;
        place-items: center;
      }

      #theme-dev-indicator[data-view="collapsed"] [data-dev-indicator-collapse] {
        top: 50%;
        transform: translateY(-50%);
      }

      #theme-dev-indicator [data-dev-indicator-collapse]:hover {
        background: #f5f5f4;
        color: #57534e;
      }

      #theme-dev-indicator [data-dev-indicator-collapse]:focus-visible {
        outline: 2px solid var(--indicator-color);
        outline-offset: 1px;
      }
    </style>

    <aside id="theme-dev-indicator" data-status="checking" data-view="expanded" aria-live="polite" hidden>
      <strong><span data-dev-indicator-title>Development: checking</span><span data-dev-indicator-short>Dev</span></strong>
      <ul>
        <li data-dev-status="vite" data-enabled="false">
          <span>Vite server</span>
          <span data-dev-value>Disabled</span>
        </li>
        <li data-dev-status="config" data-enabled="<?php echo $dev_enabled ? "true" : "false"; ?>">
          <span>Theme assets</span>
          <span data-dev-value><?php echo $dev_enabled ? "Vite" : "Build"; ?></span>
        </li>
      </ul>
      <details data-dev-indicator-help>
        <summary>Setup help</summary>
        <div data-dev-indicator-help-content>
          <p><b>Vite server:</b> run the project’s <code>dev</code> script with your package manager.</p>
          <p><b>Theme assets:</b> the source theme switches to Vite automatically when the server is running.</p>
          <p>For a custom Vite URL, update the URL in <code>inc/Assets.php</code> and the Vite <code>server</code> settings.</p>
        </div>
      </details>
      <button type="button" data-dev-indicator-collapse aria-label="Collapse development notice" aria-expanded="true">−</button>
    </aside>

    <script>
      (() => {
        const indicator = document.querySelector("#theme-dev-indicator");
        const title = indicator?.querySelector("[data-dev-indicator-title]");
        const collapseButton = indicator?.querySelector("[data-dev-indicator-collapse]");
        const statuses = {
          vite: indicator?.querySelector('[data-dev-status="vite"]'),
          config: indicator?.querySelector('[data-dev-status="config"]'),
        };
        const devEnabled = <?php echo wp_json_encode($dev_enabled); ?>;
        const devServer = <?php echo wp_json_encode($dev_server); ?>;
        const storageKey = "wp-theme-starter-dev-indicator";

        if (!indicator || !title || !collapseButton || Object.values(statuses).some(status => !status)) return;

        const readView = () => {
          try {
            return window.localStorage.getItem(storageKey);
          } catch {
            return null;
          }
        };

        const saveView = view => {
          try {
            window.localStorage.setItem(storageKey, view);
          } catch {}
        };

        const savedView = readView();
        const setView = collapsed => {
          indicator.dataset.view = collapsed ? "collapsed" : "expanded";
          collapseButton.textContent = collapsed ? "+" : "−";
          collapseButton.setAttribute("aria-label", collapsed ? "Expand development notice" : "Collapse development notice");
          collapseButton.setAttribute("aria-expanded", String(!collapsed));
        };

        setView(savedView === "collapsed");
        indicator.hidden = false;

        collapseButton.addEventListener("click", () => {
          const collapsed = indicator.dataset.view !== "collapsed";
          setView(collapsed);
          saveView(collapsed ? "collapsed" : "expanded");
        });

        const setStatus = (name, enabled) => {
          const status = statuses[name];
          status.dataset.enabled = String(enabled);
          status.querySelector("[data-dev-value]").textContent = enabled ? "Vite" : "Build";
        };

        const update = (status, heading, running) => {
          indicator.dataset.status = status;
          title.textContent = heading;
          setStatus("vite", running);
          setStatus("config", devEnabled);
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

          if (devEnabled && running) {
            update("active", "Development: active", running);
          } else if (devEnabled) {
            update("config-only", "Development: Vite offline", running);
          } else if (running) {
            update("server-only", "Development: build assets", running);
          } else {
            update("disabled", "Development: disabled", running);
          }
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
