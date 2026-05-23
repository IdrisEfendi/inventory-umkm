@extends('layouts.admin')

@section('title', 'Pembelian Baru')

@php
    $title = 'Pembelian Baru';
    $subtitle = 'Pilih supplier, item produk, lalu stok akan otomatis bertambah.';
@endphp

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-950 dark:text-white">Input Pembelian / Restock</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Harga modal produk akan diperbarui mengikuti input pembelian terbaru.</p>
        </div>
        <a href="{{ route('purchases.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">← Kembali</a>
    </div>

    @if ($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700 dark:border-rose-400/20 dark:bg-rose-500/15 dark:text-rose-200">
            <p>Periksa kembali pembelian:</p>
            <ul class="mt-2 list-disc space-y-1 pl-5 font-medium">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('purchases.store') }}" class="grid gap-6 xl:grid-cols-[1fr_380px]" id="purchase-form">
        @csrf

        <section class="space-y-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h3 class="text-lg font-bold text-slate-950 dark:text-white">Item Produk</h3>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Tambahkan produk yang direstock dari pembelian ini.</p>
                </div>
                <button type="button" id="add-row" class="rounded-xl bg-slate-950 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200">+ Item</button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-[820px] w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-800">
                        <tr>
                            <th class="px-4 py-3 font-bold">Produk</th>
                            <th class="px-4 py-3 text-right font-bold">Harga Modal</th>
                            <th class="px-4 py-3 text-right font-bold">Stok Saat Ini</th>
                            <th class="px-4 py-3 text-right font-bold">Qty Restock</th>
                            <th class="px-4 py-3 text-right font-bold">Subtotal</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody id="items-body" class="divide-y divide-slate-100 dark:divide-slate-800"></tbody>
                </table>
            </div>
        </section>

        <aside class="space-y-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900">
            <h3 class="text-lg font-bold text-slate-950 dark:text-white">Ringkasan Pembelian</h3>

            <div>
                <label for="supplier_id" class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-200">Supplier</label>
                <select id="supplier_id" name="supplier_id" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-indigo-500/20">
                    <option value="">Tanpa supplier</option>
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>{{ $supplier->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="purchase_date" class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-200">Tanggal</label>
                <input id="purchase_date" name="purchase_date" type="datetime-local" value="{{ old('purchase_date', now()->format('Y-m-d\TH:i')) }}" required class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-indigo-500/20">
            </div>

            <div>
                <label for="payment_status" class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-200">Status Pembayaran</label>
                <select id="payment_status" name="payment_status" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-indigo-500/20">
                    @foreach ($paymentStatuses as $value => $label)
                        <option value="{{ $value }}" @selected(old('payment_status', 'paid') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="rounded-2xl bg-slate-50 p-4 text-sm dark:bg-slate-800">
                <div class="flex justify-between py-1"><span class="text-slate-500 dark:text-slate-400">Subtotal</span><strong id="subtotal-label">Rp 0</strong></div>
                <div class="mt-3">
                    <label for="discount" class="mb-2 block font-bold text-slate-700 dark:text-slate-200">Diskon</label>
                    <input id="discount" name="discount" type="number" min="0" value="{{ old('discount', 0) }}" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-indigo-500/20">
                </div>
                <div class="mt-3 flex justify-between border-t border-slate-200 pt-3 text-base dark:border-slate-700"><span class="font-bold text-slate-700 dark:text-slate-200">Total</span><strong id="total-label" class="text-slate-950 dark:text-white">Rp 0</strong></div>
            </div>

            <div>
                <label for="note" class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-200">Catatan</label>
                <textarea id="note" name="note" rows="3" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-indigo-500/20" placeholder="Nomor invoice supplier, tempo, catatan restock...">{{ old('note') }}</textarea>
            </div>

            <button class="w-full rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200 dark:bg-indigo-500 dark:hover:bg-indigo-400 dark:focus:ring-indigo-500/30">Simpan Pembelian</button>
        </aside>
    </form>
</div>

<script>
    const products = @json($products);
    const oldItems = @json(old('items', [['product_id' => '', 'quantity' => 1, 'cost_price' => 0]]));
    const money = new Intl.NumberFormat('id-ID');
    const tbody = document.getElementById('items-body');
    const discount = document.getElementById('discount');

    function formatRupiah(value) { return 'Rp ' + money.format(Math.max(0, Number(value) || 0)); }
    function productOptions(selected = '') {
        return '<option value="">Pilih produk</option>' + products.map(product => {
            const label = `${product.name}${product.sku ? ' — ' + product.sku : ''}`;
            return `<option value="${product.id}" ${String(selected) === String(product.id) ? 'selected' : ''}>${label}</option>`;
        }).join('');
    }
    function rowTemplate(index, item = {}) {
        return `<tr data-row>
            <td class="px-4 py-3"><select name="items[${index}][product_id]" data-product required class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">${productOptions(item.product_id || '')}</select></td>
            <td class="px-4 py-3 text-right"><input name="items[${index}][cost_price]" data-cost type="number" min="0" step="0.01" value="${item.cost_price || 0}" required class="w-32 rounded-xl border border-slate-300 bg-white px-3 py-2 text-right text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100"></td>
            <td class="px-4 py-3 text-right" data-stock>0</td>
            <td class="px-4 py-3 text-right"><input name="items[${index}][quantity]" data-qty type="number" min="1" value="${item.quantity || 1}" required class="w-24 rounded-xl border border-slate-300 bg-white px-3 py-2 text-right text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100"></td>
            <td class="px-4 py-3 text-right font-bold text-slate-950 dark:text-white" data-line-total>Rp 0</td>
            <td class="px-4 py-3 text-right"><button type="button" data-remove class="rounded-lg bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 transition hover:bg-rose-100 dark:bg-rose-500/15 dark:text-rose-200">Hapus</button></td>
        </tr>`;
    }
    function reindexRows() {
        [...tbody.querySelectorAll('[data-row]')].forEach((row, index) => {
            row.querySelector('[data-product]').name = `items[${index}][product_id]`;
            row.querySelector('[data-cost]').name = `items[${index}][cost_price]`;
            row.querySelector('[data-qty]').name = `items[${index}][quantity]`;
        });
    }
    function addRow(item = {}) {
        tbody.insertAdjacentHTML('beforeend', rowTemplate(tbody.children.length, item));
        const row = tbody.lastElementChild;
        const product = products.find(product => String(product.id) === String(item.product_id || ''));
        if (product && (!item.cost_price || Number(item.cost_price) === 0)) row.querySelector('[data-cost]').value = Number(product.purchase_price) || 0;
        recalculate();
    }
    function recalculate() {
        let subtotal = 0;
        tbody.querySelectorAll('[data-row]').forEach(row => {
            const product = products.find(product => String(product.id) === String(row.querySelector('[data-product]').value));
            const qty = Number(row.querySelector('[data-qty]').value) || 0;
            const cost = Number(row.querySelector('[data-cost]').value) || 0;
            const stock = product ? Number(product.stock) : 0;
            const lineTotal = cost * qty;
            row.querySelector('[data-stock]').textContent = money.format(stock);
            row.querySelector('[data-line-total]').textContent = formatRupiah(lineTotal);
            subtotal += lineTotal;
        });
        const total = Math.max(0, subtotal - (Number(discount.value) || 0));
        document.getElementById('subtotal-label').textContent = formatRupiah(subtotal);
        document.getElementById('total-label').textContent = formatRupiah(total);
    }
    document.getElementById('add-row').addEventListener('click', () => addRow());
    tbody.addEventListener('input', recalculate);
    tbody.addEventListener('change', event => {
        if (event.target.matches('[data-product]')) {
            const row = event.target.closest('[data-row]');
            const product = products.find(product => String(product.id) === String(event.target.value));
            if (product) row.querySelector('[data-cost]').value = Number(product.purchase_price) || 0;
        }
        recalculate();
    });
    tbody.addEventListener('click', event => {
        if (event.target.matches('[data-remove]')) {
            event.target.closest('[data-row]').remove();
            if (!tbody.children.length) addRow();
            reindexRows();
            recalculate();
        }
    });
    discount.addEventListener('input', recalculate);
    oldItems.forEach(item => addRow(item));
</script>
@endsection
