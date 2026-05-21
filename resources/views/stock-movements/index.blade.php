@extends('layouts.admin')

@section('title', 'Stock Movement')

@php
    $title = 'Stock Movement';
    $subtitle = 'Catat stok masuk, stok keluar, dan adjustment stok akhir.';
    $typeLabels = [
        'in' => 'Stok Masuk',
        'out' => 'Stok Keluar',
        'adjustment' => 'Adjustment',
    ];
    $typeTones = [
        'in' => 'emerald',
        'out' => 'rose',
        'adjustment' => 'indigo',
    ];
@endphp

@section('content')
<div class="space-y-6">
    @if (session('success'))
        <div role="status" class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700">
            <p>Periksa kembali input stok:</p>
            <ul class="mt-2 list-disc space-y-1 pl-5 font-medium">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid gap-4 md:grid-cols-3">
        <x-admin.kpi-card label="Total Movement" :value="number_format($totalMovements, 0, ',', '.')" icon="⇄" tone="indigo" />
        <x-admin.kpi-card label="Total Stok" :value="number_format($totalStock, 0, ',', '.')" icon="📦" tone="emerald" />
        <x-admin.kpi-card label="Produk Stok Rendah" :value="number_format($lowStockProductsCount, 0, ',', '.')" icon="⚠️" tone="amber" />
    </div>

    <div class="grid gap-6 xl:grid-cols-[420px_1fr]">
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-5">
                <h2 class="text-lg font-bold text-slate-950">Input Movement</h2>
                <p class="mt-1 text-sm text-slate-500">Adjustment akan menjadikan jumlah sebagai stok akhir produk.</p>
            </div>

            <form method="POST" action="{{ route('stock-movements.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="product_id" class="mb-2 block text-sm font-bold text-slate-700">Produk</label>
                    <select id="product_id" name="product_id" required class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                        <option value="">Pilih produk</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" @selected(old('product_id') == $product->id)>
                                {{ $product->name }}{{ $product->sku ? ' — '.$product->sku : '' }} (stok {{ number_format($product->stock, 0, ',', '.') }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="type" class="mb-2 block text-sm font-bold text-slate-700">Jenis Movement</label>
                    <select id="type" name="type" required class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                        @foreach ($typeLabels as $value => $label)
                            <option value="{{ $value }}" @selected(old('type') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="quantity" class="mb-2 block text-sm font-bold text-slate-700">Jumlah / Stok Akhir</label>
                    <input id="quantity" name="quantity" type="number" min="0" required value="{{ old('quantity') }}" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100" placeholder="Contoh: 10">
                    <p class="mt-2 text-xs text-slate-500">Untuk adjustment, isi dengan stok akhir yang benar.</p>
                </div>

                <div>
                    <label for="note" class="mb-2 block text-sm font-bold text-slate-700">Catatan</label>
                    <textarea id="note" name="note" rows="4" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100" placeholder="Contoh: pembelian supplier, retur, koreksi opname...">{{ old('note') }}</textarea>
                </div>

                <button class="w-full rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200">
                    Simpan Movement
                </button>
            </form>
        </section>

        <section class="space-y-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <form method="GET" action="{{ route('stock-movements.index') }}" class="grid gap-3 lg:grid-cols-[1fr_180px_190px_auto]">
                    <input name="search" value="{{ $search }}" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100" placeholder="Cari produk, SKU, catatan...">
                    <select name="type" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                        <option value="">Semua Jenis</option>
                        @foreach ($typeLabels as $value => $label)
                            <option value="{{ $value }}" @selected($type === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <select name="product_id" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                        <option value="">Semua Produk</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" @selected((string) $productId === (string) $product->id)>{{ $product->name }}</option>
                        @endforeach
                    </select>
                    <div class="flex gap-2">
                        <button class="rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800 focus:outline-none focus:ring-4 focus:ring-slate-200">Filter</button>
                        <a href="{{ route('stock-movements.index') }}" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Reset</a>
                    </div>
                </form>
            </div>

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                @if ($movements->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-[920px] w-full text-left text-sm">
                            <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                                <tr>
                                    <th class="px-5 py-4 font-bold">Tanggal</th>
                                    <th class="px-5 py-4 font-bold">Produk</th>
                                    <th class="px-5 py-4 font-bold">Jenis</th>
                                    <th class="px-5 py-4 text-right font-bold">Jumlah</th>
                                    <th class="px-5 py-4 text-right font-bold">Sebelum</th>
                                    <th class="px-5 py-4 text-right font-bold">Sesudah</th>
                                    <th class="px-5 py-4 font-bold">Catatan</th>
                                    <th class="px-5 py-4 font-bold">User</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($movements as $movement)
                                    <tr class="text-slate-700">
                                        <td class="px-5 py-4 whitespace-nowrap">{{ $movement->created_at->format('d M Y H:i') }}</td>
                                        <td class="px-5 py-4">
                                            <p class="font-bold text-slate-950">{{ $movement->product->name }}</p>
                                            <p class="mt-1 font-mono text-xs text-slate-500">{{ $movement->product->sku ?: 'Tanpa SKU' }}</p>
                                        </td>
                                        <td class="px-5 py-4"><x-admin.badge :tone="$typeTones[$movement->type] ?? 'slate'">{{ $typeLabels[$movement->type] ?? $movement->type }}</x-admin.badge></td>
                                        <td class="px-5 py-4 text-right font-semibold">{{ number_format($movement->quantity, 0, ',', '.') }}</td>
                                        <td class="px-5 py-4 text-right">{{ number_format($movement->stock_before, 0, ',', '.') }}</td>
                                        <td class="px-5 py-4 text-right font-bold text-slate-950">{{ number_format($movement->stock_after, 0, ',', '.') }}</td>
                                        <td class="px-5 py-4 max-w-xs text-slate-500">{{ $movement->note ?: '-' }}</td>
                                        <td class="px-5 py-4 whitespace-nowrap">{{ $movement->creator?->name ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="border-t border-slate-200 px-5 py-4">{{ $movements->links() }}</div>
                @else
                    <div class="p-6">
                        <x-admin.empty-state title="Belum ada stock movement" description="Gunakan form di samping untuk mencatat stok masuk, keluar, atau adjustment." />
                    </div>
                @endif
            </div>
        </section>
    </div>
</div>
@endsection
