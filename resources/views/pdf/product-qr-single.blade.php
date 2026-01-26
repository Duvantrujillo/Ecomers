<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; margin: 10px; }
        .wrap { text-align: center; }
        .name { font-size: 14px; font-weight: 700; margin-bottom: 6px; }
        .ref { font-size: 10px; color: #666; font-family: monospace; margin-top: 8px; word-break: break-all; }
        img { width: 220px; height: 220px; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="name">{{ $product->name }}</div>
    <img src="{{ $product->qrDataUri(260) }}" alt="QR">
    <div class="ref">{{ $product->qr_reference }}</div>
</div>
</body>
</html>
