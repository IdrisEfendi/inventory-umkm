<?php

namespace App\Http\Controllers;

use App\Models\StoreSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StoreSettingController extends Controller
{
    public function edit(): View
    {
        return view('settings.store', [
            'setting' => StoreSetting::current(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'store_name' => ['required', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:120'],
            'address' => ['nullable', 'string', 'max:500'],
            'receipt_footer' => ['nullable', 'string', 'max:160'],
        ], [
            'store_name.required' => 'Nama toko wajib diisi.',
            'store_name.max' => 'Nama toko maksimal 120 karakter.',
            'email.email' => 'Format email toko tidak valid.',
            'address.max' => 'Alamat maksimal 500 karakter.',
            'receipt_footer.max' => 'Catatan footer nota maksimal 160 karakter.',
        ]);

        StoreSetting::current()->update($data);

        return back()->with('success', 'Pengaturan toko berhasil disimpan.');
    }
}
