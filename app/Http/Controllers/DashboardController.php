<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Borrowing;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Redirect to role-based dashboard.
     */
    public function index()
    {
        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('student.dashboard');
    }

    /**
     * Admin Dashboard.
     */
    public function admin()
    {
        $totalItems     = Item::sum('stock');
        $totalAvailable = Item::where('status', 'available')->sum('stock');
        $totalBorrowed  = Borrowing::whereIn('status', ['approved'])->sum('quantity');
        $pendingCount   = Borrowing::where('status', 'pending')->count();
        $returnPending  = Borrowing::where('status', 'return_requested')->count();

        // Top 5 most borrowed items
        $topItems = Item::withCount(['borrowings as borrow_count' => function ($q) {
            $q->whereNotIn('status', ['rejected']);
        }])
            ->orderByDesc('borrow_count')
            ->take(5)
            ->get();

        // Recent 8 activities
        $recentActivities = Borrowing::with(['user', 'item'])
            ->latest()
            ->take(8)
            ->get();

        // Advanced Analytics: Trend peminjaman 7 hari terakhir
        $trendLabels = [];
        $trendValues = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $trendLabels[] = now()->subDays($i)->format('d M');
            $trendValues[$date] = 0;
        }

        $trendRaw = Borrowing::select(\Illuminate\Support\Facades\DB::raw('DATE(borrow_date) as date'), \Illuminate\Support\Facades\DB::raw('SUM(quantity) as total'))
            ->where('borrow_date', '>=', now()->subDays(6)->format('Y-m-d'))
            ->whereIn('status', ['approved', 'returned', 'return_requested'])
            ->groupBy('date')
            ->get();

        foreach ($trendRaw as $data) {
            if (isset($trendValues[$data->date])) {
                $trendValues[$data->date] = (int) $data->total;
            }
        }
        $trendValues = array_values($trendValues);

        // Advanced Analytics: Distribusi barang per kategori
        $categoriesRaw = Category::withSum('items as total_stock', 'stock')->get();
        $categoryLabels = [];
        $categoryStocks = [];
        foreach ($categoriesRaw as $cat) {
            $categoryLabels[] = $cat->name;
            $categoryStocks[] = (int) ($cat->total_stock ?? 0);
        }

        // Advanced Analytics: Distribusi kondisi fisik barang
        $conditionsRaw = Item::select('condition', \Illuminate\Support\Facades\DB::raw('SUM(stock) as total'))
            ->groupBy('condition')
            ->get();
        $conditionLabels = ['good' => 'Baik', 'damaged' => 'Rusak', 'lost' => 'Hilang', 'maintenance' => 'Perawatan'];
        $conditionDataMap = ['good' => 0, 'damaged' => 0, 'lost' => 0, 'maintenance' => 0];
        foreach ($conditionsRaw as $cond) {
            if (isset($conditionDataMap[$cond->condition])) {
                $conditionDataMap[$cond->condition] = (int) $cond->total;
            }
        }
        $conditionDataset = [
            'labels' => array_values($conditionLabels),
            'values' => [
                $conditionDataMap['good'],
                $conditionDataMap['damaged'],
                $conditionDataMap['lost'],
                $conditionDataMap['maintenance']
            ]
        ];

        return view('admin.dashboard', compact(
            'totalItems',
            'totalAvailable',
            'totalBorrowed',
            'pendingCount',
            'returnPending',
            'topItems',
            'recentActivities',
            'trendLabels',
            'trendValues',
            'categoryLabels',
            'categoryStocks',
            'conditionDataset'
        ));
    }

    /**
     * Student Dashboard.
     */
    public function student()
    {
        $user = Auth::user();

        $activeBorrowings = Borrowing::where('user_id', $user->id)
            ->where('status', 'approved')
            ->count();

        $pendingRequests = Borrowing::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();

        $historyCount = Borrowing::where('user_id', $user->id)
            ->whereIn('status', ['returned', 'rejected'])
            ->count();

        $recentBorrowings = Borrowing::with('item.category')
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('student.dashboard', compact(
            'activeBorrowings',
            'pendingRequests',
            'historyCount',
            'recentBorrowings'
        ));
    }
}
