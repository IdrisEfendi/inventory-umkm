<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        return view('reports.index', $this->reportData($request));
    }

    public function print(Request $request): View
    {
        return view('reports.print', $this->reportData($request));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $data = $this->reportData($request);
        $filename = sprintf('laporan-inventory-%s-sampai-%s.csv', $data['from'], $data['to']);

        return response()->streamDownload(function () use ($data) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['Laporan Inventory UMKM']);
            fputcsv($handle, ['Periode', $data['from'], $data['to']]);
            fputcsv($handle, []);

            fputcsv($handle, ['Ringkasan']);
            fputcsv($handle, ['Omzet', $data['revenue']]);
            fputcsv($handle, ['Subtotal', $data['subtotal']]);
            fputcsv($handle, ['Diskon', $data['discount']]);
            fputcsv($handle, ['Transaksi', $data['transactions']]);
            fputcsv($handle, ['Rata-rata Transaksi', $data['averageTransaction']]);
            fputcsv($handle, ['Item Terjual', $data['soldQuantity']]);
            fputcsv($handle, ['Stok Masuk', $data['stockIn']]);
            fputcsv($handle, ['Stok Keluar', $data['stockOut']]);
            fputcsv($handle, ['Adjustment Stok', $data['stockAdjustments']]);
            fputcsv($handle, ['Nilai Inventory', $data['inventoryValue']]);
            fputcsv($handle, []);

            fputcsv($handle, ['Produk Terlaris']);
            fputcsv($handle, ['SKU', 'Produk', 'Qty Terjual', 'Omzet', 'Stok Saat Ini']);
            foreach ($data['topProducts'] as $product) {
                fputcsv($handle, [
                    $product->product_sku_snapshot,
                    $product->product_name_snapshot,
                    $product->total_quantity,
                    $product->total_revenue,
                    $product->current_stock,
                ]);
            }
            fputcsv($handle, []);

            fputcsv($handle, ['Stok Menipis']);
            fputcsv($handle, ['Produk', 'Kategori', 'Stok', 'Minimum']);
            foreach ($data['lowStockProducts'] as $product) {
                fputcsv($handle, [
                    $product->name,
                    $product->category?->name,
                    $product->stock,
                    $product->minimum_stock,
                ]);
            }
            fputcsv($handle, []);

            fputcsv($handle, ['Transaksi Terbaru']);
            fputcsv($handle, ['Invoice', 'Tanggal', 'Pelanggan', 'Item', 'Total']);
            foreach ($data['recentSales'] as $sale) {
                fputcsv($handle, [
                    $sale->invoice_number,
                    $sale->sale_date->format('Y-m-d H:i'),
                    $sale->customer_name ?: 'Umum',
                    $sale->items_count,
                    $sale->total,
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function reportData(Request $request): array
    {
        $period = $request->query('period', 'this_month');
        [$from, $to] = $this->resolveDateRange($request, $period);

        if ($from->gt($to)) {
            [$from, $to] = [$to, $from];
        }

        $salesQuery = Sale::query()
            ->whereBetween('sale_date', [$from->copy()->startOfDay(), $to->copy()->endOfDay()]);

        $revenue = (float) (clone $salesQuery)->sum('total');
        $subtotal = (float) (clone $salesQuery)->sum('subtotal');
        $discount = (float) (clone $salesQuery)->sum('discount');
        $transactions = (int) (clone $salesQuery)->count();
        $averageTransaction = $transactions > 0 ? $revenue / $transactions : 0;

        $soldItems = SaleItem::query()
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereBetween('sales.sale_date', [$from->copy()->startOfDay(), $to->copy()->endOfDay()]);

        $soldQuantity = (int) (clone $soldItems)->sum('sale_items.quantity');

        $topProducts = (clone $soldItems)
            ->leftJoin('products', 'sale_items.product_id', '=', 'products.id')
            ->select([
                'sale_items.product_id',
                'sale_items.product_name_snapshot',
                'sale_items.product_sku_snapshot',
                DB::raw('COALESCE(products.stock, 0) as current_stock'),
                DB::raw('SUM(sale_items.quantity) as total_quantity'),
                DB::raw('SUM(sale_items.subtotal) as total_revenue'),
            ])
            ->groupBy('sale_items.product_id', 'sale_items.product_name_snapshot', 'sale_items.product_sku_snapshot', 'products.stock')
            ->orderByDesc('total_quantity')
            ->limit(8)
            ->get();

        $stockMovements = StockMovement::query()
            ->whereBetween('created_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()]);

        $stockIn = (int) (clone $stockMovements)->where('type', StockMovement::TYPE_IN)->sum('quantity');
        $stockOut = (int) (clone $stockMovements)->where('type', StockMovement::TYPE_OUT)->sum('quantity');
        $stockAdjustments = (int) (clone $stockMovements)->where('type', StockMovement::TYPE_ADJUSTMENT)->sum('quantity');

        $lowStockProducts = Product::query()
            ->with('category')
            ->where('is_active', true)
            ->whereColumn('stock', '<=', 'minimum_stock')
            ->orderBy('stock')
            ->orderBy('name')
            ->limit(10)
            ->get();

        $inventoryValue = (float) Product::query()
            ->selectRaw('COALESCE(SUM(stock * purchase_price), 0) as value')
            ->value('value');

        $chartLabels = collect();
        $chartValues = collect();
        $cursor = $from->copy();
        while ($cursor->lte($to)) {
            $date = $cursor->toDateString();
            $chartLabels->push($cursor->translatedFormat('d M'));
            $chartValues->push((float) Sale::whereDate('sale_date', $date)->sum('total'));
            $cursor->addDay();
        }

        $recentSales = (clone $salesQuery)
            ->withCount('items')
            ->latest('sale_date')
            ->limit(6)
            ->get();

        return [
            'period' => $period,
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'revenue' => $revenue,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'transactions' => $transactions,
            'averageTransaction' => $averageTransaction,
            'soldQuantity' => $soldQuantity,
            'stockIn' => $stockIn,
            'stockOut' => $stockOut,
            'stockAdjustments' => $stockAdjustments,
            'topProducts' => $topProducts,
            'lowStockProducts' => $lowStockProducts,
            'inventoryValue' => $inventoryValue,
            'chartLabels' => $chartLabels,
            'chartValues' => $chartValues,
            'recentSales' => $recentSales,
        ];
    }

    private function resolveDateRange(Request $request, string $period): array
    {
        $today = Carbon::today();

        return match ($period) {
            'today' => [$today->copy(), $today->copy()],
            'last_7_days' => [$today->copy()->subDays(6), $today->copy()],
            'this_year' => [$today->copy()->startOfYear(), $today->copy()->endOfYear()],
            'custom' => [
                $this->safeDate($request->query('from'), $today->copy()->startOfMonth()),
                $this->safeDate($request->query('to'), $today->copy()),
            ],
            default => [$today->copy()->startOfMonth(), $today->copy()->endOfMonth()],
        };
    }

    private function safeDate(?string $value, Carbon $fallback): Carbon
    {
        if (! $value) {
            return $fallback;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return $fallback;
        }
    }
}
