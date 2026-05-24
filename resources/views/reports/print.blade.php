<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Laporan {{ $from }} - {{ $to }}</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #f8fafc; color: #0f172a; font-family: Arial, sans-serif; font-size: 12px; }
        .actions { max-width: 960px; margin: 24px auto 0; display: flex; justify-content: flex-end; gap: 8px; }
        .btn { border: 1px solid #cbd5e1; background: #fff; color: #334155; border-radius: 10px; padding: 10px 14px; font-weight: 700; text-decoration: none; cursor: pointer; }
        .btn-primary { border-color: #4f46e5; background: #4f46e5; color: #fff; }
        .page { max-width: 960px; margin: 16px auto 32px; background: #fff; padding: 28px; box-shadow: 0 20px 50px rgba(15, 23, 42, .10); }
        h1, h2, h3, p { margin: 0; }
        h1 { font-size: 22px; }
        h2 { font-size: 15px; margin-bottom: 10px; }
        .muted { color: #64748b; }
        .grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-top: 20px; }
        .card { border: 1px solid #e2e8f0; border-radius: 14px; padding: 14px; }
        .label { color: #64748b; font-size: 11px; }
        .value { margin-top: 8px; font-size: 18px; font-weight: 800; }
        .section { margin-top: 24px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f8fafc; color: #64748b; font-size: 10px; text-transform: uppercase; letter-spacing: .05em; text-align: left; }
        th, td { border: 1px solid #e2e8f0; padding: 9px; vertical-align: top; }
        .right { text-align: right; }
        @media print {
            body { background: #fff; }
            .actions { display: none; }
            .page { margin: 0; max-width: none; box-shadow: none; padding: 0; }
            @page { size: A4; margin: 12mm; }
        }
    </style>
</head>
<body>
    <div class="actions">
        <a href="{{ route('reports.index', request()->only(['period', 'from', 'to'])) }}" class="btn">Kembali</a>
        <button onclick="window.print()" class="btn btn-primary">Print</button>
    </div>

    <main class="page">
        <header>
            <p class="muted">Inventory UMKM</p>
            <h1>Laporan Periode {{ \Illuminate\Support\Carbon::parse($from)->translatedFormat('d M Y') }} - {{ \Illuminate\Support\Carbon::parse($to)->translatedFormat('d M Y') }}</h1>
            <p class="muted" style="margin-top: 6px;">Dicetak pada {{ now()->translatedFormat('d M Y H:i') }}</p>
        </header>

        <section class="grid">
            <div class="card"><p class="label">Omzet</p><p class="value">Rp {{ number_format($revenue, 0, ',', '.') }}</p></div>
            <div class="card"><p class="label">Transaksi</p><p class="value">{{ number_format($transactions, 0, ',', '.') }}</p></div>
            <div class="card"><p class="label">Item Terjual</p><p class="value">{{ number_format($soldQuantity, 0, ',', '.') }}</p></div>
            <div class="card"><p class="label">Rata-rata Transaksi</p><p class="value">Rp {{ number_format($averageTransaction, 0, ',', '.') }}</p></div>
            <div class="card"><p class="label">Stok Masuk</p><p class="value">{{ number_format($stockIn, 0, ',', '.') }}</p></div>
            <div class="card"><p class="label">Stok Keluar</p><p class="value">{{ number_format($stockOut, 0, ',', '.') }}</p></div>
            <div class="card"><p class="label">Adjustment</p><p class="value">{{ number_format($stockAdjustments, 0, ',', '.') }}</p></div>
            <div class="card"><p class="label">Nilai Inventory</p><p class="value">Rp {{ number_format($inventoryValue, 0, ',', '.') }}</p></div>
        </section>

        <section class="section">
            <h2>Produk Terlaris</h2>
            <table>
                <thead><tr><th>Produk</th><th>SKU</th><th class="right">Qty</th><th class="right">Omzet</th><th class="right">Stok</th></tr></thead>
                <tbody>
                    @forelse ($topProducts as $product)
                        <tr>
                            <td>{{ $product->product_name_snapshot }}</td>
                            <td>{{ $product->product_sku_snapshot ?: '-' }}</td>
                            <td class="right">{{ number_format($product->total_quantity, 0, ',', '.') }}</td>
                            <td class="right">Rp {{ number_format($product->total_revenue, 0, ',', '.') }}</td>
                            <td class="right">{{ number_format($product->current_stock, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="muted">Belum ada data produk terlaris.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </section>

        <section class="section">
            <h2>Stok Rendah</h2>
            <table>
                <thead><tr><th>Produk</th><th>Kategori</th><th class="right">Stok</th><th class="right">Minimum</th></tr></thead>
                <tbody>
                    @forelse ($lowStockProducts as $product)
                        <tr>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->category?->name ?? '-' }}</td>
                            <td class="right">{{ number_format($product->stock, 0, ',', '.') }}</td>
                            <td class="right">{{ number_format($product->minimum_stock, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="muted">Semua stok aman.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </section>

        <section class="section">
            <h2>Transaksi Terbaru</h2>
            <table>
                <thead><tr><th>Invoice</th><th>Tanggal</th><th>Pelanggan</th><th class="right">Item</th><th class="right">Total</th></tr></thead>
                <tbody>
                    @forelse ($recentSales as $sale)
                        <tr>
                            <td>{{ $sale->invoice_number }}</td>
                            <td>{{ $sale->sale_date->translatedFormat('d M Y H:i') }}</td>
                            <td>{{ $sale->customer_name ?: 'Umum' }}</td>
                            <td class="right">{{ number_format($sale->items_count, 0, ',', '.') }}</td>
                            <td class="right">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="muted">Belum ada transaksi pada periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>
