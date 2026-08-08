import { readFile, writeFile } from "node:fs/promises";
import path from "node:path";
import { fileURLToPath } from "node:url";
import { createInterface } from "node:readline/promises";
import { replacements, STARTER_PACKAGE_NAME } from "./theme-identity.config.mjs";

const ROOT_DIR = path.resolve(path.dirname(fileURLToPath(import.meta.url)), "..");
const IS_POSTINSTALL = process.argv.includes("--postinstall");
const IS_DRY_RUN = process.argv.includes("--dry-run");

function createIdentity(input) {
  const name = input.trim();

  if (name.length > 80 || !/^[A-Za-z]+(?: [A-Za-z]+)*$/.test(name)) {
    throw new Error("Use 1–80 Latin letters and single spaces, for example: Pride AC.");
  }

  const words = name.split(" ");
  const namespace = words.map(word => (word === word.toUpperCase() ? word : word[0].toUpperCase() + word.slice(1).toLowerCase())).join("_");
  const slug = words.map(word => word.toLowerCase()).join("-");

  if (slug === STARTER_PACKAGE_NAME || slug === "wp-theme-starter") {
    throw new Error("This name conflicts with the starter theme identity. Choose a different name.");
  }

  return {
    name,
    slug,
    namespace,
    constantPrefix: words.map(word => word.toUpperCase()).join("_"),
  };
}

async function isInitialized() {
  const packageJson = JSON.parse(await readFile(path.join(ROOT_DIR, "package.json"), "utf8"));
  return packageJson.name !== STARTER_PACKAGE_NAME;
}

async function prepareChanges(identity) {
  const changes = new Map();

  for (const replacement of replacements) {
    for (const relativeFile of replacement.files) {
      const file = path.join(ROOT_DIR, relativeFile);
      const current = changes.get(file) ?? (await readFile(file, "utf8"));

      if (!current.includes(replacement.token)) {
        throw new Error(`Expected "${replacement.token}" in ${relativeFile}. No files were changed.`);
      }

      changes.set(file, current.replaceAll(replacement.token, identity[replacement.value]));
    }
  }

  return changes;
}

async function ask(question, terminal) {
  const answer = await terminal.question(question);
  return answer.trim();
}

async function main() {
  if (await isInitialized()) {
    if (!IS_POSTINSTALL) process.stdout.write("Theme is already initialized.\n");
    return;
  }

  if (IS_POSTINSTALL && !process.stdin.isTTY) return;

  const terminal = createInterface({ input: process.stdin, output: process.stdout });

  try {
    const identity = createIdentity(await ask("Theme name: ", terminal));

    process.stdout.write(`\nName:      ${identity.name}\n`);
    process.stdout.write(`Slug:      ${identity.slug}\n`);
    process.stdout.write(`Namespace: ${identity.namespace}\n`);
    process.stdout.write(`Prefix:    ${identity.constantPrefix}\n\n`);

    if (IS_DRY_RUN) {
      const changes = await prepareChanges(identity);
      process.stdout.write("Would update:\n");
      for (const file of changes.keys()) {
        process.stdout.write(`- ${path.relative(ROOT_DIR, file)}\n`);
      }
      return;
    }

    const confirmed = (await ask("Continue? (Y/n) ", terminal)).toLowerCase();
    if (confirmed !== "" && confirmed !== "y" && confirmed !== "yes") {
      process.stdout.write("Cancelled.\n");
      return;
    }

    const changes = await prepareChanges(identity);
    await Promise.all([...changes].map(([file, content]) => writeFile(file, content)));
    process.stdout.write(`\nTheme initialized as ${identity.name}.\n`);
  } finally {
    terminal.close();
  }
}

main().catch(error => {
  process.stderr.write(`Error: ${error instanceof Error ? error.message : String(error)}\n`);
  process.exitCode = 1;
});
