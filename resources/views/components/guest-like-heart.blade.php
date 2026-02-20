@props([
  'productId',
  'ttlMinutes' => 240, // 4 horas (cámbialo)
  'top' => 'top-4',
  'right' => 'right-4',
  'size' => 'h-6 w-6',
])

@php
  $key = "guest_like_product_{$productId}";
@endphp

<div
  x-data="guestLikeWithTTL('{{ $key }}', {{ (int)$ttlMinutes }})"
  x-init="init()"
  class="absolute {{ $top }} {{ $right }} z-10"
>
  <button
    type="button"
    class="relative inline-flex items-center justify-center rounded-full bg-white/90 p-2 shadow hover:bg-white focus:outline-none"
    :aria-label="liked ? 'Quitar me encanta' : 'Me encanta'"
    :title="liked ? 'Quitar me encanta' : 'Me encanta'"
    @click.stop="toggle()"
  >
    {{-- chispas SOLO cuando se activa (no cuando se quita) --}}
    <template x-if="burst">
      <span class="absolute inset-0 rounded-full bg-red-400/50 animate-ping"></span>
    </template>

    {{-- corazón --}}
    <svg
      xmlns="http://www.w3.org/2000/svg"
      viewBox="0 0 24 24"
      stroke-width="1.5"
      stroke="currentColor"
      class="{{ $size }} transition duration-150"
      :class="liked ? 'text-red-500 fill-red-500 scale-110' : 'text-gray-800 fill-none'"
    >
      <path stroke-linecap="round" stroke-linejoin="round"
        d="M21 8.25c0-2.485-2.099-4.5-4.687-4.5-1.935 0-3.597 1.126-4.313 2.733C11.284 4.876 9.622 3.75 7.687 3.75 5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"
      />
    </svg>
  </button>
</div>

@once
<script>
  document.addEventListener('alpine:init', () => {
    Alpine.data('guestLikeWithTTL', (storageKey, ttlMinutes) => ({
      liked: false,
      burst: false,

      init() {
        const raw = localStorage.getItem(storageKey);
        if (!raw) return;

        try {
          const data = JSON.parse(raw); // { liked: boolean, expiresAt: number }
          const now = Date.now();

          // Expiró → limpiar
          if (!data.expiresAt || now > data.expiresAt) {
            localStorage.removeItem(storageKey);
            this.liked = false;
            return;
          }

          this.liked = !!data.liked;
        } catch (e) {
          localStorage.removeItem(storageKey);
          this.liked = false;
        }
      },

      persist() {
        const expiresAt = Date.now() + (ttlMinutes * 60 * 1000);
        localStorage.setItem(storageKey, JSON.stringify({
          liked: this.liked,
          expiresAt
        }));
      },

      toggle() {
        const next = !this.liked;
        this.liked = next;

        // chispas solo cuando se activa
        if (next) {
          this.burst = true;
          setTimeout(() => (this.burst = false), 350);
        }

        // si lo quitó, igual actualizamos (queda liked:false y reinicia TTL)
        // si prefieres que al quitar se borre del storage, te dejo opción abajo
        this.persist();
      }
    }));
  });
</script>
@endonce