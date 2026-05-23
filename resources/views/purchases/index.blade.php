@extends('layouts.admin')

@section('title', 'Pembelian / Restock')

@php
    $title = 'Pembelian / Restock';
    $subtitle = 'Catat pembelian supplier dan stok masuk otomatis.';
    $statusLabels = ['paid' => 'Lunas', 'partial' => 'Sebagian', 'unpaid' => 'Belum Lunas'];
    $statusTones = ['paid' => 'emerald', 'partial' => 'amber', 'unpaid' => 'rose'];
@endphp

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-950 dark:text-white">Daftar Pembelian</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Kelola invoice pembelian/restock dari supplier.</p>
        </div>
        <a href="{{ route('purchases.create') }}" class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200 dark:bg-indigo-500 dark:hover:bg-indigo-400 dark:focus:ring-indigo-500/30">+ Pembelian Baru</a>
    </div>

    @if (session('success'))
        <div role="status" class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700 dark:border-emerald-400/20 dark:bg-emerald-500/15 dark:text-emerald-200">{{ session('success') }}</div>
    @endif

    <div class="grid gap-4 md:grid-cols-3">
        <x-admin.kpi-card label="Pembelian Hari Ini" :value="number_format($todayPurchasesCount, 0, ',', '.')" icon="🛒" tone="indigo" />
        <x-admin.kpi-card label="Total Hari Ini" :value="'Rp '.number_format($todayPurchasesTotal, 0, ',', '.')" icon="Rp" tone="emerald" />
        <x-admin.kpi-card label="Total Bulan Ini" :value="'Rp '.number_format($monthPurchasesTotal, 0, ',', '.')" icon="📈" tone="sky" />
    </div>

    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900">
        <form method="GET" action="{{ route('purchases.index') }}" class="grid gap-3 xl:grid-cols-[1fr_180px_160px_150px_150px_auto]">
            <input name="search" value="{{ $search }}" class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-indigo-500/20" placeholder="Cari nomor, supplier, catatan...">
            <select name="supplier_id" class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-indigo-500/20">
                <option value="">Semua Supplier</option>
                @foreach ($suppliers as $supplier)
                    <option value="{{ $supplier->id }}" @selected((string) $supplierId === (string) $supplier->id)>{{ $supplier->name }}</option>
                @endforeach
            </select>
            <select name="payment_status" class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-indigo-500/20">
                <option value="">Semua Status</option>
                @foreach ($statusLabels as $value => $label)
                    <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <input name="from" type="date" value="{{ $from }}" class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-indigo-500/20">
            <input name="to" type="date" value="{{ $to }}" class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-indigo-500/20">
            <div class="flex gap-2">
                <button class="rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800 focus:outline-none focus:ring-4 focus:ring-slate-200 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200 dark:focus:ring-slate-700">Filter</button>
                <a href="{{ route('purchases.index') }}" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">Reset</a>
            </div>
        </form>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900">
        @if ($purchases->count())
            <div class="overflow-x-auto">
                <table class="min-w-[980px] w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-800">
                        <tr>
                            <th class="px-5 py-4 font-bold">Nomor</th>
                            <th class="px-5 py-4 font-bold">Tanggal</th>
                            <th class="px-5 py-4 font-bold">Supplier</th>
                            <th class="px-5 py-4 text-right font-bold">Item</th>
                            <th class="px-5 py-4 text-right font-bold">Total</th>
                            <th class="px-5 py-4 font-bold">Status</th>
                            <th class="px-5 py-4 text-right font-bold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach ($purchases as $purchase)
                            <tr class="text-slate-700 dark:text-slate-200">
                                <td class="px-5 py-4 font-mono text-xs font-bold text-slate-950 dark:text-white">{{ $purchase->purchase_number }}</td>
                                <td class="px-5 py-4 whitespace-nowrap">{{ $purchase->purchase_date->translatedFormat('d M Y H:i') }}</td>
                                <td class="px-5 py-4">{{ $purchase->supplier?->name ?? 'Tanpa supplier' }}</td>
                                <td class="px-5 py-4 text-right">{{ number_format($purchase->items_count, 0, ',', '.') }}</td>
                                <td class="px-5 py-4 text-right font-bold text-slate-950 dark:text-white">Rp {{ number_format($purchase->total, 0, ',', '.') }}</td>
                                <td class="px-5 py-4"><x-admin.badge :tone="$statusTones[$purchase->payment_status] ?? 'slate'">{{ $statusLabels[$purchase->payment_status] ?? $purchase->payment_status }}</x-admin.badge></td>
                                <td class="px-5 py-4 text-right"><a href="{{ route('purchases.show', $purchase) }}" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">Detail</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-200 px-5 py-4 dark:border-slate-700">{{ $purchases->links() }}</div>
        @else
            <div class="p-6"><x-admin.empty-state title="Belum ada pembelian" description="Catat pembelian/restock pertama agar stok produk otomatis bertambah." action-label="Pembelian Baru" :action-url="route('purchases.create')" /></div>
        @endif
    </section>
</div>
@endsection
