<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; margin: 14px; }
        .item {
            border: 1px solid #e5e7eb;
            padding: 10px;
            margin-bottom: 10px;
            page-break-inside: avoid;
        }
        .row { display: flex; align-items: center; gap: 12px; }
        .name { font-size: 13px; font-weight: 700; margin-bottom: 4px; }
        .ref { font-size: 10px; color: #666; font-family: monospace; word-break: break-all; }
        img { width: 140px; height: 140px; }
    </style>
</head>
<body>
@foreach ($products as $product)
    <div class="item">
        <div class="row">
            <img src="{{ $product->qrDataUri(220) }}" alt="QR">
            <div>
                <div class="name">{{ $product->name }}</div>
                <div class="ref">{{ $product->qr_reference }}</div>
            </div>
        </div>
    </div>
@endforeach
</body>
</html>
