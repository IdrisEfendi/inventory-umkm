<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStockMovementRequest;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class StockMovementController extends Controller
{
    public function index(Request $request): View
    {
        $type = $request->query('type');
        $productId = $request->query('product_id');
        $search = trim((string) $request->query('search'));

        $movements = StockMovement::query()
            ->with(['product.category', 'creator'])
            ->when($type, fn ($query) => $query->where('type', $type))
            ->when($productId, fn ($query) => $query->where('product_id', $productId))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('note', 'like', "%{$search}%")
                        ->orWhereHas('product', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%")
                                ->orWhere('sku', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('stock-movements.index', [
            'movements' => $movements,
            'products' => Product::where('is_active', true)->orderBy('name')->get(),
            'type' => $type,
            'productId' => $productId,
            'search' => $search,
            'totalMovements' => StockMovement::count(),
            'totalStock' => Product::sum('stock'),
            'lowStockProductsCount' => Product::where('is_active', true)->whereColumn('stock', '<=', 'minimum_stock')->count(),
        ]);
    }

    public function store(StoreStockMovementRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $quantity = (int) $data['quantity'];
        $type = $data['type'];

        if ($type !== StockMovement::TYPE_ADJUSTMENT && $quantity < 1) {
            throw ValidationException::withMessages([
                'quantity' => 'Jumlah stok masuk/keluar harus lebih dari 0.',
            ]);
        }

        DB::transaction(function () use ($data, $quantity, $type) {
            $product = Product::whereKey($data['product_id'])->lockForUpdate()->firstOrFail();
            $stockBefore = (int) $product->stock;
            $stockAfter = match ($type) {
                StockMovement::TYPE_IN => $stockBefore + $quantity,
                StockMovement::TYPE_OUT => $stockBefore - $quantity,
                StockMovement::TYPE_ADJUSTMENT => $quantity,
            };

            if ($stockAfter < 0) {
                throw ValidationException::withMessages([
                    'quantity' => 'Stok keluar melebihi stok tersedia. Stok saat ini: '.number_format($stockBefore, 0, ',', '.'),
                ]);
            }

            $product->update([
                'stock' => $stockAfter,
            ]);

            StockMovement::create([
                'product_id' => $product->id,
                'type' => $type,
                'source_type' => 'manual',
                'source_id' => null,
                'quantity' => $quantity,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'note' => filled($data['note'] ?? null) ? trim((string) $data['note']) : null,
                'created_by' => auth()->id(),
            ]);
        });

        return redirect()->route('stock-movements.index')->with('success', 'Pergerakan stok berhasil disimpan.');
    }
}
