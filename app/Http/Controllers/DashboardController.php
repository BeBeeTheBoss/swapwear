<?php

namespace App\Http\Controllers;

use App\Models\MainCategory;
use App\Models\Order;
use App\Models\SellingProduct;
use App\Models\SubCategory;
use App\Models\User;
use App\Models\VerifiedApproveRequest;
use Carbon\Carbon;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $monthStart = now()->startOfMonth();
        $previousMonthStart = $monthStart->copy()->subMonth();
        $stats = [
            $this->stat('Total users', User::where('role', '!=', 'admin'), $monthStart, $previousMonthStart),
            $this->stat('Active listings', SellingProduct::where('is_active', true), $monthStart, $previousMonthStart),
            $this->stat('Total orders', Order::query(), $monthStart, $previousMonthStart),
        ];
        $currentRevenue = (int) Order::where('created_at', '>=', $monthStart)->sum('total_price');
        $previousRevenue = (int) Order::whereBetween('created_at', [$previousMonthStart, $monthStart])->sum('total_price');
        $stats[] = ['label' => 'Monthly revenue', 'value' => $currentRevenue, 'change' => $this->percentageChange($currentRevenue, $previousRevenue), 'format' => 'currency'];

        $orderActivity = collect(range(6, 0))->map(function ($daysAgo) {
            $date = Carbon::today()->subDays($daysAgo);
            return ['label' => $date->format('D'), 'date' => $date->format('M j'), 'orders' => Order::whereDate('created_at', $date)->count(), 'revenue' => (int) Order::whereDate('created_at', $date)->sum('total_price')];
        });
        $recentOrders = Order::with(['user:id,name', 'selling_product:id,name'])->latest()->limit(6)->get()->map(fn ($order) => [
            'id' => $order->id, 'code' => $order->order_code, 'customer' => $order->user?->name ?? 'Unknown user',
            'product' => $order->selling_product?->name ?? 'Deleted product', 'total' => (int) $order->total_price,
            'status' => $order->status, 'date' => $order->created_at?->diffForHumans(),
        ]);
        $categories = MainCategory::withCount('sub_categories')->orderByDesc('sub_categories_count')->limit(5)->get(['id', 'name'])->map(fn ($category) => ['id' => $category->id, 'name' => $category->name, 'count' => $category->sub_categories_count]);

        return Inertia::render('Dashboard', [
            'stats' => $stats, 'orderActivity' => $orderActivity, 'recentOrders' => $recentOrders, 'categories' => $categories,
            'summary' => [
                'pendingOrders' => Order::whereIn('status', ['order-pending', 'payment-pending', 'On-Hold'])->count(),
                'pendingProducts' => SellingProduct::where('is_active', false)->count(),
                'pendingVerifications' => VerifiedApproveRequest::where('status', 'pending')->count(),
                'mainCategories' => MainCategory::count(), 'subCategories' => SubCategory::count(),
            ],
        ]);
    }

    private function stat(string $label, $query, Carbon $monthStart, Carbon $previousMonthStart): array
    {
        $value = (clone $query)->count();
        $current = (clone $query)->where('created_at', '>=', $monthStart)->count();
        $previous = (clone $query)->whereBetween('created_at', [$previousMonthStart, $monthStart])->count();
        return ['label' => $label, 'value' => $value, 'change' => $this->percentageChange($current, $previous), 'format' => 'number'];
    }

    private function percentageChange(int $current, int $previous): int
    {
        if ($previous === 0) return $current > 0 ? 100 : 0;
        return (int) round((($current - $previous) / $previous) * 100);
    }
}
