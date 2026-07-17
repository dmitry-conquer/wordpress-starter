#!/usr/bin/env bash

set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
DEFAULT_THEME_NAME="$(node -e 'console.log(JSON.parse(require("node:fs").readFileSync(process.argv[1], "utf8")).name)' "$ROOT_DIR/package.json")"
THEME_NAME="$DEFAULT_THEME_NAME"
RELEASE_DIR="$ROOT_DIR/release"

for argument in "$@"; do
  if [[ "$argument" == "--" ]]; then
    continue
  fi

  if [[ "$argument" == --* ]]; then
    THEME_NAME="${argument#--}"
    continue
  fi

  echo "Error: unsupported package argument: $argument" >&2
  echo "Use --<theme-name>, for example: pnpm build --custom-name" >&2
  exit 1
done

if [[ ! "$THEME_NAME" =~ ^[a-z0-9][a-z0-9._-]*$ ]]; then
  echo "Error: invalid theme name: $THEME_NAME" >&2
  echo "Use lowercase letters, numbers, dots, underscores, and hyphens." >&2
  exit 1
fi

THEME_DIR="$RELEASE_DIR/$THEME_NAME"

if [[ ! -f "$ROOT_DIR/assets/build/.vite/manifest.json" ]]; then
  echo "Error: Vite manifest was not generated." >&2
  exit 1
fi

mkdir -p "$RELEASE_DIR"
rm -rf "$THEME_DIR"
rm -f "$RELEASE_DIR/$THEME_NAME.zip"
mkdir -p "$THEME_DIR"

rsync -a \
  --exclude '/.claude/' \
  --exclude '/.codex/' \
  --exclude '/.git/' \
  --exclude '/.github/' \
  --exclude '/.idea/' \
  --exclude '/.vscode/' \
  --exclude '/node_modules/' \
  --exclude '/release/' \
  --exclude '/scripts/' \
  --exclude '/src/' \
  --exclude '/.DS_Store' \
  --exclude '/.env' \
  --exclude '/.env.*' \
  --exclude '/.gitignore' \
  --exclude '/.prettierignore' \
  --exclude '/.prettierrc' \
  --exclude '/CLAUDE.md' \
  --exclude '/README.md' \
  --exclude '/eslint.config.js' \
  --exclude '/package.json' \
  --exclude '/pnpm-lock.yaml' \
  --exclude '/pnpm-workspace.yaml' \
  --exclude '/tsconfig.json' \
  --exclude '/vite.config.js' \
  "$ROOT_DIR/" "$THEME_DIR/"

find "$THEME_DIR" -name '.DS_Store' -delete
find "$THEME_DIR" -name '.gitkeep' -delete

printf '\n\033[1;32mProduction build is ready\033[0m\n\n'
printf '\033[1;33mReady theme:\033[0m %s\n' "$THEME_DIR"
printf '\033[90mWorking source files remain in the project root.\033[0m\n\n'
