@extends('layouts.admin')

@section('title', 'Transaksi Penjualan')

@php
    $title = 'Transaksi Penjualan';
    $subtitle = 'Pilih produk, hitung total, dan kurangi stok otomatis.';
@endphp

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-950">Buat Transaksi</h2>
            <p class="mt-1 text-sm text-slate-500">Harga mengikuti harga jual produk saat transaksi dibuat.</p>
        </div>
        <a href="{{ route('sales.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">← Kembali</a>
    </div>

    @if ($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700">
            <p>Periksa kembali transaksi:</p>
            <ul class="mt-2 list-disc space-y-1 pl-5 font-medium">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('sales.store') }}" class="grid gap-6 xl:grid-cols-[1fr_360px]" id="sale-form">
        @csrf

        <section class="space-y-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h3 class="text-lg font-bold text-slate-950">Item Produk</h3>
                    <p class="mt-1 text-sm text-slate-500">Tambahkan satu atau beberapa produk ke transaksi.</p>
                </div>
                <button type="button" id="add-row" class="rounded-xl bg-slate-950 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">+ Item</button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-[760px] w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-3 font-bold">Produk</th>
                            <th class="px-4 py-3 text-right font-bold">Harga</th>
                            <th class="px-4 py-3 text-right font-bold">Stok</th>
                            <th class="px-4 py-3 text-right font-bold">Qty</th>
                            <th class="px-4 py-3 text-right font-bold">Subtotal</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody id="items-body" class="divide-y divide-slate-100"></tbody>
                </table>
            </div>
        </section>

        <aside class="space-y-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="text-lg font-bold text-slate-950">Ringkasan</h3>

            <div>
                <label for="sale_date" class="mb-2 block text-sm font-bold text-slate-700">Tanggal</label>
                <input id="sale_date" name="sale_date" type="datetime-local" value="{{ old('sale_date', now()->format('Y-m-d\\TH:i')) }}" required class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
            </div>

            <div>
                <label for="customer_name" class="mb-2 block text-sm font-bold text-slate-700">Pelanggan</label>
                <input id="customer_name" name="customer_name" value="{{ old('customer_name') }}" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100" placeholder="Umum">
            </div>

            <div>
                <label for="payment_method" class="mb-2 block text-sm font-bold text-slate-700">Metode Bayar</label>
                <select id="payment_method" name="payment_method" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                    @foreach (['Tunai', 'Transfer', 'QRIS', 'Debit'] as $method)
                        <option value="{{ $method }}" @selected(old('payment_method', 'Tunai') === $method)>{{ $method }}</option>
                    @endforeach
                </select>
            </div>

            <div class="rounded-2xl bg-slate-50 p-4 text-sm">
                <div class="flex justify-between py-1"><span class="text-slate-500">Subtotal</span><strong id="subtotal-label">Rp 0</strong></div>
                <div class="mt-3">
                    <label for="discount" class="mb-2 block font-bold text-slate-700">Diskon</label>
                    <input id="discount" name="discount" type="number" min="0" value="{{ old('discount', 0) }}" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                </div>
                <div class="mt-3 flex justify-between border-t border-slate-200 pt-3 text-base"><span class="font-bold text-slate-700">Total</span><strong id="total-label" class="text-slate-950">Rp 0</strong></div>
            </div>

            <div>
                <label for="paid_amount" class="mb-2 block text-sm font-bold text-slate-700">Jumlah Bayar</label>
                <input id="paid_amount" name="paid_amount" type="number" min="0" value="{{ old('paid_amount', 0) }}" required class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                <p class="mt-2 text-xs text-slate-500">Kembalian: <strong id="change-label">Rp 0</strong></p>
            </div>

            <div>
                <label for="note" class="mb-2 block text-sm font-bold text-slate-700">Catatan</label>
                <textarea id="note" name="note" rows="3" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100" placeholder="Opsional">{{ old('note') }}</textarea>
            </div>

            <button class="w-full rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200">Simpan Penjualan</button>
        </aside>
    </form>
