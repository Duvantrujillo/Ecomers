@php use Illuminate\Support\Facades\Storage; @endphp

<div id="default-carousel" class="relative w-full" data-carousel="slide">
    <!-- Carousel wrapper -->
    <div class="relative h-56 overflow-hidden rounded-base md:h-96">
        <!-- Item 1 -->
        <div class="hidden duration-700 ease-in-out" data-carousel-item>
            <img src="/docs/images/carousel/carousel-1.svg"
                class="absolute block w-full -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2" alt="...">
        </div>
        <!-- Item 2 -->
        <div class="hidden duration-700 ease-in-out" data-carousel-item>
            <img src="/docs/images/carousel/carousel-2.svg"
                class="absolute block w-full -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2" alt="...">
        </div>
        <!-- Item 3 -->
        <div class="hidden duration-700 ease-in-out" data-carousel-item>
            <img src="/docs/images/carousel/carousel-3.svg"
                class="absolute block w-full -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2" alt="...">
        </div>
        <!-- Item 4 -->
        <div class="hidden duration-700 ease-in-out" data-carousel-item>
            <img src="/docs/images/carousel/carousel-4.svg"
                class="absolute block w-full -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2" alt="...">
        </div>
        <!-- Item 5 -->
        <div class="hidden duration-700 ease-in-out" data-carousel-item>
            <img src="/docs/images/carousel/carousel-5.svg"
                class="absolute block w-full -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2" alt="...">
        </div>
    </div>
    <!-- Slider indicators -->
    <div class="absolute z-30 flex -translate-x-1/2 bottom-5 left-1/2 space-x-3 rtl:space-x-reverse">
        <button type="button" class="w-3 h-3 rounded-base" aria-current="true" aria-label="Slide 1"
            data-carousel-slide-to="0"></button>
        <button type="button" class="w-3 h-3 rounded-base" aria-current="false" aria-label="Slide 2"
            data-carousel-slide-to="1"></button>
        <button type="button" class="w-3 h-3 rounded-base" aria-current="false" aria-label="Slide 3"
            data-carousel-slide-to="2"></button>
        <button type="button" class="w-3 h-3 rounded-base" aria-current="false" aria-label="Slide 4"
            data-carousel-slide-to="3"></button>
        <button type="button" class="w-3 h-3 rounded-base" aria-current="false" aria-label="Slide 5"
            data-carousel-slide-to="4"></button>
    </div>
    <!-- Slider controls -->
    <button type="button"
        class="absolute top-0 start-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none"
        data-carousel-prev>
        <span
            class="inline-flex items-center justify-center w-10 h-10 rounded-base bg-white/30 dark:bg-gray-800/30 group-hover:bg-white/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-gray-800/70 group-focus:outline-none">
            <svg class="w-5 h-5 text-white rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m15 19-7-7 7-7" />
            </svg>
            <span class="sr-only">Previous</span>
        </span>
    </button>
    <button type="button"
        class="absolute top-0 end-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none"
        data-carousel-next>
        <span
            class="inline-flex items-center justify-center w-10 h-10 rounded-base bg-white/30 dark:bg-gray-800/30 group-hover:bg-white/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-gray-800/70 group-focus:outline-none">
            <svg class="w-5 h-5 text-white rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m9 5 7 7-7 7" />
            </svg>
            <span class="sr-only">Next</span>
        </span>
    </button>
</div>


