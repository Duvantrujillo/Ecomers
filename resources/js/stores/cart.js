export function cartStore() {
    return {
        items: {},

        get count() {
            return Object.values(this.items)
                .reduce((total, item) => total + item.qty, 0);
        },

        // ✅ subtotal para el drawer
        get subtotal() {
            return Object.values(this.items)
                .reduce((total, item) => total + (Number(item.price) * Number(item.qty)), 0);
        },

        init() {
            const stored = localStorage.getItem('cart');
            if (stored) {
                this.items = JSON.parse(stored);
            }
        },

        persist() {
            localStorage.setItem('cart', JSON.stringify(this.items));
        },

        add(product) {
            const id = product.id.toString();

            if (!this.items[id]) {
                this.items[id] = { ...product, qty: 1 };
            } else {
                this.items[id].qty++;
            }

            this.persist();
            window.dispatchEvent(new CustomEvent('cart:added'));
        },

        // 🔹 comportamiento original (cards)
        decrement(productId) {
            const id = productId.toString();
            if (!this.items[id]) return;

            this.items[id].qty--;

            if (this.items[id].qty <= 0) {
                delete this.items[id];
            }

            this.persist();
        },

        // 🔹 solo drawer (no baja de 1)
        decrementInDrawer(productId) {
            const id = String(productId);
            if (!this.items[id]) return;

            if (this.items[id].qty > 1) {
                this.items[id].qty -= 1;
                this.persist();
            }
        },

        // ✅ MÉTODO QUE FALTABA
        remove(productId) {
            const id = String(productId);
            if (!this.items[id]) return;

            delete this.items[id];
            this.persist();
        },

        qty(productId) {
            return this.items[productId]?.qty ?? 0;
        },

        has(productId) {
            return !!this.items[productId];
        },
    }
}