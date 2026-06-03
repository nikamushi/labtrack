<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller
{
    /**
     * Admin: all transaction history.
     */
    public function adminIndex(Request $request)
    {
        $query = Borrowing::with(['user', 'item.category'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->whereHas('item', fn($q) => $q->where('name', 'like', '%' . $request->search . '%'))
                  ->orWhereHas('user', fn($q) => $q->where('name', 'like', '%' . $request->search . '%'));
        }

        $history = $query->paginate(20)->withQueryString();

        return view('admin.history.index', compact('history'));
    }

    /**
     * Student: personal borrowing history.
     */
    public function studentIndex()
    {
        $history = Borrowing::with('item.category')
            ->where('user_id', Auth::id())
            ->whereIn('status', ['returned', 'rejected'])
            ->latest()
            ->paginate(15);

        return view('student.history.index', compact('history'));
    }
}
