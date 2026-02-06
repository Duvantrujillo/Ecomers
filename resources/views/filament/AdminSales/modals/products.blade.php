@php
    /** @var \App\Models\AdminSale $sale */
    $items = $sale->saleItems;

    $count = $items->count();
    $units = (int) $items->sum('quantity');
    $total = number_format((float) $items->sum('subtotal'), 2);

    $date = $sale->sale_date ? \Illuminate\Support\Carbon::parse($sale->sale_date)->format('d/m/Y H:i') : '—';
    $customer = $sale->customer_name ?: '—';

    $statusLabel = match ($sale->status) {
        'paid' => 'Pagada',
        'pending' => 'Pendiente',
        'shipped' => 'Enviada',
        'cancelled' => 'Cancelada',
        default => ucfirst((string) $sale->status),
    };

    $svgPlaceholder = "data:image/svg+xml;base64," . base64_encode('
        <svg xmlns="http://www.w3.org/2000/svg" width="96" height="96">
            <rect width="100%" height="100%" fill="#f3f4f6"/>
            <path d="M28 60l10-12 10 12 8-10 12 16H28z" fill="#cbd5e1"/>
            <circle cx="40" cy="38" r="6" fill="#cbd5e1"/>
            <text x="50%" y="86%" text-anchor="middle" font-size="10" fill="#9ca3af" font-family="Arial">Sin imagen</text>
        </svg>
    ');
@endphp

<div style="max-width: 980px; margin: 0 auto; display: grid; gap: 16px;">

    {{-- Header --}}
    <div style="border:1px solid rgba(255,255,255,.08); background: rgba(255,255,255,.03); border-radius:16px; padding:18px;">
        <div style="display:flex; gap:16px; justify-content:space-between; align-items:flex-start; flex-wrap:wrap;">
            <div style="min-width:260px;">
                <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                    <div style="font-size:18px; font-weight:700;">Venta #{{ $sale->id }}</div>
                    <span style="font-size:12px; padding:4px 10px; border-radius:999px; border:1px solid rgba(255,255,255,.12); background:rgba(255,255,255,.04);">
                        {{ $statusLabel }}
                    </span>
                </div>

                <div style="margin-top:6px; font-size:13px; opacity:.75;">{{ $date }}</div>

                <div style="margin-top:12px; display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                    <div style="border:1px solid rgba(255,255,255,.08); background:rgba(255,255,255,.03); border-radius:12px; padding:12px;">
                        <div style="font-size:11px; letter-spacing:.08em; text-transform:uppercase; opacity:.7;">Cliente</div>
                        <div style="margin-top:4px; font-weight:600;">{{ $customer }}</div>
                    </div>
                    <div style="border:1px solid rgba(255,255,255,.08); background:rgba(255,255,255,.03); border-radius:12px; padding:12px;">
                        <div style="font-size:11px; letter-spacing:.08em; text-transform:uppercase; opacity:.7;">Notas</div>
                        <div style="margin-top:4px; opacity:.9;">{{ $sale->notes ?: '—' }}</div>
                    </div>
                </div>
            </div>

            {{-- Summary --}}
            <div style="display:grid; grid-template-columns:repeat(3, minmax(110px, 1fr)); gap:10px; min-width:360px;">
                <div style="border:1px solid rgba(255,255,255,.08); background:rgba(255,255,255,.03); border-radius:14px; padding:12px; text-align:center;">
                    <div style="font-size:11px; letter-spacing:.08em; text-transform:uppercase; opacity:.7;">Items</div>
                    <div style="margin-top:6px; font-size:14px; font-weight:700;">{{ $count }}</div>
                </div>
                <div style="border:1px solid rgba(255,255,255,.08); background:rgba(255,255,255,.03); border-radius:14px; padding:12px; text-align:center;">
                    <div style="font-size:11px; letter-spacing:.08em; text-transform:uppercase; opacity:.7;">Unidades</div>
                    <div style="margin-top:6px; font-size:14px; font-weight:700;">{{ $units }}</div>
                </div>
                <div style="border:1px solid rgba(255,255,255,.08); background:rgba(255,255,255,.03); border-radius:14px; padding:12px; text-align:center;">
                    <div style="font-size:11px; letter-spacing:.08em; text-transform:uppercase; opacity:.7;">Total</div>
                    <div style="margin-top:6px; font-size:14px; font-weight:700;">${{ $total }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Products list (mejor UX que tabla para “persona normal”) --}}
    <div style="border:1px solid rgba(255,255,255,.08); background: rgba(255,255,255,.02); border-radius:16px; overflow:hidden;">
        <div style="padding:14px 18px; border-bottom:1px solid rgba(255,255,255,.08); display:flex; justify-content:space-between; align-items:center;">
            <div>
                <div style="font-weight:700;">Productos</div>
                <div style="font-size:12px; opacity:.75;">Detalle de productos vendidos</div>
            </div>
            <div style="font-size:12px; opacity:.75;">{{ $count }} item(s)</div>
        </div>

        <div style="max-height:520px; overflow:auto;">
            @foreach ($items as $item)
                @php
                    $product = $item->product;
                    $name = $product?->name ?? 'Producto';

                    $img = $product?->images?->first();
                    $imgUrl = $img?->image_url ?: $svgPlaceholder;

                    $qty  = (int) $item->quantity;
                    $unit = number_format((float) $item->unit_price, 2);
                    $sub  = number_format((float) $item->subtotal, 2);
                @endphp

                <div style="padding:14px 18px; border-bottom:1px solid rgba(255,255,255,.06); display:flex; gap:14px; align-items:flex-start;">
                    <img
                        src="{{ $imgUrl }}"
                        alt="{{ $name }}"
                        style="width:64px; height:64px; border-radius:14px; object-fit:cover; background:#111827; border:1px solid rgba(255,255,255,.10); flex:0 0 auto;"
                        loading="lazy"
                        onerror="this.src='{{ $svgPlaceholder }}'"
                    />

                    <div style="flex:1; min-width:0;">
                        <div style="display:flex; justify-content:space-between; gap:12px; align-items:flex-start; flex-wrap:wrap;">
                            <div style="min-width:220px;">
                                <div style="font-weight:700; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                    {{ $name }}
                                </div>
                                <div style="margin-top:4px; font-size:12px; opacity:.75;">
                                    Precio unit.: <b style="opacity:.95;">${{ $unit }}</b>
                                </div>
                            </div>

                            <div style="display:flex; gap:18px; align-items:center;">
                                <div style="font-size:12px; padding:6px 10px; border-radius:999px; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.10);">
                                    Cant: <b>{{ $qty }}</b>
                                </div>

                                <div style="text-align:right;">
                                    <div style="font-size:11px; opacity:.7;">Subtotal</div>
                                    <div style="font-size:14px; font-weight:800;">${{ $sub }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div style="padding:14px 18px; border-top:1px solid rgba(255,255,255,.08); display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap;">
            <div style="opacity:.8;">Total a pagar</div>
            <div style="display:flex; gap:12px; align-items:center;">
                <div style="font-size:12px; opacity:.75;">Unidades: <b style="opacity:.95;">{{ $units }}</b></div>
                <div style="width:1px; height:16px; background:rgba(255,255,255,.12);"></div>
                <div style="font-size:18px; font-weight:900;">${{ $total }}</div>
            </div>
        </div>
    </div>

</div>
