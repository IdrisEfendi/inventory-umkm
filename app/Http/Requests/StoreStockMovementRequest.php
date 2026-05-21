<?php

namespace App\Http\Requests;

use App\Models\StockMovement;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStockMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', Rule::exists('products', 'id')],
            'type' => ['required', Rule::in([
                StockMovement::TYPE_IN,
                StockMovement::TYPE_OUT,
                StockMovement::TYPE_ADJUSTMENT,
            ])],
            'quantity' => ['required', 'integer', 'min:0'],
            'note' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Produk wajib dipilih.',
            'product_id.exists' => 'Produk yang dipilih tidak ditemukan.',
            'type.required' => 'Jenis pergerakan stok wajib dipilih.',
            'type.in' => 'Jenis pergerakan stok tidak valid.',
            'quantity.required' => 'Jumlah wajib diisi.',
            'quantity.integer' => 'Jumlah harus berupa angka bulat.',
            'quantity.min' => 'Jumlah tidak boleh kurang dari 0.',
            'note.max' => 'Catatan maksimal 1000 karakter.',
        ];
    }
}
