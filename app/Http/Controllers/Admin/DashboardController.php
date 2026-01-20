<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        /* METRIC  */
        $totalRevenue = Transaction::whereIn('status', ['paid', 'completed'])
            ->sum(DB::raw('total_amount + shipping_fee'));

        // Order metrics
        $totalOrders      = Transaction::whereIn('status', ['paid', 'completed'])->count();
        $pendingOrders    = Transaction::where('status', 'pending')->count();
        $paidOrders       = Transaction::where('status', 'paid')->count();
        $cancelledOrders  = Transaction::where('status', 'cancelled')->count();
        $completedOrders  = Transaction::where('status', 'completed')->count();
        $totalProducts   = Product::count();
        $totalCategories = Category::count();
        $totalUsers      = User::count();

        /* ORDER TERBARU */
        $recentOrders = Transaction::with('user')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        /* WEEKLY REVENUE */
        $weeklyRaw = Transaction::whereIn('status', ['paid', 'completed'])
            ->whereBetween('created_at', [
                now()->subDays(6)->startOfDay(),
                now()->endOfDay()
            ])
            ->selectRaw('DATE(created_at) as date, SUM(total_amount + shipping_fee) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        $weeklyRevenue = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $weeklyRevenue->put(
                Carbon::parse($date)->format('D'),
                (int) ($weeklyRaw[$date] ?? 0)
            );
        }

        /* MONTHLY REVENUE */
        $monthlyRaw = Transaction::whereIn('status', ['paid', 'completed'])
            ->whereYear('created_at', now()->year)
            ->selectRaw('MONTH(created_at) as month, SUM(total_amount + shipping_fee) as total')
            ->groupBy('month')
            ->pluck('total', 'month');

        $monthNames = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        $monthlyRevenue = collect();

        foreach (range(1, 12) as $month) {
            $monthlyRevenue->put(
                $monthNames[$month - 1],
                (int) ($monthlyRaw[$month] ?? 0)
            );
        }

        /* YEARLY REVENUE */
        $yearlyRevenue = Transaction::whereIn('status', ['paid', 'completed'])
            ->selectRaw('YEAR(created_at) as year, SUM(total_amount + shipping_fee) as total')
            ->groupBy('year')
            ->orderBy('year')
            ->pluck('total', 'year');

        /* ORDER STATUS CHART*/
        $expectedStatuses = ['pending', 'paid', 'cancelled', 'completed'];

        $orderStatusRaw = Transaction::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $orderStatus = collect($expectedStatuses)->mapWithKeys(function ($status) use ($orderStatusRaw) {
            return [$status => (int) ($orderStatusRaw[$status] ?? 0)];
        });

        return view('admin.dashboard', compact(
            'totalRevenue',
            'totalOrders',
            'pendingOrders',
            'paidOrders',
            'cancelledOrders',
            'completedOrders',
            'totalProducts',
            'totalCategories',
            'totalUsers',
            'recentOrders',
            'weeklyRevenue',
            'monthlyRevenue',
            'yearlyRevenue',
            'orderStatus'
        ));
    }

    /**
     * AJAX endpoint
     */
    public function stats()
    {
        $expectedStatuses = ['pending', 'paid', 'cancelled', 'completed'];

        $orderStatusRaw = Transaction::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $orderStatus = collect($expectedStatuses)->mapWithKeys(function ($status) use ($orderStatusRaw) {
            return [$status => (int) ($orderStatusRaw[$status] ?? 0)];
        });

        $totalRevenue = Transaction::whereIn('status', ['paid', 'completed'])
            ->sum(DB::raw('total_amount + shipping_fee'));

        return response()->json([
            'totalRevenue' => (int) $totalRevenue,
            'totalOrders'  => Transaction::whereIn('status', ['paid', 'completed'])->count(),
            'orderStatus'  => $orderStatus,
        ]);
    }
}
