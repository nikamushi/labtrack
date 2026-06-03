<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BorrowingController extends Controller
{
    /**
     * Admin: list all pending borrow requests.
     */
    public function adminIndex()
    {
        $borrowings = Borrowing::with(['user', 'item.category'])
            ->whereIn('status', ['pending'])
            ->latest()
            ->paginate(15);

        return view('admin.borrowings.index', compact('borrowings'));
    }

    /**
     * Admin: approve a borrow request.
     */
    public function approve(Borrowing $borrowing)
    {
        if ($borrowing->status !== 'pending') {
            return back()->with('error', 'Permintaan ini sudah diproses sebelumnya.');
        }

        $item = $borrowing->item;

        if ($item->stock < $borrowing->quantity) {
            return back()->with('error', 'Stok tidak mencukupi untuk menyetujui permintaan ini.');
        }

        // Deduct stock
        $item->decrement('stock', $borrowing->quantity);
        $item->refresh();

        // Sync item status: borrowed if no stock left, else stays available
        if ($item->condition === 'maintenance') {
            $item->update(['status' => 'maintenance']);
        } elseif ($item->condition === 'lost') {
            $item->update(['status' => 'unavailable']);
        } elseif ($item->stock === 0) {
            $item->update(['status' => 'borrowed']);
        } else {
            $item->update(['status' => 'available']);
        }

        $borrowing->update(['status' => 'approved']);

        return back()->with('success', 'Permintaan peminjaman disetujui.');
    }

    /**
     * Admin: reject a borrow request.
     */
    public function reject(Borrowing $borrowing)
    {
        if ($borrowing->status !== 'pending') {
            return back()->with('error', 'Permintaan ini sudah diproses sebelumnya.');
        }

        $borrowing->update(['status' => 'rejected']);

        return back()->with('success', 'Permintaan peminjaman ditolak.');
    }

    /**
     * Student: list own active borrowings.
     */
    public function studentIndex()
    {
        $borrowings = Borrowing::with('item.category')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(15);

        return view('student.borrowings.index', compact('borrowings'));
    }

    /**
     * Student: submit a new borrow request.
     */
    public function store(Request $request)
    {
        $request->validate([
            'item_id'     => 'required|exists:items,id',
            'quantity'    => 'required|integer|min:1',
            'borrow_date' => 'required|date|after_or_equal:today',
            'return_date' => 'required|date|after:borrow_date',
        ]);

        $item = Item::findOrFail($request->item_id);

        if ($item->status !== 'available') {
            return back()->with('error', 'Barang ini tidak tersedia untuk dipinjam.');
        }

        if ($item->stock < $request->quantity) {
            return back()->with('error', "Stok tidak mencukupi. Stok tersedia: {$item->stock}.");
        }

        Borrowing::create([
            'user_id'     => Auth::id(),
            'item_id'     => $item->id,
            'quantity'    => $request->quantity,
            'borrow_date' => $request->borrow_date,
            'return_date' => $request->return_date,
            'status'      => 'pending',
        ]);

        return redirect()->route('student.borrowings.index')
            ->with('success', 'Permintaan peminjaman berhasil dikirim. Menunggu persetujuan admin.');
    }
}
