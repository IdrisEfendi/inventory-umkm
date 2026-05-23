<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_name',
        'phone',
        'email',
        'address',
        'receipt_footer',
    ];

    public static function current(): self
    {
        return self::query()->firstOrCreate([], [
            'store_name' => 'Inventory UMKM',
            'receipt_footer' => 'Terima kasih sudah berbelanja.',
        ]);
    }
}