</div>

<script>
    const products = @json($products);
    const oldItems = @json(old('items', [['product_id' => '', 'quantity' => 1]]));
    const money = new Intl.NumberFormat('id-ID');
    const tbody = document.getElementById('items-body');
    const discount = document.getElementById('discount');
    const paidAmount = document.getElementById('paid_amount');

    function formatRupiah(value) {
        return 'Rp ' + money.format(Math.max(0, Number(value) || 0));
    }

    function productOptions(selected = '') {
        return '<option value="">Pilih produk</option>' + products.map(product => {
            const label = `${product.name}${product.sku ? ' — ' + product.sku : ''}`;
            return `<option value="${product.id}" ${String(selected) === String(product.id) ? 'selected' : ''}>${label}</option>`;
        }).join('');
    }

    function rowTemplate(index, item = {}) {
        return `
            <tr data-row>
                <td class="px-4 py-3">
                    <select name="items[${index}][product_id]" data-product required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                        ${productOptions(item.product_id || '')}
                    </select>
                </td>
                <td class="px-4 py-3 text-right font-semibold" data-price>Rp 0</td>
                <td class="px-4 py-3 text-right" data-stock>0</td>
                <td class="px-4 py-3 text-right">
                    <input name="items[${index}][quantity]" data-qty type="number" min="1" value="${item.quantity || 1}" required class="w-24 rounded-xl border border-slate-300 px-3 py-2 text-right text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                </td>
                <td class="px-4 py-3 text-right font-bold text-slate-950" data-line-total>Rp 0</td>
                <td class="px-4 py-3 text-right"><button type="button" data-remove class="rounded-lg bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 transition hover:bg-rose-100">Hapus</button></td>
            </tr>
        `;
    }

    function reindexRows() {
        [...tbody.querySelectorAll('[data-row]')].forEach((row, index) => {
            row.querySelector('[data-product]').name = `items[${index}][product_id]`;
            row.querySelector('[data-qty]').name = `items[${index}][quantity]`;
        });
    }

    function addRow(item = {}) {
        tbody.insertAdjacentHTML('beforeend', rowTemplate(tbody.children.length, item));
        recalculate();
    }

    function recalculate() {
        let subtotal = 0;

        tbody.querySelectorAll('[data-row]').forEach(row => {
            const product = products.find(product => String(product.id) === String(row.querySelector('[data-product]').value));
            const qty = Number(row.querySelector('[data-qty]').value) || 0;
            const price = product ? Number(product.selling_price) : 0;
            const stock = product ? Number(product.stock) : 0;
            const lineTotal = price * qty;

            row.querySelector('[data-price]').textContent = formatRupiah(price);
            row.querySelector('[data-stock]').textContent = money.format(stock);
            row.querySelector('[data-line-total]').textContent = formatRupiah(lineTotal);
            subtotal += lineTotal;
        });

        const discountValue = Number(discount.value) || 0;
        const total = Math.max(0, subtotal - discountValue);
        const paid = Number(paidAmount.value) || 0;

        document.getElementById('subtotal-label').textContent = formatRupiah(subtotal);
        document.getElementById('total-label').textContent = formatRupiah(total);
        document.getElementById('change-label').textContent = formatRupiah(paid - total);
    }

    document.getElementById('add-row').addEventListener('click', () => addRow());
    tbody.addEventListener('input', recalculate);
    tbody.addEventListener('change', recalculate);
    tbody.addEventListener('click', event => {
        if (event.target.matches('[data-remove]')) {
            event.target.closest('[data-row]').remove();
            if (!tbody.children.length) addRow();
            reindexRows();
            recalculate();
        }
    });
    discount.addEventListener('input', recalculate);
    paidAmount.addEventListener('input', recalculate);

    oldItems.forEach(item => addRow(item));
</script>
@endsection
