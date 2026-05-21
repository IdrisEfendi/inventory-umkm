<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSaleRequest;
use App\Models\Product;
use App\Models\Sale;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $from = $request->query('from');
        $to = $request->query('to');

        $sales = Sale::query()
            ->withCount('items')
            ->with('creator')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('invoice_number', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhere('note', 'like', "%{$search}%");
                });
            })
            ->when($from, fn ($query) => $query->whereDate('sale_date', '>=', $from))
            ->when($to, fn ($query) => $query->whereDate('sale_date', '<=', $to))
            ->latest('sale_date')
            ->paginate(12)
            ->withQueryString();

        return view('sales.index', [
            'sales' => $sales,
            'search' => $search,
            'from' => $from,
            'to' => $to,
            'todayRevenue' => Sale::whereDate('sale_date', Carbon::today())->sum('total'),
            'todaySalesCount' => Sale::whereDate('sale_date', Carbon::today())->count(),
            'monthRevenue' => Sale::whereMonth('sale_date', Carbon::today()->month)
                ->whereYear('sale_date', Carbon::today()->year)
                ->sum('total'),
        ]);
    }

    public function create(): View
    {
        return view('sales.create', [
            'products' => Product::where('is_active', true)
                ->where('stock', '>', 0)
                ->orderBy('name')
                ->get(['id', 'sku', 'name', 'selling_price', 'stock']),
        ]);
    }

    public function store(StoreSaleRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $items = collect($data['items'])
            ->map(fn ($item) => [
                'product_id' => (int) $item['product_id'],
                'quantity' => (int) $item['quantity'],
            ])
            ->groupBy('product_id')
            ->map(fn ($rows, $productId) => [
                'product_id' => (int) $productId,
                'quantity' => $rows->sum('quantity'),
            ])
            ->values();

        if ($items->isEmpty()) {
            throw ValidationException::withMessages(['items' => 'Minimal pilih 1 produk.']);
        }

        $sale = DB::transaction(function () use ($data, $items) {
            $products = Product::whereIn('id', $items->pluck('product_id'))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $subtotal = 0;
            $saleItems = [];

            foreach ($items as $item) {
                $product = $products->get($item['product_id']);

                if (! $product || ! $product->is_active) {
                    throw ValidationException::withMessages(['items' => 'Ada produk yang tidak aktif atau tidak ditemukan.']);
                }

                if ($product->stock < $item['quantity']) {
                    throw ValidationException::withMessages([
                        'items' => "Stok {$product->name} tidak cukup. Tersedia {$product->stock}, diminta {$item['quantity']}.",
                    ]);
                }

                $price = (float) $product->selling_price;
                $lineSubtotal = $price * $item['quantity'];
                $subtotal += $lineSubtotal;

                $saleItems[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'price' => $price,
                    'subtotal' => $lineSubtotal,
                ];
            }

            $discount = (float) ($data['discount'] ?? 0);

            if ($discount > $subtotal) {
                throw ValidationException::withMessages(['discount' => 'Diskon tidak boleh lebih besar dari subtotal.']);
            }

            $total = $subtotal - $discount;
            $paidAmount = (float) $data['paid_amount'];

            if ($paidAmount < $total) {
                throw ValidationException::withMessages(['paid_amount' => 'Jumlah bayar kurang dari total penjualan.']);
            }

            $sale = Sale::create([
                'invoice_number' => $this->generateInvoiceNumber(),
                'sale_date' => $data['sale_date'],
                'customer_name' => filled($data['customer_name'] ?? null) ? trim((string) $data['customer_name']) : null,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total' => $total,
                'paid_amount' => $paidAmount,
                'change_amount' => $paidAmount - $total,
                'payment_method' => filled($data['payment_method'] ?? null) ? $data['payment_method'] : null,
                'note' => filled($data['note'] ?? null) ? trim((string) $data['note']) : null,
                'created_by' => auth()->id(),
            ]);

            foreach ($saleItems as $item) {
                $product = $item['product'];
                $stockBefore = (int) $product->stock;
                $stockAfter = $stockBefore - $item['quantity'];

                $sale->items()->create([
                    'product_id' => $product->id,
                    'product_name_snapshot' => $product->name,
                    'product_sku_snapshot' => $product->sku,
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['subtotal'],
                ]);

                $product->update(['stock' => $stockAfter]);

                StockMovement::create([
                    'product_id' => $product->id,
                    'type' => StockMovement::TYPE_OUT,
                    'source_type' => 'sale',
                    'source_id' => $sale->id,
                    'quantity' => $item['quantity'],
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'note' => 'Penjualan '.$sale->invoice_number,
                    'created_by' => auth()->id(),
                ]);
            }

            return $sale;
        });

        return redirect()->route('sales.show', $sale)->with('success', 'Penjualan berhasil disimpan dan stok otomatis berkurang.');
    }

    public function show(Sale $sale): View
    {
        return view('sales.show', [
            'sale' => $sale->load(['items.product', 'creator']),
        ]);
    }

    private function generateInvoiceNumber(): string
    {
        $prefix = 'INV-'.now()->format('Ymd').'-';
        $number = Sale::whereDate('created_at', now()->toDateString())->count() + 1;

        do {
            $invoice = $prefix.str_pad((string) $number, 4, '0', STR_PAD_LEFT);
            $number++;
        } while (Sale::where('invoice_number', $invoice)->exists());

        return $invoice;
    }
}
