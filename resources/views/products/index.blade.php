@extends('layouts.admin')

@section('title', 'Produk')

@php
    $title = 'Produk';
    $subtitle = 'Kelola katalog produk, harga, stok, dan status inventory.';
@endphp

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-950">Daftar Produk</h2>
            <p class="mt-1 text-sm text-slate-500">Pantau produk, harga jual, stok, dan batas minimum.</p>
        </div>
        <a href="{{ route('products.create') }}" class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200">+ Tambah Produk</a>
    </div>

    @if (session('success'))
        <div role="status" class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">{{ session('success') }}</div>
    @endif

    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <form method="GET" action="{{ route('products.index') }}" class="grid gap-3 xl:grid-cols-[1fr_180px_180px_150px_150px_auto]">
            <input name="search" value="{{ $search }}" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100" placeholder="Cari nama, SKU, deskripsi...">
            <select name="category_id" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                <option value="">Semua Kategori</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected((string) $categoryId === (string) $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
            <select name="supplier_id" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                <option value="">Semua Supplier</option>
                @foreach ($suppliers as $supplier)
                    <option value="{{ $supplier->id }}" @selected((string) $supplierId === (string) $supplier->id)>{{ $supplier->name }}</option>
                @endforeach
            </select>
            <select name="status" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                <option value="">Semua Status</option>
                <option value="active" @selected($status === 'active')>Aktif</option>
                <option value="inactive" @selected($status === 'inactive')>Nonaktif</option>
            </select>
            <select name="stock" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                <option value="">Semua Stok</option>
                <option value="low" @selected($stock === 'low')>Stok Rendah</option>
            </select>
            <div class="flex gap-2">
                <button class="rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800 focus:outline-none focus:ring-4 focus:ring-slate-200">Filter</button>
                <a href="{{ route('products.index') }}" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Reset</a>
            </div>
        </form>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        @if ($products->count())
            <div class="overflow-x-auto">
                <table class="min-w-[1080px] w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-4 font-bold">Produk</th>
                            <th class="px-5 py-4 font-bold">Kategori</th>
                            <th class="px-5 py-4 font-bold">Supplier</th>
                            <th class="px-5 py-4 text-right font-bold">Harga Beli</th>
                            <th class="px-5 py-4 text-right font-bold">Harga Jual</th>
                            <th class="px-5 py-4 text-right font-bold">Stok</th>
                            <th class="px-5 py-4 font-bold">Status</th>
                            <th class="px-5 py-4 text-right font-bold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($products as $product)
                            <tr class="text-slate-700">
                                <td class="px-5 py-4">
                                    <p class="font-bold text-slate-950">{{ $product->name }}</p>
                                    <p class="mt-1 font-mono text-xs text-slate-500">{{ $product->sku ?: 'Tanpa SKU' }}</p>
                                    <p class="mt-1 max-w-md truncate text-xs text-slate-400">{{ $product->description ?: 'Tidak ada deskripsi' }}</p>
                                </td>
                                <td class="px-5 py-4">{{ $product->category?->name ?? 'Tanpa kategori' }}</td>
                                <td class="px-5 py-4">{{ $product->supplier?->name ?? 'Tanpa supplier' }}</td>
                                <td class="px-5 py-4 text-right">Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</td>
                                <td class="px-5 py-4 text-right font-semibold text-slate-950">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</td>
                                <td class="px-5 py-4 text-right">
                                    <div class="flex flex-col items-end gap-1">
                                        <span class="font-bold {{ $product->is_low_stock ? 'text-rose-600' : 'text-slate-950' }}">{{ number_format($product->stock, 0, ',', '.') }}</span>
                                        <span class="text-xs text-slate-400">Min {{ number_format($product->minimum_stock, 0, ',', '.') }}</span>
                                        @if ($product->is_low_stock)
                                            <x-admin.badge tone="rose">Stok Rendah</x-admin.badge>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-4"><x-admin.badge :tone="$product->is_active ? 'emerald' : 'slate'">{{ $product->is_active ? 'Aktif' : 'Nonaktif' }}</x-admin.badge></td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('products.edit', $product) }}" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">Edit</a>
                                        <form method="POST" action="{{ route('products.toggle', $product) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">{{ $product->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                                        </form>
                                        <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('Hapus produk ini? Jika sudah punya riwayat, produk akan dinonaktifkan.');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="rounded-lg bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 transition hover:bg-rose-100">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-200 px-5 py-4">{{ $products->links() }}</div>
        @else
            <div class="p-6">
                <x-admin.empty-state title="Belum ada produk" description="Tambahkan produk pertama agar stok dan penjualan bisa mulai dilacak." action-label="Tambah Produk" :action-url="route('products.create')" />
            </div>
        @endif
    </section>
</div>
@endsection
