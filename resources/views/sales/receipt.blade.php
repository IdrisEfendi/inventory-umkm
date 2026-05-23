<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota {{ $sale->invoice_number }}</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #f1f5f9; color: #0f172a; font-family: Arial, sans-serif; font-size: 12px; }
        .page { max-width: 420px; margin: 24px auto; background: #fff; padding: 24px; box-shadow: 0 20px 50px rgba(15, 23, 42, .12); }
        .actions { max-width: 420px; margin: 24px auto 0; display: flex; gap: 8px; justify-content: flex-end; }
        .btn { border: 1px solid #cbd5e1; background: #fff; color: #334155; border-radius: 10px; padding: 10px 14px; font-weight: 700; text-decoration: none; cursor: pointer; }
        .btn-primary { border-color: #4f46e5; background: #4f46e5; color: #fff; }
        .header { text-align: center; }
        .store-name { font-size: 18px; font-weight: 900; letter-spacing: .02em; }
        .muted { color: #64748b; }
        .small { font-size: 11px; }
        .divider { border: 0; border-top: 1px dashed #94a3b8; margin: 14px 0; }
        .row { display: flex; justify-content: space-between; gap: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th { color: #64748b; font-size: 10px; text-transform: uppercase; letter-spacing: .05em; text-align: left; padding-bottom: 8px; }
        td { padding: 7px 0; vertical-align: top; border-bottom: 1px dashed #e2e8f0; }
        .right { text-align: right; }
        .product { font-weight: 700; }
        .total { font-size: 14px; font-weight: 900; }
        .footer { text-align: center; margin-top: 18px; }
        @media print {
            body { background: #fff; }
            .actions { display: none; }
            .page { margin: 0; width: 100%; max-width: none; box-shadow: none; padding: 0; }
            @page { size: 80mm auto; margin: 8mm; }
        }
    </style>
</head>
<body>
    <div class="actions">
        <a href="{{ route('sales.show', $sale) }}" class="btn">Kembali</a>
        <button onclick="window.print()" class="btn btn-primary">Cetak</button>
    </div>

    <main class="page">
        <header class="header">
            <div class="store-name">{{ $setting->store_name }}</div>
            @if ($setting->address)
                <div class="muted small" style="margin-top: 6px; line-height: 1.5;">{{ $setting->address }}</div>
            @endif
            @if ($setting->phone || $setting->email)
                <div class="muted small" style="margin-top: 4px;">{{ collect([$setting->phone, $setting->email])->filter()->join(' • ') }}</div>
            @endif
        </header>

        <hr class="divider">

        <section class="small">
            <div class="row"><span class="muted">Invoice</span><strong>{{ $sale->invoice_number }}</strong></div>
            <div class="row" style="margin-top: 6px;"><span class="muted">Tanggal</span><span>{{ $sale->sale_date->translatedFormat('d M Y H:i') }}</span></div>
            <div class="row" style="margin-top: 6px;"><span class="muted">Pelanggan</span><span>{{ $sale->customer_name ?: 'Umum' }}</span></div>
            <div class="row" style="margin-top: 6px;"><span class="muted">Kasir</span><span>{{ $sale->creator?->name ?? '-' }}</span></div>
        </section>

        <hr class="divider">

        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sale->items as $item)
                    <tr>
                        <td>
                            <div class="product">{{ $item->product_name_snapshot }}</div>
                            <div class="muted small">{{ number_format($item->quantity, 0, ',', '.') }} x Rp {{ number_format($item->price, 0, ',', '.') }}</div>
                        </td>
                        <td class="right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <section style="margin-top: 12px;">
            <div class="row"><span class="muted">Subtotal</span><span>Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</span></div>
            <div class="row" style="margin-top: 7px;"><span class="muted">Diskon</span><span>Rp {{ number_format($sale->discount, 0, ',', '.') }}</span></div>
            <hr class="divider" style="margin: 10px 0;">
            <div class="row total"><span>Total</span><span>Rp {{ number_format($sale->total, 0, ',', '.') }}</span></div>
            <div class="row" style="margin-top: 7px;"><span class="muted">Bayar</span><span>Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}</span></div>
            <div class="row" style="margin-top: 7px;"><span class="muted">Kembalian</span><span>Rp {{ number_format($sale->change_amount, 0, ',', '.') }}</span></div>
            <div class="row" style="margin-top: 7px;"><span class="muted">Metode</span><span>{{ $sale->payment_method ?: 'Tunai' }}</span></div>
        </section>

        @if ($sale->note)
            <hr class="divider">
            <div class="small"><strong>Catatan:</strong> {{ $sale->note }}</div>
        @endif

        <footer class="footer small muted">
            <hr class="divider">
            {{ $setting->receipt_footer ?: 'Terima kasih sudah berbelanja.' }}
        </footer>
    </main>
</body>
</html>
