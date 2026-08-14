# Site Theme

Custom WordPress theme with Vite, Tailwind CSS, TypeScript and Alpine.js.

## Requirements

- A working WordPress installation
- PHP 8.2 or newer
- Node.js 22
- pnpm 11.10.0

## Start development

Install dependencies:

```bash
pnpm install
```

Start Vite and open the WordPress site:

```bash
pnpm dev
```

Edit files here:

- Page templates: `templates/`
- Reusable PHP markup: `template-parts/`
- Theme functionality: `inc/`
- Styles: `src/styles/main.css`
- Scripts: `src/scripts/main.ts`
- ACF field definitions: `acf-json/` when present

## Build

```bash
pnpm build
```

This checks TypeScript, builds production CSS and JavaScript, and creates the ready-to-use WordPress theme at:

```text
release/site-theme/
```

All working source files remain in the project root. The complete production theme that can be installed or deployed is generated in the `release/` directory. Its default directory name comes from the `name` field in `package.json`.

The packaging script copies the complete theme and excludes development-only files such as `src/`, `node_modules/`, editor settings, build configuration, and package-manager files. New production files and directories, including additional WordPress templates, `theme.json`, `languages/`, `patterns/`, or WooCommerce overrides, are therefore included automatically.

Run the complete local verification before pushing changes:

```bash
pnpm check
```

This runs ESLint, checks formatting, checks TypeScript, builds the assets, and recreates the production theme.

## How it works

WordPress always renders the PHP templates.

During development, `pnpm dev` starts Vite on `http://localhost:5173`. When the working source theme detects that server, it loads `src/scripts/main.ts` from Vite automatically. The script imports `src/styles/main.css`, so Vite sends both JavaScript and styles directly to the browser and updates them with hot reload. When Vite is not running, it falls back to `assets/build/`.

After a fresh checkout, either keep `pnpm dev` running or run `pnpm build` before opening the site. Without the Vite server or an existing production build, the theme has no compiled CSS or JavaScript to load.

For a non-default Vite address, update the URL in `inc/Assets.php` and the Vite `server` settings in `vite.config.js`.

For production, run `pnpm build`. Vite compiles the assets into `assets/build/`, and WordPress loads the generated CSS and JavaScript through the Vite manifest. The ready-to-use theme in `release/site-theme/` does not contain the source-only Vite files, so it always uses the production build.

## Production deployment

Production deployments run automatically in GitHub Actions after every push to `main`. Developers do not deploy themes from their computers.

The workflow installs the locked dependencies, runs ESLint and the formatting check, builds the production theme, and then synchronizes only the generated theme directory over SFTP with `lftp`:

```text
release/site-theme/ → DEPLOY_PATH
```

It does not modify WordPress core, plugins, uploads, the database, or other themes. Files inside the deployed theme directory must not be edited manually: the next deployment makes that directory match the current `main` commit.

### One-time GitHub setup

Create a `production` environment in the repository settings, then add these Actions secrets:

```text
DEPLOY_HOST      Server hostname or IP address
DEPLOY_PORT      SFTP/SSH port, usually 22 or 2222
DEPLOY_USER      SSH username
DEPLOY_PASSWORD  SSH password
```

Also add the `DEPLOY_PATH` Actions variable. It must be the absolute path visible to the SFTP account and point directly to one theme directory inside `wp-content/themes`. Both chrooted and full server paths are supported, for example:

```text
/wp-content/themes/client-theme
/var/www/site/public/wp-content/themes/client-theme
```

The workflow rejects empty, relative, malformed, or broader paths such as `/wp-content/themes`. The SFTP user only needs write access to the configured theme directory. `lftp` is installed on the GitHub Actions runner; neither `lftp` nor `rsync` is required on the WordPress server.

### Rollback

Revert the problematic commit and push to `main`:

```bash
git revert <commit>
git push origin main
```

GitHub Actions will build and deploy the reverted version automatically.

### Data outside Git

- Commit ACF field-group JSON in `acf-json/`.
- Keep WordPress content, menus, theme options, media uploads, and the database outside this deployment process.
