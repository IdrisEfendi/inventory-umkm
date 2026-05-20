@csrf

<div class="grid gap-5">
    <div>
        <label for="name" class="mb-2 block text-sm font-semibold text-slate-700">Nama Kategori <span class="text-rose-500">*</span></label>
        <input
            id="name"
            name="name"
            value="{{ old('name', $category->name) }}"
            required
            class="block w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm outline-none transition placeholder:text-slate-400 hover:border-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 @error('name') border-rose-300 focus:border-rose-500 focus:ring-rose-100 @enderror"
            placeholder="Contoh: Makanan Pokok"
        >
        @error('name')
            <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="slug" class="mb-2 block text-sm font-semibold text-slate-700">Slug</label>
        <input
            id="slug"
            name="slug"
            value="{{ old('slug', $category->slug) }}"
            class="block w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm outline-none transition placeholder:text-slate-400 hover:border-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 @error('slug') border-rose-300 focus:border-rose-500 focus:ring-rose-100 @enderror"
            placeholder="Kosongkan untuk generate otomatis"
        >
        <p class="mt-2 text-xs text-slate-500">Slug dipakai untuk identitas URL/data. Jika kosong, akan dibuat otomatis dari nama.</p>
        @error('slug')
            <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="description" class="mb-2 block text-sm font-semibold text-slate-700">Deskripsi</label>
        <textarea
            id="description"
            name="description"
            rows="4"
            class="block w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm outline-none transition placeholder:text-slate-400 hover:border-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 @error('description') border-rose-300 focus:border-rose-500 focus:ring-rose-100 @enderror"
            placeholder="Catatan singkat tentang kategori ini"
        >{{ old('description', $category->description) }}</textarea>
        @error('description')
            <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <label class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->is_active ?? true)) class="mt-1 h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
        <span>
            <span class="block text-sm font-semibold text-slate-800">Kategori aktif</span>
            <span class="mt-1 block text-sm text-slate-500">Kategori aktif bisa dipakai saat membuat produk.</span>
        </span>
    </label>
</div>

<div class="mt-8 flex flex-col-reverse gap-3 border-t border-slate-200 pt-6 sm:flex-row sm:justify-end">
    <a href="{{ route('categories.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-100">
        Batal
    </a>
    <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200">
        {{ $submitLabel ?? 'Simpan' }}
    </button>
</div>
