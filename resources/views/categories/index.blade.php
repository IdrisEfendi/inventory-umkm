@extends('layouts.admin')

@section('title', 'Kategori')

@php
    $title = 'Kategori';
    $subtitle = 'Kelola kelompok produk agar katalog inventory lebih rapi.';
@endphp

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-950">Daftar Kategori</h2>
            <p class="mt-1 text-sm text-slate-500">Gunakan kategori untuk mengelompokkan produk UMKM.</p>
        </div>
        <a href="{{ route('categories.create') }}" class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200">
            + Tambah Kategori
        </a>
    </div>

    @if (session('success'))
        <div role="status" class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <form method="GET" action="{{ route('categories.index') }}" class="grid gap-3 md:grid-cols-[1fr_180px_auto]">
            <input
                name="search"
                value="{{ $search }}"
                class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                placeholder="Cari nama, slug, atau deskripsi..."
            >
            <select name="status" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                <option value="">Semua Status</option>
                <option value="active" @selected($status === 'active')>Aktif</option>
                <option value="inactive" @selected($status === 'inactive')>Nonaktif</option>
            </select>
            <div class="flex gap-2">
                <button class="rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800 focus:outline-none focus:ring-4 focus:ring-slate-200">Filter</button>
                <a href="{{ route('categories.index') }}" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Reset</a>
            </div>
        </form>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        @if ($categories->count())
            <div class="overflow-x-auto">
                <table class="min-w-[860px] w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-4 font-bold">Kategori</th>
                            <th class="px-5 py-4 font-bold">Slug</th>
                            <th class="px-5 py-4 font-bold">Produk</th>
                            <th class="px-5 py-4 font-bold">Status</th>
                            <th class="px-5 py-4 text-right font-bold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($categories as $category)
                            <tr class="text-slate-700">
                                <td class="px-5 py-4">
                                    <p class="font-bold text-slate-950">{{ $category->name }}</p>
                                    <p class="mt-1 max-w-md truncate text-xs text-slate-500">{{ $category->description ?: 'Tidak ada deskripsi' }}</p>
                                </td>
                                <td class="px-5 py-4 font-mono text-xs text-slate-500">{{ $category->slug }}</td>
                                <td class="px-5 py-4">{{ number_format($category->products_count, 0, ',', '.') }} produk</td>
                                <td class="px-5 py-4">
                                    <x-admin.badge :tone="$category->is_active ? 'emerald' : 'slate'">
                                        {{ $category->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </x-admin.badge>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('categories.edit', $category) }}" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">Edit</a>
                                        <form method="POST" action="{{ route('categories.toggle', $category) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">
                                                {{ $category->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('categories.destroy', $category) }}" onsubmit="return confirm('Hapus kategori ini? Jika sudah punya produk, kategori akan dinonaktifkan.');">
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

            <div class="border-t border-slate-200 px-5 py-4">
                {{ $categories->links() }}
            </div>
        @else
            <div class="p-6">
                <x-admin.empty-state
                    title="Belum ada kategori"
                    description="Tambahkan kategori pertama agar produk bisa dikelompokkan dengan rapi."
                    action-label="Tambah Kategori"
                    :action-url="route('categories.create')"
                />
            </div>
        @endif
    </section>
</div>
@endsection
