<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReturnController extends Controller
{
    /**
     * Admin: list all return requests.
     */
    public function adminIndex()
    {
        $borrowings = Borrowing::with(['user', 'item.category'])
            ->where('status', 'return_requested')
            ->latest()
            ->paginate(15);

        return view('admin.returns.index', compact('borrowings'));
    }

    /**
     * Admin: confirm a return.
     */
    public function approve(Borrowing $borrowing)
    {
        if ($borrowing->status !== 'return_requested') {
            return back()->with('error', 'Permintaan pengembalian ini tidak valid atau sudah diproses.');
        }

        $item = $borrowing->item;

        // Add back stock
        $item->increment('stock', $borrowing->quantity);
        if ($item->fresh()->stock > 0) {
            $item->update(['status' => 'available']);
        }

        $borrowing->update(['status' => 'returned']);

        return back()->with('success', 'Pengembalian berhasil dikonfirmasi dan stok barang telah diperbarui.');
    }

    /**
     * Student: submit a return request.
     */
    public function store(Borrowing $borrowing)
    {
        // Ensure the borrowing belongs to this student
        if ($borrowing->user_id !== Auth::id()) {
            abort(403);
        }

        if ($borrowing->status !== 'approved') {
            return back()->with('error', 'Hanya peminjaman yang disetujui yang bisa dikembalikan.');
        }

        $borrowing->update(['status' => 'return_requested']);

        return back()->with('success', 'Permintaan pengembalian berhasil dikirim. Menunggu konfirmasi admin.');
    }
}
