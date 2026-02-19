<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    public function index()
    {
        // Cache corto para que la home cargue rápido (ajusta a 300/600 si quieres)
        $featured = Cache::remember('home_featured_v1', 60, function () {
            return Product::query()
                ->where('active', 1)
                ->whereHas('category', fn ($q) => $q->where('active', 1))
                ->with('mainImage:id,product_id,url,alt_text,order')
                ->latest('id')
                ->take(5)
                ->get(['id', 'name', 'price'])
                ->map(function ($p) {
                    $img = $p->mainImage?->url;

                    $p->image_url = $img
                        ? Storage::disk('public')->url($img)
                        : asset('img/no-image.png');

                    $p->image_alt = $p->mainImage?->alt_text ?? $p->name;

                    // opcional: no cargar relación completa si no la usas en la vista
                    unset($p->mainImage);

                    return $p;
                });
        });

        $products = Cache::remember('home_products_v1', 60, function () {
            return Product::query()
                ->where('active', 1)
                ->whereHas('category', fn ($q) => $q->where('active', 1))
                ->with([
                    'category:id,name',
                    'mainImage:id,product_id,url,alt_text,order',
                ])
                ->latest('id')
                ->take(8)
                ->get(['id', 'name', 'price', 'category_id'])
                ->map(function ($p) {
                    $img = $p->mainImage?->url;

                    $p->image_url = $img
                        ? Storage::disk('public')->url($img)
                        : asset('img/no-image.png');

                    $p->image_alt = $p->mainImage?->alt_text ?? $p->name;

                    unset($p->mainImage);

                    return $p;
                });
        });

        return view('home', compact('featured', 'products'));
    }
}
