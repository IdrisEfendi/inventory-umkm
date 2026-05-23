<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $categoryId = $request->query('category_id');
        $supplierId = $request->query('supplier_id');
        $status = $request->query('status');
        $stock = $request->query('stock');

        $products = Product::query()
            ->with(['category', 'supplier'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($categoryId, fn ($query) => $query->where('category_id', $categoryId))
            ->when($supplierId, fn ($query) => $query->where('supplier_id', $supplierId))
            ->when($status === 'active', fn ($query) => $query->where('is_active', true))
            ->when($status === 'inactive', fn ($query) => $query->where('is_active', false))
            ->when($stock === 'low', fn ($query) => $query->whereColumn('stock', '<=', 'minimum_stock'))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('products.index', [
            'products' => $products,
            'categories' => Category::where('is_active', true)->orderBy('name')->get(),
            'suppliers' => Supplier::where('is_active', true)->orderBy('name')->get(),
            'search' => $search,
            'categoryId' => $categoryId,
            'supplierId' => $supplierId,
            'status' => $status,
            'stock' => $stock,
        ]);
    }

    public function create(): View
    {
        return view('products.create', [
            'product' => new Product([
                'is_active' => true,
                'stock' => 0,
                'minimum_stock' => 0,
                'purchase_price' => 0,
                'selling_price' => 0,
            ]),
            'categories' => Category::where('is_active', true)->orderBy('name')->get(),
            'suppliers' => Supplier::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        Product::create($this->validatedData($request->validated()));

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product): View
    {
        return view('products.edit', [
            'product' => $product,
            'categories' => Category::where('is_active', true)->orderBy('name')->get(),
            'suppliers' => Supplier::where('is_active', true)
                ->orWhere('id', $product->supplier_id)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $product->update($this->validatedData($request->validated()));

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->saleItems()->exists() || $product->stockMovements()->exists()) {
            $product->update(['is_active' => false]);

            return redirect()
                ->route('products.index')
                ->with('success', 'Produk memiliki riwayat transaksi/stok, jadi produk dinonaktifkan agar data tetap aman.');
        }

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
    }

    public function toggle(Product $product): RedirectResponse
    {
        $product->update([
            'is_active' => ! $product->is_active,
        ]);

        return redirect()->route('products.index')->with('success', 'Status produk berhasil diperbarui.');
    }

    private function validatedData(array $data): array
    {
        return [
            'category_id' => $data['category_id'] ?: null,
            'supplier_id' => $data['supplier_id'] ?: null,
            'sku' => filled($data['sku'] ?? null) ? trim((string) $data['sku']) : null,
            'name' => trim((string) $data['name']),
            'description' => $data['description'] ?? null,
            'purchase_price' => $data['purchase_price'],
            'selling_price' => $data['selling_price'],
            'stock' => $data['stock'],
            'minimum_stock' => $data['minimum_stock'],
            'is_active' => (bool) ($data['is_active'] ?? false),
        ];
    }
}
