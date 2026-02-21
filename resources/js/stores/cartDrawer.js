export function cartDrawer() {
  return {
    init() {
      // Cerrar con ESC
      window.addEventListener("keydown", (e) => {
        if (e.key === "Escape") this.$store.ui.closeCart();
      });

      // Scroll lock PRO
      this.$watch("$store.ui.cartOpen", (open) => {
        const body = document.body;

        if (open) {
          const scrollY = window.scrollY;
          body.dataset.scrollY = String(scrollY);

          // Evita salto por scrollbar
          const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
          body.style.paddingRight = scrollbarWidth > 0 ? `${scrollbarWidth}px` : "";

          // Bloquea scroll real
          body.style.position = "fixed";
          body.style.top = `-${scrollY}px`;
          body.style.left = "0";
          body.style.right = "0";
          body.style.width = "100%";
          body.style.overflow = "hidden";
        } else {
          const y = parseInt(body.dataset.scrollY || "0", 10);

          // Restaurar
          body.style.position = "";
          body.style.top = "";
          body.style.left = "";
          body.style.right = "";
          body.style.width = "";
          body.style.overflow = "";
          body.style.paddingRight = "";
          delete body.dataset.scrollY;

          window.scrollTo(0, y);
        }
      });
    },
  };
}