# WP Theme Starter

WordPress starter theme with Vite, Tailwind CSS, TypeScript and Alpine.js.

## Start development

Install dependencies:

```bash
pnpm install
```

Rename the starter before development. Enter a theme name of up to 80 Latin letters with single spaces, for example `Awesome Theme`. The script derives `awesome-theme`, `Awesome_Theme`, and `AWESOME_THEME`, then updates only its explicit file allowlist:

```bash
pnpm rename-theme
```

To preview the files it would update without changing anything:

```bash
pnpm rename-theme -- --dry-run
```

Start Vite and open the WordPress site:

```bash
pnpm dev
```

Edit files here:

- PHP markup: `template-parts/` and theme PHP files
- Styles: `src/styles/main.css`
- Scripts: `src/scripts/main.ts`

## Build

```bash
pnpm build
```

This checks TypeScript, builds production CSS and JavaScript, and creates the ready-to-use WordPress theme at:

```text
release/wordpress-starter/
```

All working source files remain in the project root. The complete production theme that can be installed or deployed is generated in the `release/` directory. Its default directory name comes from the `name` field in `package.json`.

The packaging script copies the complete theme and excludes development-only files such as `src/`, `node_modules/`, editor settings, build configuration, and package-manager files. New production files and directories, including additional WordPress templates, `theme.json`, `languages/`, `patterns/`, or WooCommerce overrides, are therefore included automatically.

To create the release with a custom directory name, pass it as a build argument:

```bash
pnpm build --custom-name
```

This creates the production theme at `release/custom-name/`.

## How it works

WordPress always renders the PHP templates.

During development, `pnpm dev` starts Vite on `http://localhost:5173`. When the working source theme detects that server, it loads `src/scripts/main.ts` from Vite automatically. The script imports `src/styles/main.css`, so Vite sends both JavaScript and styles directly to the browser and updates them with hot reload. When Vite is not running, it falls back to `assets/build/`.

For a non-default Vite address, update the URL in `inc/Assets.php` and the Vite `server` settings in `vite.config.js`.

For production, run `pnpm build`. Vite compiles the assets into `assets/build/`, and WordPress loads the generated CSS and JavaScript through the Vite manifest. The ready-to-use theme in `release/wordpress-starter/` does not contain the source-only Vite files, so it always uses the production build.
