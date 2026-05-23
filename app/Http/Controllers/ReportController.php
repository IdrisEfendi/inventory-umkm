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

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $period = $request->query('period', 'this_month');
        [$from, $to] = $this->resolveDateRange($request, $period);

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

        return view('reports.index', [
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
        ]);
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
