@extends('layouts.admin')

@section('title', 'Penjualan')

@php
    $title = 'Penjualan';
    $subtitle = 'Kelola transaksi penjualan dan stok keluar otomatis.';
@endphp

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-950">Daftar Penjualan</h2>
            <p class="mt-1 text-sm text-slate-500">Pantau invoice, pelanggan, metode pembayaran, dan total transaksi.</p>
        </div>
        <a href="{{ route('sales.create') }}" class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200">+ Transaksi Baru</a>
    </div>

    @if (session('success'))
        <div role="status" class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">{{ session('success') }}</div>
    @endif

    <div class="grid gap-4 md:grid-cols-3">
        <x-admin.kpi-card label="Penjualan Hari Ini" :value="number_format($todaySalesCount, 0, ',', '.')" icon="🧾" tone="indigo" />
        <x-admin.kpi-card label="Omzet Hari Ini" :value="'Rp '.number_format($todayRevenue, 0, ',', '.')" icon="💰" tone="emerald" />
        <x-admin.kpi-card label="Omzet Bulan Ini" :value="'Rp '.number_format($monthRevenue, 0, ',', '.')" icon="📈" tone="sky" />
    </div>

    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <form method="GET" action="{{ route('sales.index') }}" class="grid gap-3 lg:grid-cols-[1fr_170px_170px_auto]">
            <input name="search" value="{{ $search }}" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100" placeholder="Cari invoice, pelanggan, catatan...">
            <input name="from" type="date" value="{{ $from }}" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
            <input name="to" type="date" value="{{ $to }}" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
            <div class="flex gap-2">
                <button class="rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800 focus:outline-none focus:ring-4 focus:ring-slate-200">Filter</button>
                <a href="{{ route('sales.index') }}" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Reset</a>
            </div>
        </form>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        @if ($sales->count())
            <div class="overflow-x-auto">
                <table class="min-w-[920px] w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-4 font-bold">Invoice</th>
                            <th class="px-5 py-4 font-bold">Tanggal</th>
                            <th class="px-5 py-4 font-bold">Pelanggan</th>
                            <th class="px-5 py-4 text-right font-bold">Item</th>
                            <th class="px-5 py-4 text-right font-bold">Total</th>
                            <th class="px-5 py-4 font-bold">Pembayaran</th>
                            <th class="px-5 py-4 text-right font-bold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($sales as $sale)
                            <tr class="text-slate-700">
                                <td class="px-5 py-4 font-mono text-xs font-bold text-slate-950">{{ $sale->invoice_number }}</td>
                                <td class="px-5 py-4 whitespace-nowrap">{{ $sale->sale_date->format('d M Y H:i') }}</td>
                                <td class="px-5 py-4">{{ $sale->customer_name ?: 'Umum' }}</td>
                                <td class="px-5 py-4 text-right">{{ number_format($sale->items_count, 0, ',', '.') }}</td>
                                <td class="px-5 py-4 text-right font-bold text-slate-950">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                                <td class="px-5 py-4"><x-admin.badge tone="emerald">{{ $sale->payment_method ?: 'Tunai' }}</x-admin.badge></td>
                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('sales.show', $sale) }}" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">Detail</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-200 px-5 py-4">{{ $sales->links() }}</div>
        @else
            <div class="p-6">
                <x-admin.empty-state title="Belum ada penjualan" description="Buat transaksi penjualan pertama. Stok produk akan otomatis berkurang." action-label="Transaksi Baru" :action-url="route('sales.create')" />
            </div>
        @endif
    </section>
</div>
@endsection
