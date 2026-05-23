<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PurchaseController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $supplierId = $request->query('supplier_id');
        $status = $request->query('payment_status');
        $from = $request->query('from');
        $to = $request->query('to');

        $purchases = Purchase::query()
            ->with(['supplier', 'creator'])
            ->withCount('items')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('purchase_number', 'like', "%{$search}%")
                        ->orWhere('note', 'like', "%{$search}%")
                        ->orWhereHas('supplier', fn ($query) => $query->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($supplierId, fn ($query) => $query->where('supplier_id', $supplierId))
            ->when($status, fn ($query) => $query->where('payment_status', $status))
            ->when($from, fn ($query) => $query->whereDate('purchase_date', '>=', $from))
            ->when($to, fn ($query) => $query->whereDate('purchase_date', '<=', $to))
            ->latest('purchase_date')
            ->paginate(12)
            ->withQueryString();

        return view('purchases.index', [
            'purchases' => $purchases,
            'suppliers' => Supplier::where('is_active', true)->orderBy('name')->get(),
            'search' => $search,
            'supplierId' => $supplierId,
            'status' => $status,
            'from' => $from,
            'to' => $to,
            'todayPurchasesCount' => Purchase::whereDate('purchase_date', Carbon::today())->count(),
            'todayPurchasesTotal' => Purchase::whereDate('purchase_date', Carbon::today())->sum('total'),
            'monthPurchasesTotal' => Purchase::whereMonth('purchase_date', Carbon::today()->month)
                ->whereYear('purchase_date', Carbon::today()->year)
                ->sum('total'),
        ]);
    }

    public function create(): View
    {
        return view('purchases.create', [
            'suppliers' => Supplier::where('is_active', true)->orderBy('name')->get(),
            'products' => Product::where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'supplier_id', 'sku', 'name', 'purchase_price', 'stock']),
            'paymentStatuses' => $this->paymentStatuses(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'purchase_date' => ['required', 'date'],
            'payment_status' => ['required', Rule::in(array_keys($this->paymentStatuses()))],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'note' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.cost_price' => ['required', 'numeric', 'min:0'],
        ], [
            'purchase_date.required' => 'Tanggal pembelian wajib diisi.',
            'payment_status.required' => 'Status pembayaran wajib dipilih.',
            'items.required' => 'Minimal pilih 1 produk.',
            'items.*.product_id.required' => 'Produk wajib dipilih.',
            'items.*.quantity.min' => 'Jumlah restock minimal 1.',
            'items.*.cost_price.required' => 'Harga modal wajib diisi.',
        ]);

        $items = collect($data['items'])
            ->map(fn ($item) => [
                'product_id' => (int) $item['product_id'],
                'quantity' => (int) $item['quantity'],
                'cost_price' => (float) $item['cost_price'],
            ])
            ->groupBy('product_id')
            ->map(fn ($rows, $productId) => [
                'product_id' => (int) $productId,
                'quantity' => $rows->sum('quantity'),
                'cost_price' => (float) $rows->last()['cost_price'],
            ])
            ->values();

        if ($items->isEmpty()) {
            throw ValidationException::withMessages(['items' => 'Minimal pilih 1 produk.']);
        }

        $purchase = DB::transaction(function () use ($data, $items) {
            $products = Product::whereIn('id', $items->pluck('product_id'))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $subtotal = 0;
            $purchaseItems = [];

            foreach ($items as $item) {
                $product = $products->get($item['product_id']);

                if (! $product || ! $product->is_active) {
                    throw ValidationException::withMessages(['items' => 'Ada produk yang tidak aktif atau tidak ditemukan.']);
                }

                $lineSubtotal = $item['cost_price'] * $item['quantity'];
                $subtotal += $lineSubtotal;

                $purchaseItems[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'cost_price' => $item['cost_price'],
                    'subtotal' => $lineSubtotal,
                ];
            }

            $discount = (float) ($data['discount'] ?? 0);

            if ($discount > $subtotal) {
                throw ValidationException::withMessages(['discount' => 'Diskon tidak boleh lebih besar dari subtotal.']);
            }

            $purchase = Purchase::create([
                'purchase_number' => $this->generatePurchaseNumber(),
                'supplier_id' => $data['supplier_id'] ?: null,
                'purchase_date' => $data['purchase_date'],
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total' => $subtotal - $discount,
                'payment_status' => $data['payment_status'],
                'note' => filled($data['note'] ?? null) ? trim((string) $data['note']) : null,
                'created_by' => auth()->id(),
            ]);

            foreach ($purchaseItems as $item) {
                $product = $item['product'];
                $stockBefore = (int) $product->stock;
                $stockAfter = $stockBefore + $item['quantity'];

                $purchase->items()->create([
                    'product_id' => $product->id,
                    'product_name_snapshot' => $product->name,
                    'product_sku_snapshot' => $product->sku,
                    'cost_price' => $item['cost_price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['subtotal'],
                ]);

                $product->update([
                    'stock' => $stockAfter,
                    'purchase_price' => $item['cost_price'],
                    'supplier_id' => $data['supplier_id'] ?: $product->supplier_id,
                ]);

                StockMovement::create([
                    'product_id' => $product->id,
                    'type' => StockMovement::TYPE_IN,
                    'source_type' => 'purchase',
                    'source_id' => $purchase->id,
                    'quantity' => $item['quantity'],
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'note' => 'Pembelian '.$purchase->purchase_number,
                    'created_by' => auth()->id(),
                ]);
            }

            return $purchase;
        });

        return redirect()->route('purchases.show', $purchase)->with('success', 'Pembelian berhasil disimpan dan stok otomatis bertambah.');
    }

    public function show(Purchase $purchase): View
    {
        return view('purchases.show', [
            'purchase' => $purchase->load(['supplier', 'items.product', 'creator']),
            'paymentStatuses' => $this->paymentStatuses(),
        ]);
    }

    private function generatePurchaseNumber(): string
    {
        $prefix = 'PO-'.now()->format('Ymd').'-';
        $number = Purchase::whereDate('created_at', now()->toDateString())->count() + 1;

        do {
            $purchaseNumber = $prefix.str_pad((string) $number, 4, '0', STR_PAD_LEFT);
            $number++;
        } while (Purchase::where('purchase_number', $purchaseNumber)->exists());

        return $purchaseNumber;
    }

    private function paymentStatuses(): array
    {
        return [
            Purchase::STATUS_PAID => 'Lunas',
            Purchase::STATUS_PARTIAL => 'Sebagian',
            Purchase::STATUS_UNPAID => 'Belum Lunas',
        ];
    }
}
