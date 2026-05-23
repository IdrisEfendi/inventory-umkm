@extends('layouts.admin')

@section('title', 'Tambah Supplier')

@php
    $title = 'Tambah Supplier';
    $subtitle = 'Tambahkan data pemasok baru.';
@endphp

@section('content')
<div class="mx-auto max-w-3xl">
    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900">
        <form method="POST" action="{{ route('suppliers.store') }}">
            @include('suppliers._form', ['submitLabel' => 'Simpan Supplier'])
        </form>
    </section>
</div>
@endsection
