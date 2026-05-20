@csrf

<div class="grid gap-5 md:grid-cols-2">
    <div class="md:col-span-2">
        <label for="name" class="mb-2 block text-sm font-semibold text-slate-700">Nama Produk <span class="text-rose-500">*</span></label>
        <input id="name" name="name" value="{{ old('name', $product->name) }}" required class="block w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm outline-none transition hover:border-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 @error('name') border-rose-300 focus:border-rose-500 focus:ring-rose-100 @enderror" placeholder="Contoh: Beras Ramos 5kg">
        @error('name') <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="sku" class="mb-2 block text-sm font-semibold text-slate-700">SKU / Kode Barang</label>
        <input id="sku" name="sku" value="{{ old('sku', $product->sku) }}" class="block w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm outline-none transition hover:border-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 @error('sku') border-rose-300 focus:border-rose-500 focus:ring-rose-100 @enderror" placeholder="BRG-001">
        @error('sku') <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="category_id" class="mb-2 block text-sm font-semibold text-slate-700">Kategori</label>
        <select id="category_id" name="category_id" class="block w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm outline-none transition hover:border-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 @error('category_id') border-rose-300 focus:border-rose-500 focus:ring-rose-100 @enderror">
            <option value="">Tanpa kategori</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected((string) old('category_id', $product->category_id) === (string) $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        @error('category_id') <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="purchase_price" class="mb-2 block text-sm font-semibold text-slate-700">Harga Beli <span class="text-rose-500">*</span></label>
        <input id="purchase_price" name="purchase_price" type="number" min="0" step="0.01" value="{{ old('purchase_price', $product->purchase_price) }}" required class="block w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm outline-none transition hover:border-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 @error('purchase_price') border-rose-300 focus:border-rose-500 focus:ring-rose-100 @enderror" placeholder="0">
        @error('purchase_price') <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="selling_price" class="mb-2 block text-sm font-semibold text-slate-700">Harga Jual <span class="text-rose-500">*</span></label>
        <input id="selling_price" name="selling_price" type="number" min="0" step="0.01" value="{{ old('selling_price', $product->selling_price) }}" required class="block w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm outline-none transition hover:border-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 @error('selling_price') border-rose-300 focus:border-rose-500 focus:ring-rose-100 @enderror" placeholder="0">
        @error('selling_price') <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="stock" class="mb-2 block text-sm font-semibold text-slate-700">Stok <span class="text-rose-500">*</span></label>
        <input id="stock" name="stock" type="number" min="0" step="1" value="{{ old('stock', $product->stock) }}" required class="block w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm outline-none transition hover:border-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 @error('stock') border-rose-300 focus:border-rose-500 focus:ring-rose-100 @enderror" placeholder="0">
        @error('stock') <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="minimum_stock" class="mb-2 block text-sm font-semibold text-slate-700">Stok Minimum <span class="text-rose-500">*</span></label>
        <input id="minimum_stock" name="minimum_stock" type="number" min="0" step="1" value="{{ old('minimum_stock', $product->minimum_stock) }}" required class="block w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm outline-none transition hover:border-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 @error('minimum_stock') border-rose-300 focus:border-rose-500 focus:ring-rose-100 @enderror" placeholder="0">
        @error('minimum_stock') <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-2">
        <label for="description" class="mb-2 block text-sm font-semibold text-slate-700">Deskripsi</label>
        <textarea id="description" name="description" rows="4" class="block w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm outline-none transition hover:border-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 @error('description') border-rose-300 focus:border-rose-500 focus:ring-rose-100 @enderror" placeholder="Catatan produk, ukuran, varian, atau informasi tambahan">{{ old('description', $product->description) }}</textarea>
        @error('description') <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p> @enderror
    </div>

    <label class="md:col-span-2 flex items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active ?? true)) class="mt-1 h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
        <span>
            <span class="block text-sm font-semibold text-slate-800">Produk aktif</span>
            <span class="mt-1 block text-sm text-slate-500">Produk aktif bisa dipakai untuk stok dan transaksi penjualan.</span>
        </span>
    </label>
</div>

<div class="mt-8 flex flex-col-reverse gap-3 border-t border-slate-200 pt-6 sm:flex-row sm:justify-end">
    <a href="{{ route('products.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-100">Batal</a>
    <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200">{{ $submitLabel ?? 'Simpan' }}</button>
</div>
