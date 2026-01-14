<div class="bg-white">
  <div class="mx-auto max-w-2xl px-4 py-16 sm:px-6 sm:py-24 lg:max-w-7xl lg:px-8">
    <h2 class="text-2xl font-bold tracking-tight text-gray-900">Customers also purchased</h2>

    <div class="mt-6 grid grid-cols-1 gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-4 xl:gap-x-8">
      <div class="group relative">
        <img src="https://tailwindcss.com/plus-assets/img/ecommerce-images/product-page-01-related-product-01.jpg" alt="Front of men&#039;s Basic Tee in black." class="aspect-square w-full rounded-md bg-gray-200 object-cover group-hover:opacity-75 lg:aspect-auto lg:h-80" />
        <div class="mt-4 flex justify-between">
          <div>
            <h3 class="text-sm text-gray-700">
              <a href="#">
                <span aria-hidden="true" class="absolute inset-0"></span>
                Basic Tee
              </a>
            </h3>
            <p class="mt-1 text-sm text-gray-500">Black</p>
          </div>
          <p class="text-sm font-medium text-gray-900">$35</p>
        </div>
      </div>
      <div class="group relative">
        <img src="https://tailwindcss.com/plus-assets/img/ecommerce-images/product-page-01-related-product-02.jpg" alt="Front of men&#039;s Basic Tee in white." class="aspect-square w-full rounded-md bg-gray-200 object-cover group-hover:opacity-75 lg:aspect-auto lg:h-80" />
        <div class="mt-4 flex justify-between">
          <div>
            <h3 class="text-sm text-gray-700">
              <a href="#">
                <span aria-hidden="true" class="absolute inset-0"></span>
                Basic Tee
              </a>
            </h3>
            <p class="mt-1 text-sm text-gray-500">Aspen White</p>
          </div>
          <p class="text-sm font-medium text-gray-900">$35</p>
        </div>
      </div>
      <div class="group relative">
        <img src="https://tailwindcss.com/plus-assets/img/ecommerce-images/product-page-01-related-product-03.jpg" alt="Front of men&#039;s Basic Tee in dark gray." class="aspect-square w-full rounded-md bg-gray-200 object-cover group-hover:opacity-75 lg:aspect-auto lg:h-80" />
        <div class="mt-4 flex justify-between">
          <div>
            <h3 class="text-sm text-gray-700">
              <a href="#">
                <span aria-hidden="true" class="absolute inset-0"></span>
                Basic Tee
              </a>
            </h3>
            <p class="mt-1 text-sm text-gray-500">Charcoal</p>
          </div>
          <p class="text-sm font-medium text-gray-900">$35</p>
        </div>
      </div>
      <div class="group relative">
        <img src="https://tailwindcss.com/plus-assets/img/ecommerce-images/product-page-01-related-product-04.jpg" alt="Front of men&#039;s Artwork Tee in peach with white and brown dots forming an isometric cube." class="aspect-square w-full rounded-md bg-gray-200 object-cover group-hover:opacity-75 lg:aspect-auto lg:h-80" />
        <div class="mt-4 flex justify-between">
          <div>
            <h3 class="text-sm text-gray-700">
              <a href="#">
                <span aria-hidden="true" class="absolute inset-0"></span>
                Artwork Tee
              </a>
            </h3>
            <p class="mt-1 text-sm text-gray-500">Iso Dots</p>
          </div>
          <p class="text-sm font-medium text-gray-900">$35</p>
        </div>
      </div>
    </div>
  </div>
</div><!-- WhatsApp flotante con animación -->
<a href="https://wa.me/1234567890" target="_blank" aria-label="Contactar por WhatsApp">
    <img src="{{ asset('img/whatsapp.png') }}" alt="WhatsApp" class="whatsapp-float animate-bounce">
</a>

<!-- CSS -->
<style>
/* Animación personalizada de rebote */
@keyframes bounce {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-10px); } /* Salta 10px hacia arriba */
}

.whatsapp-float {
    position: fixed;      
    bottom: 20px;         
    right: 20px;          
    width: 60px;          
    height: 60px;
    z-index: 1000;        
    cursor: pointer;      
    animation: bounce 2.5s infinite; /* Llama a la animación */
    transition: transform 0.3s;
}

.whatsapp-float:hover {
    transform: scale(1.2); /* Pequeño zoom al pasar el mouse */
    animation: none;       /* Detiene el salto al hacer hover */
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
