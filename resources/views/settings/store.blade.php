@extends('layouts.admin')

@section('title', 'Pengaturan Toko')

@php
    $title = 'Pengaturan Toko';
    $subtitle = 'Atur identitas toko yang tampil di nota penjualan.';
@endphp

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-950 dark:text-white">Identitas Toko</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Data ini akan dipakai pada cetak nota/invoice penjualan.</p>
        </div>
        <a href="{{ route('sales.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">Lihat Penjualan</a>
    </div>

    @if (session('success'))
        <div role="status" class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700 dark:border-emerald-400/20 dark:bg-emerald-500/15 dark:text-emerald-200">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700 dark:border-rose-400/20 dark:bg-rose-500/15 dark:text-rose-200">
            <p>Periksa kembali input:</p>
            <ul class="mt-2 list-disc space-y-1 pl-5 font-medium">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-[1fr_360px]">
        <form method="POST" action="{{ route('settings.store.update') }}" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900">
            @csrf
            @method('PUT')

            <div class="grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="store_name" class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-200">Nama Toko <span class="text-rose-500">*</span></label>
                    <input id="store_name" name="store_name" value="{{ old('store_name', $setting->store_name) }}" required class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-indigo-500/20" placeholder="Contoh: Toko Berkah UMKM">
                </div>

                <div>
                    <label for="phone" class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-200">No. Telepon/WA</label>
                    <input id="phone" name="phone" value="{{ old('phone', $setting->phone) }}" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-indigo-500/20" placeholder="08xxxxxxxxxx">
                </div>

                <div>
                    <label for="email" class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-200">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $setting->email) }}" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-indigo-500/20" placeholder="toko@email.com">
                </div>

                <div class="sm:col-span-2">
                    <label for="address" class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-200">Alamat</label>
                    <textarea id="address" name="address" rows="4" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-indigo-500/20" placeholder="Alamat toko">{{ old('address', $setting->address) }}</textarea>
                </div>

                <div class="sm:col-span-2">
                    <label for="receipt_footer" class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-200">Catatan Footer Nota</label>
                    <input id="receipt_footer" name="receipt_footer" value="{{ old('receipt_footer', $setting->receipt_footer) }}" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-indigo-500/20" placeholder="Terima kasih sudah berbelanja.">
                    <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Tampil di bagian bawah nota.</p>
                </div>
            </div>

            <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">Batal</a>
                <button class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200 dark:bg-indigo-500 dark:hover:bg-indigo-400 dark:focus:ring-indigo-500/30">Simpan Pengaturan</button>
            </div>
        </form>

        <aside class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900">
            <h3 class="text-lg font-bold text-slate-950 dark:text-white">Preview Header Nota</h3>
            <div class="mt-5 rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-5 text-center dark:border-slate-700 dark:bg-slate-800/60">
                <p class="text-lg font-black text-slate-950 dark:text-white">{{ $setting->store_name }}</p>
                @if ($setting->address)
                    <p class="mt-2 text-xs leading-5 text-slate-500 dark:text-slate-400">{{ $setting->address }}</p>
                @endif
                <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                    {{ collect([$setting->phone, $setting->email])->filter()->join(' • ') ?: 'Kontak toko belum diisi' }}
                </p>
                <div class="my-4 border-t border-dashed border-slate-300 dark:border-slate-700"></div>
                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $setting->receipt_footer ?: 'Footer nota belum diisi' }}</p>
            </div>
        </aside>
    </div>
</div>
@endsection
