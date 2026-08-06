import { defineConfig } from "vite";
import tailwindcss from "@tailwindcss/vite";
import FullReload from "vite-plugin-full-reload";
import path from "node:path";
import { fileURLToPath } from "node:url";

const root = path.dirname(fileURLToPath(import.meta.url));

const color = {
  reset: "\x1b[0m",
  bold: "\x1b[1m",
  green: "\x1b[32m",
  yellow: "\x1b[33m",
  gray: "\x1b[90m",
};

const wordpressNotice = {
  name: "wordpress-development-notice",
  configureServer(server) {
    server.middlewares.use("/__theme-dev-status", (request, response, next) => {
      if (request.method !== "GET") {
        next();
        return;
      }

      response.statusCode = 200;
      response.setHeader("Content-Type", "application/json");
      response.setHeader("Cache-Control", "no-store");
      response.setHeader("Access-Control-Allow-Origin", "*");
      response.end(JSON.stringify({ status: "ready", service: "theme-vite-dev-server" }));
    });

    server.printUrls = () => {
      server.config.logger.info("");
      server.config.logger.info(`  ${color.bold}${color.green}Development server is ready${color.reset}`);
      server.config.logger.info("");
      server.config.logger.info(`  ${color.bold}${color.yellow}1. Open your LocalWP site${color.reset}`);
      server.config.logger.info(`  ${color.gray}Vite only serves CSS and JavaScript with hot reload.${color.reset}`);
      server.config.logger.info("");
      server.config.logger.info(`  ${color.bold}${color.yellow}Production:${color.reset} run the ${color.bold}build script${color.reset}`);
      server.config.logger.info(`  ${color.gray}Ready theme: release/${color.reset}`);
      server.config.logger.info("");
    };
  },
};

export default defineConfig({
  plugins: [tailwindcss(), FullReload(["*.php", "inc/**/*.php", "template-parts/**/*.php", "templates/**/*.php", "acf-json/**/*.json"]), wordpressNotice],
  server: {
    host: "0.0.0.0",
    port: 5173,
    strictPort: true,
    cors: true,
  },
  build: {
    manifest: true,
    outDir: path.resolve(root, "assets/build"),
    emptyOutDir: true,
    rollupOptions: {
      input: path.resolve(root, "src/scripts/main.ts"),
      output: {
        entryFileNames: "js/[name]-[hash].js",
        chunkFileNames: "js/[name]-[hash].js",
        assetFileNames: asset => {
          if (asset.name?.endsWith(".css")) return "css/[name]-[hash][extname]";
          if (/\.(woff2?|ttf|otf|eot)$/.test(asset.name ?? "")) return "fonts/[name]-[hash][extname]";
          return "assets/[name]-[hash][extname]";
        },
      },
    },
  },
});
