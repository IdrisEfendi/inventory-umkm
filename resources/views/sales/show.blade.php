@extends('layouts.admin')

@section('title', 'Detail Penjualan')

@php
    $title = 'Detail Penjualan';
    $subtitle = $sale->invoice_number;
@endphp

@section('content')
<div class="space-y-6">
    @if (session('success'))
        <div role="status" class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">{{ session('success') }}</div>
    @endif

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-950">{{ $sale->invoice_number }}</h2>
            <p class="mt-1 text-sm text-slate-500">{{ $sale->sale_date->format('d M Y H:i') }} • {{ $sale->customer_name ?: 'Pelanggan umum' }}</p>
        </div>
        <a href="{{ route('sales.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">← Daftar Penjualan</a>
    </div>

    <div class="grid gap-6 lg:grid-cols-[1fr_360px]">
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4">
                <h3 class="font-bold text-slate-950">Item Transaksi</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-[720px] w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-4 font-bold">Produk</th>
                            <th class="px-5 py-4 text-right font-bold">Harga</th>
                            <th class="px-5 py-4 text-right font-bold">Qty</th>
                            <th class="px-5 py-4 text-right font-bold">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($sale->items as $item)
                            <tr class="text-slate-700">
                                <td class="px-5 py-4">
                                    <p class="font-bold text-slate-950">{{ $item->product_name_snapshot }}</p>
                                    <p class="mt-1 font-mono text-xs text-slate-500">{{ $item->product_sku_snapshot ?: 'Tanpa SKU' }}</p>
                                </td>
                                <td class="px-5 py-4 text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                <td class="px-5 py-4 text-right">{{ number_format($item->quantity, 0, ',', '.') }}</td>
                                <td class="px-5 py-4 text-right font-bold text-slate-950">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <aside class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="text-lg font-bold text-slate-950">Ringkasan</h3>
            <dl class="mt-4 space-y-3 text-sm">
                <div class="flex justify-between"><dt class="text-slate-500">Subtotal</dt><dd class="font-semibold">Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Diskon</dt><dd class="font-semibold">Rp {{ number_format($sale->discount, 0, ',', '.') }}</dd></div>
                <div class="flex justify-between border-t border-slate-200 pt-3 text-base"><dt class="font-bold text-slate-700">Total</dt><dd class="font-bold text-slate-950">Rp {{ number_format($sale->total, 0, ',', '.') }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Bayar</dt><dd class="font-semibold">Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Kembalian</dt><dd class="font-semibold">Rp {{ number_format($sale->change_amount, 0, ',', '.') }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Metode</dt><dd><x-admin.badge tone="emerald">{{ $sale->payment_method ?: 'Tunai' }}</x-admin.badge></dd></div>
                <div class="border-t border-slate-200 pt-3"><dt class="text-slate-500">Catatan</dt><dd class="mt-1 font-medium text-slate-700">{{ $sale->note ?: '-' }}</dd></div>
                <div><dt class="text-slate-500">Dibuat oleh</dt><dd class="mt-1 font-medium text-slate-700">{{ $sale->creator?->name ?? '-' }}</dd></div>
            </dl>
        </aside>
    </div>
</div>
@endsection
