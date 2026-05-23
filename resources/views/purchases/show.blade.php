@extends('layouts.admin')

@section('title', 'Detail Pembelian')

@php
    $title = 'Detail Pembelian';
    $subtitle = $purchase->purchase_number;
    $statusTones = ['paid' => 'emerald', 'partial' => 'amber', 'unpaid' => 'rose'];
@endphp

@section('content')
<div class="space-y-6">
    @if (session('success'))
        <div role="status" class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700 dark:border-emerald-400/20 dark:bg-emerald-500/15 dark:text-emerald-200">{{ session('success') }}</div>
    @endif

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-950 dark:text-white">{{ $purchase->purchase_number }}</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $purchase->purchase_date->translatedFormat('d M Y H:i') }} • {{ $purchase->supplier?->name ?? 'Tanpa supplier' }}</p>
        </div>
        <div class="flex flex-col gap-2 sm:flex-row">
            <a href="{{ route('purchases.create') }}" class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200 dark:bg-indigo-500 dark:hover:bg-indigo-400">Pembelian Baru</a>
            <a href="{{ route('purchases.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">← Daftar Pembelian</a>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-[1fr_360px]">
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900">
            <div class="border-b border-slate-200 px-5 py-4 dark:border-slate-700">
                <h3 class="font-bold text-slate-950 dark:text-white">Item Restock</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-[720px] w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-800">
                        <tr>
                            <th class="px-5 py-4 font-bold">Produk</th>
                            <th class="px-5 py-4 text-right font-bold">Harga Modal</th>
                            <th class="px-5 py-4 text-right font-bold">Qty</th>
                            <th class="px-5 py-4 text-right font-bold">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach ($purchase->items as $item)
                            <tr class="text-slate-700 dark:text-slate-200">
                                <td class="px-5 py-4">
                                    <p class="font-bold text-slate-950 dark:text-white">{{ $item->product_name_snapshot }}</p>
                                    <p class="mt-1 font-mono text-xs text-slate-500">{{ $item->product_sku_snapshot ?: 'Tanpa SKU' }}</p>
                                </td>
                                <td class="px-5 py-4 text-right">Rp {{ number_format($item->cost_price, 0, ',', '.') }}</td>
                                <td class="px-5 py-4 text-right">{{ number_format($item->quantity, 0, ',', '.') }}</td>
                                <td class="px-5 py-4 text-right font-bold text-slate-950 dark:text-white">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <aside class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900">
            <h3 class="text-lg font-bold text-slate-950 dark:text-white">Ringkasan</h3>
            <dl class="mt-4 space-y-3 text-sm">
                <div class="flex justify-between"><dt class="text-slate-500 dark:text-slate-400">Supplier</dt><dd class="font-semibold text-right">{{ $purchase->supplier?->name ?? '-' }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500 dark:text-slate-400">Subtotal</dt><dd class="font-semibold">Rp {{ number_format($purchase->subtotal, 0, ',', '.') }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500 dark:text-slate-400">Diskon</dt><dd class="font-semibold">Rp {{ number_format($purchase->discount, 0, ',', '.') }}</dd></div>
                <div class="flex justify-between border-t border-slate-200 pt-3 text-base dark:border-slate-700"><dt class="font-bold text-slate-700 dark:text-slate-200">Total</dt><dd class="font-bold text-slate-950 dark:text-white">Rp {{ number_format($purchase->total, 0, ',', '.') }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500 dark:text-slate-400">Status</dt><dd><x-admin.badge :tone="$statusTones[$purchase->payment_status] ?? 'slate'">{{ $paymentStatuses[$purchase->payment_status] ?? $purchase->payment_status }}</x-admin.badge></dd></div>
                <div class="border-t border-slate-200 pt-3 dark:border-slate-700"><dt class="text-slate-500 dark:text-slate-400">Catatan</dt><dd class="mt-1 font-medium text-slate-700 dark:text-slate-200">{{ $purchase->note ?: '-' }}</dd></div>
                <div><dt class="text-slate-500 dark:text-slate-400">Dibuat oleh</dt><dd class="mt-1 font-medium text-slate-700 dark:text-slate-200">{{ $purchase->creator?->name ?? '-' }}</dd></div>
            </dl>
        </aside>
    </div>
</div>
@endsection
