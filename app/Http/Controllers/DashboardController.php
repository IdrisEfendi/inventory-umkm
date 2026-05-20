<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = Carbon::today();

        $todayRevenue = Sale::whereDate('sale_date', $today)->sum('total');
        $todaySalesCount = Sale::whereDate('sale_date', $today)->count();
        $activeProductsCount = Product::where('is_active', true)->count();
        $lowStockProducts = Product::with('category')
            ->where('is_active', true)
            ->whereColumn('stock', '<=', 'minimum_stock')
            ->orderBy('stock')
            ->limit(5)
            ->get();

        $recentSales = Sale::withCount('items')
            ->latest('sale_date')
            ->limit(5)
            ->get();

        $salesChartLabels = collect(range(6, 0))->map(function (int $daysAgo) {
            return Carbon::today()->subDays($daysAgo)->translatedFormat('d M');
        });

        $salesChartValues = collect(range(6, 0))->map(function (int $daysAgo) {
            $date = Carbon::today()->subDays($daysAgo);

            return (float) Sale::whereDate('sale_date', $date)->sum('total');
        });

        return view('dashboard.index', [
            'todayRevenue' => $todayRevenue,
            'todaySalesCount' => $todaySalesCount,
            'activeProductsCount' => $activeProductsCount,
            'lowStockProductsCount' => Product::where('is_active', true)->whereColumn('stock', '<=', 'minimum_stock')->count(),
            'lowStockProducts' => $lowStockProducts,
            'recentSales' => $recentSales,
            'salesChartLabels' => $salesChartLabels,
            'salesChartValues' => $salesChartValues,
        ]);
    }
}
