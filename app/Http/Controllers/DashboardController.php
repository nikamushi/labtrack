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

        return view('admin.dashboard', compact(
            'totalItems',
            'totalAvailable',
            'totalBorrowed',
            'pendingCount',
            'returnPending',
            'topItems',
            'recentActivities'
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
