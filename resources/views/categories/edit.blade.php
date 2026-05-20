@extends('layouts.admin')

@section('title', 'Edit Kategori')

@php
    $title = 'Edit Kategori';
    $subtitle = 'Perbarui informasi kategori produk.';
@endphp

@section('content')
<div class="mx-auto max-w-3xl">
    <div class="mb-6">
        <a href="{{ route('categories.index') }}" class="text-sm font-semibold text-indigo-600 transition hover:text-indigo-700">← Kembali ke daftar kategori</a>
    </div>

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-6">
            <h2 class="text-xl font-bold text-slate-950">Informasi Kategori</h2>
            <p class="mt-1 text-sm text-slate-500">Perubahan kategori akan memengaruhi pengelompokan produk terkait.</p>
        </div>

        <form method="POST" action="{{ route('categories.update', $category) }}">
            @method('PUT')
            @include('categories._form', ['submitLabel' => 'Update Kategori'])
        </form>
    </section>
</div>
@endsection
