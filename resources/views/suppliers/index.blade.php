@extends('layouts.admin')

@section('title', 'Supplier')

@php
    $title = 'Supplier';
    $subtitle = 'Kelola data pemasok dan relasi produk.';
@endphp

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-950 dark:text-white">Daftar Supplier</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Simpan kontak pemasok untuk memudahkan restock dan pelacakan produk.</p>
        </div>
        <a href="{{ route('suppliers.create') }}" class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200 dark:bg-indigo-500 dark:hover:bg-indigo-400 dark:focus:ring-indigo-500/30">+ Tambah Supplier</a>
    </div>

    @if (session('success'))
        <div role="status" class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700 dark:border-emerald-400/20 dark:bg-emerald-500/15 dark:text-emerald-200">{{ session('success') }}</div>
    @endif

    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900">
        <form method="GET" action="{{ route('suppliers.index') }}" class="grid gap-3 md:grid-cols-[1fr_180px_auto]">
            <input name="search" value="{{ $search }}" class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-indigo-500/20" placeholder="Cari nama, kontak, telepon, email, alamat...">
            <select name="status" class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-indigo-500/20">
                <option value="">Semua Status</option>
                <option value="active" @selected($status === 'active')>Aktif</option>
                <option value="inactive" @selected($status === 'inactive')>Nonaktif</option>
            </select>
            <div class="flex gap-2">
                <button class="rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800 focus:outline-none focus:ring-4 focus:ring-slate-200 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200 dark:focus:ring-slate-700">Filter</button>
                <a href="{{ route('suppliers.index') }}" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">Reset</a>
            </div>
        </form>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900">
        @if ($suppliers->count())
            <div class="overflow-x-auto">
                <table class="min-w-[980px] w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-800">
                        <tr>
                            <th class="px-5 py-4 font-bold">Supplier</th>
                            <th class="px-5 py-4 font-bold">Kontak</th>
                            <th class="px-5 py-4 font-bold">Alamat</th>
                            <th class="px-5 py-4 font-bold">Produk</th>
                            <th class="px-5 py-4 font-bold">Status</th>
                            <th class="px-5 py-4 text-right font-bold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach ($suppliers as $supplier)
                            <tr class="text-slate-700 dark:text-slate-200">
                                <td class="px-5 py-4">
                                    <p class="font-bold text-slate-950 dark:text-white">{{ $supplier->name }}</p>
                                    <p class="mt-1 max-w-md truncate text-xs text-slate-500 dark:text-slate-400">{{ $supplier->note ?: 'Tidak ada catatan' }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="font-semibold">{{ $supplier->contact_person ?: '-' }}</p>
                                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ collect([$supplier->phone, $supplier->email])->filter()->join(' • ') ?: 'Kontak belum diisi' }}</p>
                                </td>
                                <td class="px-5 py-4 max-w-xs truncate">{{ $supplier->address ?: '-' }}</td>
                                <td class="px-5 py-4">{{ number_format($supplier->products_count, 0, ',', '.') }} produk</td>
                                <td class="px-5 py-4"><x-admin.badge :tone="$supplier->is_active ? 'emerald' : 'slate'">{{ $supplier->is_active ? 'Aktif' : 'Nonaktif' }}</x-admin.badge></td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('suppliers.edit', $supplier) }}" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">Edit</a>
                                        <form method="POST" action="{{ route('suppliers.toggle', $supplier) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">{{ $supplier->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                                        </form>
                                        <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}" onsubmit="return confirm('Hapus supplier ini? Jika sudah punya produk, supplier akan dinonaktifkan.');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="rounded-lg bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 transition hover:bg-rose-100 dark:bg-rose-500/15 dark:text-rose-200 dark:hover:bg-rose-500/25">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-200 px-5 py-4 dark:border-slate-700">{{ $suppliers->links() }}</div>
        @else
            <div class="p-6">
                <x-admin.empty-state title="Belum ada supplier" description="Tambahkan supplier pertama agar produk bisa dikaitkan dengan pemasok." action-label="Tambah Supplier" :action-url="route('suppliers.create')" />
            </div>
        @endif
    </section>
</div>
@endsection
