@extends('layouts.admin')

@section('title', 'Edit Supplier')

@php
    $title = 'Edit Supplier';
    $subtitle = $supplier->name;
@endphp

@section('content')
<div class="mx-auto max-w-3xl">
    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900">
        <form method="POST" action="{{ route('suppliers.update', $supplier) }}">
            @method('PUT')
            @include('suppliers._form', ['submitLabel' => 'Simpan Perubahan'])
        </form>
    </section>
</div>
@endsection
