import Alpine from 'alpinejs'
import { registerLikesStore } from './stores/likesStore'
import { cartStore } from "./stores/cart";
import { flyToCart } from './animations/flyToCart'
// ✅ NUEVO
import { uiStore } from './stores/uiStore';

window.Alpine = Alpine
registerLikesStore(Alpine)

Alpine.store("cart", cartStore());
Alpine.store('cart').init()

// ✅ NUEVO
Alpine.store('ui', uiStore());
window.animations = {
  flyToCart,
}
Alpine.start()