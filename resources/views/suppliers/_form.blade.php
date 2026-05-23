@csrf

<div class="grid gap-5 md:grid-cols-2">
    <div class="md:col-span-2">
        <label for="name" class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">Nama Supplier <span class="text-rose-500">*</span></label>
        <input id="name" name="name" value="{{ old('name', $supplier->name) }}" required class="block w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm outline-none transition hover:border-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-indigo-500/20 @error('name') border-rose-300 focus:border-rose-500 focus:ring-rose-100 @enderror" placeholder="Contoh: CV Sumber Makmur">
        @error('name') <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="contact_person" class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">Nama Kontak</label>
        <input id="contact_person" name="contact_person" value="{{ old('contact_person', $supplier->contact_person) }}" class="block w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm outline-none transition hover:border-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-indigo-500/20" placeholder="Nama PIC/sales">
    </div>

    <div>
        <label for="phone" class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">Telepon / WA</label>
        <input id="phone" name="phone" value="{{ old('phone', $supplier->phone) }}" class="block w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm outline-none transition hover:border-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-indigo-500/20" placeholder="08xxxxxxxxxx">
    </div>

    <div>
        <label for="email" class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email', $supplier->email) }}" class="block w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm outline-none transition hover:border-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-indigo-500/20" placeholder="supplier@email.com">
        @error('email') <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">Status</label>
        <label class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-800/60">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $supplier->is_active ?? true)) class="mt-1 h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
            <span>
                <span class="block text-sm font-semibold text-slate-800 dark:text-slate-100">Supplier aktif</span>
                <span class="mt-1 block text-sm text-slate-500 dark:text-slate-400">Supplier aktif bisa dipilih saat membuat produk.</span>
            </span>
        </label>
    </div>

    <div class="md:col-span-2">
        <label for="address" class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">Alamat</label>
        <textarea id="address" name="address" rows="3" class="block w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm outline-none transition hover:border-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-indigo-500/20" placeholder="Alamat supplier">{{ old('address', $supplier->address) }}</textarea>
        @error('address') <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-2">
        <label for="note" class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">Catatan</label>
        <textarea id="note" name="note" rows="4" class="block w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm outline-none transition hover:border-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-indigo-500/20" placeholder="Catatan pembayaran, jadwal kirim, jenis barang, dll.">{{ old('note', $supplier->note) }}</textarea>
        @error('note') <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p> @enderror
    </div>
</div>

<div class="mt-8 flex flex-col-reverse gap-3 border-t border-slate-200 pt-6 dark:border-slate-700 sm:flex-row sm:justify-end">
    <a href="{{ route('suppliers.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-100 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800 dark:focus:ring-slate-700">Batal</a>
    <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200 dark:bg-indigo-500 dark:hover:bg-indigo-400 dark:focus:ring-indigo-500/30">{{ $submitLabel ?? 'Simpan' }}</button>
</div>
