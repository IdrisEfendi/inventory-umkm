<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sale_date' => ['required', 'date'],
            'customer_name' => ['nullable', 'string', 'max:150'],
            'payment_method' => ['nullable', 'string', 'max:50'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'paid_amount' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', Rule::exists('products', 'id')],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'sale_date.required' => 'Tanggal penjualan wajib diisi.',
            'sale_date.date' => 'Tanggal penjualan tidak valid.',
            'paid_amount.required' => 'Jumlah bayar wajib diisi.',
            'paid_amount.min' => 'Jumlah bayar tidak boleh kurang dari 0.',
            'items.required' => 'Minimal pilih 1 produk.',
            'items.min' => 'Minimal pilih 1 produk.',
            'items.*.product_id.required' => 'Produk wajib dipilih.',
            'items.*.product_id.exists' => 'Produk yang dipilih tidak ditemukan.',
            'items.*.quantity.required' => 'Qty wajib diisi.',
            'items.*.quantity.min' => 'Qty minimal 1.',
        ];
    }
}
