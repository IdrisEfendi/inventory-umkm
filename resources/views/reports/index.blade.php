@extends('layouts.admin')

@section('title', 'Laporan')

@php
    $title = 'Laporan';
    $subtitle = 'Analisis penjualan, stok, dan performa produk.';
    $periodLabels = [
        'today' => 'Hari Ini',
        'last_7_days' => '7 Hari Terakhir',
        'this_month' => 'Bulan Ini',
        'this_year' => 'Tahun Ini',
        'custom' => 'Custom',
    ];
    $maxSales = max($chartValues->max(), 1);
    $exportParams = request()->only(['period', 'from', 'to']);
@endphp

@section('content')
<div class="space-y-6">
    <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900 sm:p-6">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <x-admin.badge tone="indigo">{{ $periodLabels[$period] ?? 'Bulan Ini' }}</x-admin.badge>
                <h2 class="mt-3 text-xl font-black text-slate-950 dark:text-white">Laporan Periode {{ \Illuminate\Support\Carbon::parse($from)->translatedFormat('d M Y') }} - {{ \Illuminate\Support\Carbon::parse($to)->translatedFormat('d M Y') }}</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Gunakan filter untuk melihat omzet, transaksi, stok, dan produk terlaris per periode.</p>
            </div>

            <div class="space-y-3">
                <form method="GET" action="{{ route('reports.index') }}" class="grid gap-3 sm:grid-cols-[180px_150px_150px_auto]">
                    <select name="period" id="period" class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-indigo-500/20">
                        @foreach ($periodLabels as $value => $label)
                            <option value="{{ $value }}" @selected($period === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <input name="from" type="date" value="{{ $from }}" class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-indigo-500/20">
                    <input name="to" type="date" value="{{ $to }}" class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-indigo-500/20">
                    <button class="rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800 focus:outline-none focus:ring-4 focus:ring-slate-200 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200 dark:focus:ring-slate-700">Terapkan</button>
                </form>

                <div class="flex flex-col gap-2 sm:flex-row sm:justify-end">
                    <a href="{{ route('reports.print', $exportParams) }}" target="_blank" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">Print Laporan</a>
                    <a href="{{ route('reports.export-csv', $exportParams) }}" class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-200 dark:bg-emerald-500 dark:hover:bg-emerald-400">Export CSV</a>
                </div>
            </div>
        </div>
    </section>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-admin.kpi-card label="Omzet" :value="'Rp '.number_format($revenue, 0, ',', '.')" description="Total pendapatan bersih setelah diskon pada periode ini." icon="Rp" tone="emerald" />
        <x-admin.kpi-card label="Transaksi" :value="number_format($transactions, 0, ',', '.')" description="Jumlah invoice penjualan yang tercatat." icon="🧾" tone="indigo" />
        <x-admin.kpi-card label="Item Terjual" :value="number_format($soldQuantity, 0, ',', '.')" description="Akumulasi kuantitas produk yang keluar lewat penjualan." icon="📦" tone="sky" />
        <x-admin.kpi-card label="Rata-rata Transaksi" :value="'Rp '.number_format($averageTransaction, 0, ',', '.')" description="Nilai rata-rata dari setiap transaksi." icon="≈" tone="amber" />
    </section>

    <section class="grid gap-6 xl:grid-cols-[1.4fr_.9fr]">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-950 dark:text-white">Tren Omzet Harian</h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Grafik sederhana berdasarkan tanggal penjualan.</p>
                </div>
                <x-admin.badge tone="indigo">{{ $chartLabels->count() }} hari</x-admin.badge>
            </div>

            <div class="mt-6 flex h-72 items-end gap-3 overflow-x-auto rounded-2xl bg-slate-50 p-4 dark:bg-slate-800">
                @foreach ($chartValues as $index => $value)
                    @php($height = max(8, ((float) $value / $maxSales) * 100))
                    <div class="flex min-w-12 flex-1 flex-col items-center gap-3">
                        <div class="flex h-52 w-full min-w-10 items-end rounded-xl bg-white px-2 py-2 ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-700">
                            <div class="w-full rounded-lg bg-gradient-to-t from-indigo-600 to-sky-400" style="height: {{ $height }}%"></div>
                        </div>
                        <div class="text-center">
                            <p class="text-[11px] font-semibold text-slate-700 dark:text-slate-200">{{ $chartLabels[$index] }}</p>
                            <p class="mt-1 text-[10px] text-slate-400">{{ $value > 0 ? 'Rp'.number_format($value, 0, ',', '.') : 'Rp0' }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900">
                <h2 class="text-lg font-bold text-slate-950 dark:text-white">Ringkasan Stok</h2>
                <div class="mt-5 grid gap-3">
                    <div class="flex items-center justify-between rounded-2xl bg-slate-50 p-4 dark:bg-slate-800"><span class="text-sm font-semibold text-slate-600 dark:text-slate-300">Stok Masuk</span><span class="font-bold text-emerald-600">{{ number_format($stockIn, 0, ',', '.') }}</span></div>
                    <div class="flex items-center justify-between rounded-2xl bg-slate-50 p-4 dark:bg-slate-800"><span class="text-sm font-semibold text-slate-600 dark:text-slate-300">Stok Keluar</span><span class="font-bold text-rose-600">{{ number_format($stockOut, 0, ',', '.') }}</span></div>
                    <div class="flex items-center justify-between rounded-2xl bg-slate-50 p-4 dark:bg-slate-800"><span class="text-sm font-semibold text-slate-600 dark:text-slate-300">Penyesuaian</span><span class="font-bold text-amber-600">{{ number_format($stockAdjustments, 0, ',', '.') }}</span></div>
                    <div class="flex items-center justify-between rounded-2xl bg-slate-50 p-4 dark:bg-slate-800"><span class="text-sm font-semibold text-slate-600 dark:text-slate-300">Nilai Inventory</span><span class="font-bold text-slate-950 dark:text-white">Rp {{ number_format($inventoryValue, 0, ',', '.') }}</span></div>
                </div>
            </div>
        </div>
    </section>

    <section class="grid gap-6 xl:grid-cols-2">
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900">
            <div class="border-b border-slate-200 p-6 dark:border-slate-700">
                <h2 class="text-lg font-bold text-slate-950 dark:text-white">Produk Terlaris</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Diurutkan berdasarkan jumlah item terjual.</p>
            </div>
            @if ($topProducts->count())
                <div class="overflow-x-auto">
                    <table class="min-w-[640px] w-full text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-800">
                            <tr>
                                <th class="px-5 py-4 font-bold">Produk</th>
                                <th class="px-5 py-4 text-right font-bold">Terjual</th>
                                <th class="px-5 py-4 text-right font-bold">Omzet</th>
                                <th class="px-5 py-4 text-right font-bold">Stok</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach ($topProducts as $product)
                                <tr>
                                    <td class="px-5 py-4">
                                        <p class="font-bold text-slate-950 dark:text-white">{{ $product->product_name_snapshot }}</p>
                                        <p class="mt-1 font-mono text-xs text-slate-500">{{ $product->product_sku_snapshot ?: 'Tanpa SKU' }}</p>
                                    </td>
                                    <td class="px-5 py-4 text-right font-semibold">{{ number_format($product->total_quantity, 0, ',', '.') }}</td>
                                    <td class="px-5 py-4 text-right font-bold text-slate-950 dark:text-white">Rp {{ number_format($product->total_revenue, 0, ',', '.') }}</td>
                                    <td class="px-5 py-4 text-right">{{ number_format($product->current_stock, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-6"><x-admin.empty-state title="Belum ada data produk terlaris" description="Data akan muncul setelah ada transaksi pada periode ini." /></div>
            @endif
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900">
            <div class="border-b border-slate-200 p-6 dark:border-slate-700">
                <h2 class="text-lg font-bold text-slate-950 dark:text-white">Stok Rendah</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Produk aktif yang perlu segera direstock.</p>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    @forelse ($lowStockProducts as $product)
                        <div class="flex items-center justify-between gap-4 rounded-2xl border border-slate-200 p-4 dark:border-slate-700">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-bold text-slate-900 dark:text-white">{{ $product->name }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $product->category?->name ?? 'Tanpa kategori' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-rose-600">{{ number_format($product->stock, 0, ',', '.') }}</p>
                                <p class="text-xs text-slate-400">Min {{ number_format($product->minimum_stock, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    @empty
                        <x-admin.empty-state title="Semua stok aman" description="Tidak ada produk aktif yang berada di bawah batas minimum." />
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900">
        <div class="flex flex-col gap-3 border-b border-slate-200 p-6 dark:border-slate-700 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-950 dark:text-white">Transaksi Terbaru</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Transaksi terakhir dalam periode laporan.</p>
            </div>
            <a href="{{ route('sales.index', ['from' => $from, 'to' => $to]) }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">Lihat Penjualan</a>
        </div>
        @if ($recentSales->count())
            <div class="overflow-x-auto">
                <table class="min-w-[760px] w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-800">
                        <tr>
                            <th class="px-5 py-4 font-bold">Invoice</th>
                            <th class="px-5 py-4 font-bold">Tanggal</th>
                            <th class="px-5 py-4 font-bold">Pelanggan</th>
                            <th class="px-5 py-4 text-right font-bold">Item</th>
                            <th class="px-5 py-4 text-right font-bold">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach ($recentSales as $sale)
                            <tr>
                                <td class="px-5 py-4 font-mono text-xs font-bold text-slate-950 dark:text-white">{{ $sale->invoice_number }}</td>
                                <td class="px-5 py-4 whitespace-nowrap">{{ $sale->sale_date->translatedFormat('d M Y H:i') }}</td>
                                <td class="px-5 py-4">{{ $sale->customer_name ?: 'Umum' }}</td>
                                <td class="px-5 py-4 text-right">{{ number_format($sale->items_count, 0, ',', '.') }}</td>
                                <td class="px-5 py-4 text-right font-bold text-slate-950 dark:text-white">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-6"><x-admin.empty-state title="Belum ada transaksi" description="Belum ada penjualan yang tercatat pada periode ini." /></div>
        @endif
    </section>
</div>
@endsection
