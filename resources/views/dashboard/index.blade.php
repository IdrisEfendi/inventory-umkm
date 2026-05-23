@extends('layouts.admin')

@section('title', 'Dashboard')

@php
    $title = 'Dashboard';
    $subtitle = 'Ringkasan penjualan, stok, dan aktivitas toko hari ini.';
@endphp

@section('content')
<div class="space-y-6">
    <section class="overflow-hidden rounded-3xl border border-indigo-100 bg-gradient-to-br from-indigo-600 via-indigo-600 to-sky-500 p-6 text-white shadow-xl shadow-indigo-600/20 dark:border-indigo-400/20 dark:from-indigo-700 dark:via-indigo-700 dark:to-sky-700 sm:p-8">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-2xl">
                <x-admin.badge tone="indigo">Hari ini</x-admin.badge>
                <h2 class="mt-4 text-2xl font-black tracking-tight sm:text-3xl">Pantau stok dan penjualan dari satu tempat.</h2>
                <p class="mt-3 text-sm leading-6 text-indigo-100 sm:text-base">Ringkasan performa toko, produk yang perlu direstock, dan transaksi terakhir siap dipantau cepat.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('products.create') }}" class="inline-flex items-center justify-center rounded-xl bg-white/10 px-4 py-2.5 text-sm font-semibold text-white ring-1 ring-white/30 transition hover:bg-white/20 focus:outline-none focus:ring-4 focus:ring-white/20">
                    + Tambah Produk
                </a>
                <a href="{{ route('sales.create') }}" class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-indigo-700 shadow-sm transition hover:bg-indigo-50 focus:outline-none focus:ring-4 focus:ring-white/30">
                    + Catat Penjualan
                </a>
            </div>
        </div>
    </section>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-admin.kpi-card
            label="Penjualan Hari Ini"
            value="Rp {{ number_format($todayRevenue, 0, ',', '.') }}"
            description="Total omzet dari transaksi yang selesai hari ini."
            icon="Rp"
            tone="emerald"
        />

        <x-admin.kpi-card
            label="Transaksi Hari Ini"
            value="{{ number_format($todaySalesCount, 0, ',', '.') }}"
            description="Jumlah transaksi penjualan yang tercatat hari ini."
            icon="#"
            tone="indigo"
        />

        <x-admin.kpi-card
            label="Produk Aktif"
            value="{{ number_format($activeProductsCount, 0, ',', '.') }}"
            description="Produk yang tersedia dan aktif di katalog inventory."
            icon="📦"
            tone="sky"
        />

        <x-admin.kpi-card
            label="Stok Rendah"
            value="{{ number_format($lowStockProductsCount, 0, ',', '.') }}"
            description="Produk yang stoknya sudah di bawah atau sama dengan minimum."
            icon="!"
            tone="amber"
        />
    </section>

    <section class="grid gap-6 xl:grid-cols-[1.45fr_.85fr]">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-950">Penjualan 7 Hari Terakhir</h2>
                    <p class="mt-1 text-sm text-slate-500">Visual sederhana omzet harian. Chart.js bisa ditambahkan nanti.</p>
                </div>
                <a href="{{ route('sales.index') }}" class="inline-flex items-center rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700 ring-1 ring-indigo-100 transition hover:bg-indigo-100 dark:bg-indigo-500/15 dark:text-indigo-200 dark:ring-indigo-400/20">7 hari</a>
            </div>

            <div class="mt-6 flex h-72 items-end gap-3 rounded-2xl bg-slate-50 p-4">
                @php
                    $maxSales = max($salesChartValues->max(), 1);
                @endphp

                @foreach ($salesChartValues as $index => $value)
                    @php
                        $height = max(8, ((float) $value / $maxSales) * 100);
                    @endphp
                    <div class="flex min-w-0 flex-1 flex-col items-center gap-3">
                        <div class="flex h-52 w-full items-end rounded-xl bg-white px-2 py-2 ring-1 ring-slate-200">
                            <div class="w-full rounded-lg bg-gradient-to-t from-indigo-600 to-indigo-400" style="height: {{ $height }}%"></div>
                        </div>
                        <div class="text-center">
                            <p class="text-[11px] font-semibold text-slate-700">{{ $salesChartLabels[$index] }}</p>
                            <p class="mt-1 text-[10px] text-slate-400">{{ $value > 0 ? 'Rp'.number_format($value, 0, ',', '.') : 'Rp0' }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-950">Stok Rendah</h2>
                    <p class="mt-1 text-sm text-slate-500">Produk yang perlu segera direstock.</p>
                </div>
                <x-admin.badge :tone="$lowStockProductsCount > 0 ? 'amber' : 'emerald'">
                    {{ $lowStockProductsCount }} item
                </x-admin.badge>
            </div>

            <div class="mt-5 space-y-3">
                @forelse ($lowStockProducts as $product)
                    <div class="flex items-center justify-between gap-4 rounded-2xl border border-slate-200 p-4">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-bold text-slate-900">{{ $product->name }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $product->category?->name ?? 'Tanpa kategori' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-rose-600">{{ $product->stock }}</p>
                            <p class="text-xs text-slate-400">Min {{ $product->minimum_stock }}</p>
                        </div>
                    </div>
                @empty
                    <x-admin.empty-state
                        title="Semua stok aman"
                        description="Belum ada produk yang berada di bawah batas minimum stok."
                    />
                @endforelse
            </div>
        </div>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-950">Penjualan Terbaru</h2>
                <p class="mt-1 text-sm text-slate-500">Daftar transaksi terakhir yang tercatat di sistem.</p>
            </div>
            <a href="{{ route('sales.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:hover:bg-slate-800 dark:focus:ring-slate-700">
                Lihat Semua
            </a>
        </div>

        <div class="mt-5 overflow-x-auto">
            @if ($recentSales->isNotEmpty())
                <table class="min-w-[760px] w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 text-xs uppercase tracking-wide text-slate-500">
                            <th class="py-3 pr-4 font-bold">Invoice</th>
                            <th class="px-4 py-3 font-bold">Tanggal</th>
                            <th class="px-4 py-3 font-bold">Pelanggan</th>
                            <th class="px-4 py-3 font-bold">Item</th>
                            <th class="px-4 py-3 text-right font-bold">Total</th>
                            <th class="py-3 pl-4 text-right font-bold">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($recentSales as $sale)
                            <tr class="text-slate-700">
                                <td class="py-4 pr-4 font-semibold text-slate-950">{{ $sale->invoice_number }}</td>
                                <td class="px-4 py-4">{{ $sale->sale_date->translatedFormat('d M Y H:i') }}</td>
                                <td class="px-4 py-4">{{ $sale->customer_name ?: 'Umum' }}</td>
                                <td class="px-4 py-4">{{ $sale->items_count }} item</td>
                                <td class="px-4 py-4 text-right font-bold">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                                <td class="py-4 pl-4 text-right"><x-admin.badge tone="emerald">Selesai</x-admin.badge></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <x-admin.empty-state
                    title="Belum ada penjualan"
                    description="Mulai catat transaksi pertama agar performa toko muncul di dashboard."
                />
            @endif
        </div>
    </section>
</div>
@endsection
