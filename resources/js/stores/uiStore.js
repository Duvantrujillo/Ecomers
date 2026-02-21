export function uiStore() {
  return {
    cartOpen: false,
    isMobile: false,

    init() {
      const mq = window.matchMedia('(max-width: 639px)'); // < sm
      this.isMobile = mq.matches;

      const handler = (e) => (this.isMobile = e.matches);
      if (mq.addEventListener) mq.addEventListener('change', handler);
      else mq.addListener(handler);
    },

    openCart() { this.cartOpen = true },
    closeCart() { this.cartOpen = false },
    toggleCart() { this.cartOpen = !this.cartOpen },
  };
}