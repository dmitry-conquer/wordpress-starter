import { cp, mkdir, readFile, readdir, rm, stat } from "node:fs/promises";
import path from "node:path";
import { fileURLToPath } from "node:url";

const ROOT_DIR = path.resolve(path.dirname(fileURLToPath(import.meta.url)), "..");
const RELEASE_DIR = path.join(ROOT_DIR, "release");
const MANIFEST_PATH = path.join(ROOT_DIR, "assets", "build", ".vite", "manifest.json");

const ROOT_EXCLUSIONS = new Set([
  ".claude",
  ".agents",
  ".codex",
  ".git",
  ".github",
  ".idea",
  ".pnpm-store",
  ".vscode",
  "node_modules",
  "release",
  "scripts",
  "src",
  "inc/DevIndicator.php",
  ".gitignore",
  ".prettierignore",
  ".prettierrc",
  "CLAUDE.md",
  "README.md",
  "eslint.config.js",
  "package.json",
  "pnpm-lock.yaml",
  "pnpm-workspace.yaml",
  "tsconfig.json",
  "vite.config.js",
]);

const FILE_EXCLUSIONS = new Set([".DS_Store", ".gitkeep"]);
const WINDOWS_RESERVED_NAME = /^(con|prn|aux|nul|com[1-9]|lpt[1-9])(?:\..*)?$/i;

function relativePath(source) {
  return path.relative(ROOT_DIR, source).split(path.sep).join("/");
}

function shouldCopy(source) {
  const relative = relativePath(source);
  if (!relative) return true;

  const parts = relative.split("/");
  if (parts.some(part => FILE_EXCLUSIONS.has(part))) return false;
  if (ROOT_EXCLUSIONS.has(relative)) return false;
  if (!relative.includes("/") && (relative === ".env" || relative.startsWith(".env."))) return false;

  return true;
}

function themeNameFromArguments(defaultName) {
  let themeName = defaultName;
  let hasCustomName = false;

  for (const argument of process.argv.slice(2)) {
    if (argument === "--") continue;

    if (!argument.startsWith("--") || argument.length === 2) {
      throw new Error(`Unsupported package argument: ${argument}\nUse --<theme-name>, for example: pnpm build --custom-name`);
    }

    if (hasCustomName) {
      throw new Error("Only one custom theme name can be provided.");
    }

    themeName = argument.slice(2);
    hasCustomName = true;
  }

  return themeName;
}

function validateThemeName(themeName) {
  if (!/^[a-z0-9][a-z0-9._-]*$/.test(themeName) || themeName.endsWith(".") || WINDOWS_RESERVED_NAME.test(themeName)) {
    throw new Error(`Invalid theme name: ${themeName}\nUse a Windows-safe name with lowercase letters, numbers, dots, underscores, and hyphens.`);
  }
}

function validateThemeDirectory(themeDirectory) {
  const relative = path.relative(RELEASE_DIR, themeDirectory);

  if (!relative || relative.startsWith(`..${path.sep}`) || path.isAbsolute(relative)) {
    throw new Error("Refusing to package outside the release directory.");
  }
}

async function isFile(file) {
  try {
    return (await stat(file)).isFile();
  } catch {
    return false;
  }
}

async function copyTheme(themeDirectory) {
  const entries = await readdir(ROOT_DIR, { withFileTypes: true });

  for (const entry of entries) {
    const source = path.join(ROOT_DIR, entry.name);
    if (!shouldCopy(source)) continue;

    await cp(source, path.join(themeDirectory, entry.name), {
      recursive: true,
      force: true,
      preserveTimestamps: true,
      filter: shouldCopy,
    });
  }
}

async function main() {
  const packageJson = JSON.parse(await readFile(path.join(ROOT_DIR, "package.json"), "utf8"));
  const defaultThemeName = packageJson.name;

  if (typeof defaultThemeName !== "string" || defaultThemeName === "") {
    throw new Error("The package.json name field must contain a theme name.");
  }

  const themeName = themeNameFromArguments(defaultThemeName);
  validateThemeName(themeName);

  const themeDirectory = path.join(RELEASE_DIR, themeName);
  validateThemeDirectory(themeDirectory);

  if (!(await isFile(MANIFEST_PATH))) {
    throw new Error("Vite manifest was not generated.");
  }

  await mkdir(RELEASE_DIR, { recursive: true });
  await rm(themeDirectory, { recursive: true, force: true });
  await rm(path.join(RELEASE_DIR, `${themeName}.zip`), { force: true });
  await mkdir(themeDirectory, { recursive: true });
  await copyTheme(themeDirectory);

  const color = process.stdout.isTTY
    ? {
        reset: "\x1b[0m",
        bold: "\x1b[1m",
        green: "\x1b[32m",
        yellow: "\x1b[33m",
        gray: "\x1b[90m",
      }
    : { reset: "", bold: "", green: "", yellow: "", gray: "" };

  process.stdout.write(`\n${color.bold}${color.green}Production build is ready${color.reset}\n\n`);
  process.stdout.write(`${color.bold}${color.yellow}Ready theme:${color.reset} ${themeDirectory}\n`);
  process.stdout.write(`${color.gray}Working source files remain in the project root.${color.reset}\n\n`);
}

main().catch(error => {
  process.stderr.write(`Error: ${error instanceof Error ? error.message : String(error)}\n`);
  process.exitCode = 1;
});
