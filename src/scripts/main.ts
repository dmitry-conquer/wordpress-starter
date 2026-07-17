import "../styles/main.css";
import Alpine from "alpinejs";
import collapse from "@alpinejs/collapse";
import focus from "@alpinejs/focus";
import "taos";
import Lenis from "lenis";

Alpine.plugin(collapse);
Alpine.plugin(focus);
(window as unknown as { Alpine: typeof Alpine }).Alpine = Alpine;
Alpine.start();

// Wrap any third-party library or module initialization here
// to ensure the DOM is fully parsed before execution.
document.addEventListener("DOMContentLoaded", () => {
  (window as unknown as { lenis?: Lenis }).lenis = new Lenis({ autoRaf: true });
});
