<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(): View
    {
        // General stats
        $totalRevenue = Order::whereHas('payment', function($q) {
                $q->where('status', PaymentStatus::SETTLED);
            })->sum('grand_total');

        $totalOrders = Order::count();
        $totalProducts = Product::count();
        $totalCustomers = User::role('customer')->count();

        // Low stock products alert
        $lowStockProducts = Product::where('stock', '<', 5)
            ->with('primaryImage')
            ->take(5)
            ->get();

        // Recent orders
        $recentOrders = Order::with(['user', 'payment'])
            ->latest()
            ->take(5)
            ->get();

        // Monthly sales chart data (last 6 months)
        $salesData = Order::select(
                DB::raw('SUM(grand_total) as total'),
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month")
            )
            ->whereHas('payment', function($q) {
                $q->where('status', PaymentStatus::SETTLED);
            })
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        return view('admin.dashboard', compact(
            'totalRevenue',
            'totalOrders',
            'totalProducts',
            'totalCustomers',
            'lowStockProducts',
            'recentOrders',
            'salesData'
        ));
    }
}
