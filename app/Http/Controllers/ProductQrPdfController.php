<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;

class ProductQrPdfController extends Controller
{
    public function single(Product $product)
    {
        return Pdf::loadView('pdf.product-qr-single', [
                'product' => $product,
            ])
            ->setPaper('a6', 'portrait') // pequeño (etiqueta)
            ->download('QR-' . str($product->name)->slug('_') . '.pdf');
    }

    public function all()
    {
        $products = Product::query()
            ->orderBy('name')
            ->get();

        return Pdf::loadView('pdf.product-qr-all', [
                'products' => $products,
            ])
            ->setPaper('a4', 'portrait')
            ->download('QRS-PRODUCTOS.pdf');
    }
}
