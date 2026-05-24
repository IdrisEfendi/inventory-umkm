<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class StoreSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_name',
        'logo_path',
        'phone',
        'email',
        'address',
        'receipt_footer',
    ];

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo_path ? Storage::disk('public')->url($this->logo_path) : null;
    }

    public static function current(): self
    {
        return self::query()->firstOrCreate([], [
            'store_name' => 'Inventory UMKM',
            'receipt_footer' => 'Terima kasih sudah berbelanja.',
        ]);
    }
}
