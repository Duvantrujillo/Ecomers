<div
  x-data="{
    mq: null,
    isDesktop: false,

    lockBody() {
      const body = document.body
      const y = window.scrollY
      body.dataset.scrollY = String(y)

      const sbw = window.innerWidth - document.documentElement.clientWidth
      body.style.paddingRight = sbw > 0 ? `${sbw}px` : ''

      body.style.position = 'fixed'
      body.style.top = `-${y}px`
      body.style.left = '0'
      body.style.right = '0'
      body.style.width = '100%'
      body.style.overflow = 'hidden'
    },

    unlockBody() {
      const body = document.body
      const y = parseInt(body.dataset.scrollY || '0', 10)

      body.style.position = ''
      body.style.top = ''
      body.style.left = ''
      body.style.right = ''
      body.style.width = ''
      body.style.overflow = ''
      body.style.paddingRight = ''
      delete body.dataset.scrollY

      window.scrollTo(0, y)
    },

    init() {
      // desktop = lg (>=1024px). Ajusta si quieres
      this.mq = window.matchMedia('(min-width: 1024px)')
      this.isDesktop = this.mq.matches

      const onChange = (e) => {
        this.isDesktop = e.matches
        // si cambia a desktop, libera scroll; si cambia a móvil y el carrito está abierto, bloquea
        if (this.isDesktop) this.unlockBody()
        else if (this.$store.ui.cartOpen) this.lockBody()
      }

      if (this.mq.addEventListener) this.mq.addEventListener('change', onChange)
      else this.mq.addListener(onChange)

      // watch del carrito: SOLO bloquea en móvil/tablet
      this.$watch('$store.ui.cartOpen', (open) => {
        if (this.isDesktop) return // en PC no bloqueamos
        open ? this.lockBody() : this.unlockBody()
      })
    }
  }"
  x-init="init()"
>
  <!-- Overlay -->
  <div
    x-show="$store.ui.cartOpen"
    x-transition.opacity
   class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[90]"
    @click="$store.ui.closeCart()"
  ></div>

  <!-- Drawer -->
  <aside
    x-show="$store.ui.cartOpen"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="translate-x-full"
    x-transition:enter-end="translate-x-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="translate-x-0"
    x-transition:leave-end="translate-x-full"
    class="fixed right-0 top-0 h-full w-full sm:w-[420px] bg-white z-[100] shadow-2xl flex flex-col"
  >

    <!-- HEADER (sticky) -->
    <div class="sticky top-0 z-10 flex items-center justify-between px-5 py-4 border-b bg-gray-50">
      <div>
        <h3 class="text-lg font-semibold text-gray-900">Tu carrito</h3>
        <p class="text-xs text-gray-500" x-text="$store.cart.count + ' productos'"></p>
      </div>

      <!-- Botón cerrar (X) -->
      <button
        type="button"
        class="w-9 h-9 flex items-center justify-center rounded-full border border-gray-300 hover:bg-gray-100 transition"
        @click="$store.ui.closeCart()"
        aria-label="Cerrar carrito"
      >
        <svg xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
          class="w-5 h-5 text-gray-700">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    <!-- BODY -->
    <div class="flex-1 overflow-auto px-5 py-4">

      <!-- Vacío -->
      <template x-if="$store.cart.count === 0">
        <div class="text-center mt-10">
          <p class="text-gray-500 text-sm">Tu carrito está vacío</p>
        </div>
      </template>

      <!-- Productos -->
      <template x-for="item in Object.values($store.cart.items)" :key="item.id">
        <div class="flex gap-4 py-5 border-b">

          <!-- Imagen -->
          <img
            :src="item.image"
            alt=""
            class="w-20 h-20 rounded-lg object-cover bg-gray-100 flex-shrink-0"
          />

          <!-- Info -->
          <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-gray-900 truncate" x-text="item.name"></p>

            <p class="text-sm text-gray-600 mt-1">
              $<span x-text="Number(item.price).toLocaleString()"></span>
            </p>

            <!-- Controles (Cantidad + stepper + basura) -->
            <div class="mt-4 flex items-center justify-between gap-3">

              <div class="flex items-center gap-3">
                <!-- Etiqueta (solo desktop para no estorbar en móvil) -->
                <span class="text-xs text-gray-500 hidden sm:inline">Cantidad</span>

                <!-- Stepper -->
                <div class="inline-flex items-center rounded-lg border bg-white overflow-hidden">
                  <!-- - -->
                  <button
                    type="button"
                    class="w-10 h-10 grid place-items-center text-gray-700 hover:bg-gray-50 transition
                           disabled:opacity-40 disabled:cursor-not-allowed"
                    :disabled="item.qty <= 1"
                    @click="$store.cart.decrement(item.id)"
                    aria-label="Disminuir cantidad"
                  >
                    −
                  </button>

                  <!-- qty -->
                  <div class="w-10 h-10 grid place-items-center text-sm font-semibold text-gray-900">
                    <span x-text="item.qty"></span>
                  </div>

                  <!-- + -->
                  <button
                    type="button"
                    class="w-10 h-10 grid place-items-center text-gray-700 hover:bg-gray-50 transition"
                    @click="$store.cart.add(item)"
                    aria-label="Aumentar cantidad"
                  >
                    +
                  </button>
                </div>
              </div>

              <!-- Basura -->
              <button
                type="button"
                class="p-2 rounded-lg hover:bg-red-50 transition text-red-600"
                @click="$store.cart.remove(item.id)"
                aria-label="Quitar del carrito"
                title="Quitar"
              >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2"
                     class="w-5 h-5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18"/>
                  <path stroke-linecap="round" stroke-linejoin="round" d="M8 6V4h8v2"/>
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l1 14h10l1-14"/>
                  <path stroke-linecap="round" stroke-linejoin="round" d="M10 11v6"/>
                  <path stroke-linecap="round" stroke-linejoin="round" d="M14 11v6"/>
                </svg>
              </button>

            </div>

          </div>
        </div>
      </template>

    </div>

    <!-- FOOTER (sticky) -->
<div
  x-show="$store.cart.count > 0"
  x-transition.opacity
  class="sticky bottom-0 border-t px-5 py-4 bg-white"
>
  <div class="flex justify-between text-sm mb-4">
    <span class="text-gray-600">Subtotal</span>
    <span class="font-semibold">
      $<span x-text="($store.cart.subtotal ?? 0).toLocaleString()"></span>
    </span>
  </div>

  <button
    type="button"
    class="w-full bg-black text-white py-3 rounded-lg hover:bg-gray-800 transition font-medium"
  >
    Ir a pagar
  </button>
</div>

  </aside>
</div>