<div class="bg-white">
    <div class="mx-auto max-w-screen-2xl px-4 py-16 sm:px-6 sm:py-24 lg:px-8">
        <h2 class="text-2xl font-bold tracking-tight text-gray-900">Productos</h2>

        <div class="mt-6 grid grid-cols-2 gap-x-4 gap-y-8 sm:grid-cols-2 lg:grid-cols-4 xl:gap-x-6">
            @foreach ($products as $product)
                <div class="group relative overflow-hidden rounded-md" x-data>

                    <div class="absolute right-2 top-2 z-10" x-data="{ burst: false }">
                        <button type="button"
                            class="relative inline-flex items-center justify-center rounded-full bg-white/90 p-2 shadow hover:bg-white focus:outline-none"
                            @click.stop="
      const turnedOn = $store.likes.toggle('{{ $product->id }}');
      if (turnedOn) { burst=true; setTimeout(()=>burst=false, 350); }
    "
                            :aria-label="$store.likes.isLiked('{{ $product->id }}') ? 'Quitar me encanta' : 'Me encanta'">
                            <span x-show="burst"
                                class="absolute inset-0 rounded-full bg-red-400/50 animate-ping"></span>

                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="h-6 w-6 transition duration-150"
                                :class="$store.likes.isLiked('{{ $product->id }}') ? 'text-red-500 fill-red-500 scale-110' :
                                    'text-gray-800 fill-none'">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 8.25c0-2.485-2.099-4.5-4.687-4.5-1.935 0-3.597 1.126-4.313 2.733C11.284 4.876 9.622 3.75 7.687 3.75 5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                            </svg>
                        </button>
                    </div>

                    <img src="{{ $product->image_url }}" alt="{{ $product->image_alt }}"
                        class="aspect-square w-full bg-gray-200 object-cover transition-transform duration-300 ease-out lg:group-hover:scale-105"
                        loading="lazy" decoding="async" />

                    <div class="mt-3 flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h3 class="text-sm font-medium text-gray-900 truncate">
                                {{ $product->name }}
                            </h3>
                            <p class="mt-1 text-xs text-gray-500 truncate">
                                {{ $product->category->name ?? 'Sin categoría' }}
                            </p>
                        </div>

                        <p class="text-sm font-semibold text-gray-900 whitespace-nowrap">
                            ${{ number_format($product->price, 0) }}
                        </p>
                    </div>
                    <!-- BOTÓN A TODO LO ANCHO -->
                    <div class="mt-4">

                        <!-- Si no está en carrito -->
                        <template x-if="!$store.cart.has({{ $product->id }})">
                            <button class="w-full bg-black text-white py-3 rounded-md"
                                @click="
    $store.cart.add({
      id: {{ $product->id }},
      name: @js($product->name),
      price: {{ $product->price }},
      image: @js($product->image_url)
    });

    window.animations.flyToCart(
      $event.currentTarget,
      document.querySelector('[data-cart-target]')
    );
  ">
                                Agregar al carrito
                            </button>
                        </template>

                        <!-- Si ya está en carrito -->
                        <template x-if="$store.cart.has({{ $product->id }})">
                            <div class="flex items-center justify-between border rounded-md p-2">
                                <button @click="$store.cart.decrement({{ $product->id }})">−</button>

                                <span x-text="$store.cart.qty({{ $product->id }})"></span>

                                <button
                                    @click="$store.cart.add({
                    id: {{ $product->id }},
                    name: @js($product->name),
                    price: {{ $product->price }},
                    image: @js($product->image_url)
                })">+</button>
                            </div>
                        </template>

                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

</div><!-- WhatsApp flotante con animación -->
<!-- WhatsApp flotante -->
<a href="https://wa.me/1234567890" target="_blank" aria-label="Contactar por WhatsApp" x-data
    x-show="!$store.ui.cartOpen" x-transition.opacity>
    <img src="{{ asset('img/whatsapp.png') }}" alt="WhatsApp" class="whatsapp-float animate-bounce">
</a>

<!-- CSS -->
<style>
    /* Animación personalizada de rebote */
    @keyframes bounce {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-10px);
        }

        /* Salta 10px hacia arriba */
    }

    .whatsapp-float {
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 60px;
        height: 60px;
        z-index: 1000;
        cursor: pointer;
        animation: bounce 2.5s infinite;
        /* Llama a la animación */
        transition: transform 0.3s;
    }

    .whatsapp-float:hover {
        transform: scale(1.2);
        /* Pequeño zoom al pasar el mouse */
        animation: none;
        /* Detiene el salto al hacer hover */
    }

    /* Responsivo: móviles más pequeños */
    @media (max-width: 640px) {
        .whatsapp-float {
            width: 50px;
            height: 50px;
            bottom: 15px;
            right: 15px;
        }
    }
</style>
