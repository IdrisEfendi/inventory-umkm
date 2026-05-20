@extends('layouts.admin')

@section('title', 'Edit Produk')

@php
    $title = 'Edit Produk';
    $subtitle = 'Perbarui informasi produk, harga, dan stok.';
@endphp

@section('content')
<div class="mx-auto max-w-4xl">
    <div class="mb-6">
        <a href="{{ route('products.index') }}" class="text-sm font-semibold text-indigo-600 transition hover:text-indigo-700">← Kembali ke daftar produk</a>
    </div>

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-6">
            <h2 class="text-xl font-bold text-slate-950">Informasi Produk</h2>
            <p class="mt-1 text-sm text-slate-500">Perubahan stok manual sementara bisa dilakukan di sini. Nanti akan dipindah ke modul stock movement.</p>
        </div>

        <form method="POST" action="{{ route('products.update', $product) }}">
            @method('PUT')
            @include('products._form', ['submitLabel' => 'Update Produk'])
        </form>
    </section>
</div>
@endsection
