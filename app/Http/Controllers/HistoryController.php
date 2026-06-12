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

    /**
     * Export borrowing history report as CSV.
     */
    public function export(Request $request)
    {
        $query = Borrowing::with(['user', 'item.category'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->whereHas('item', fn($q) => $q->where('name', 'like', '%' . $request->search . '%'))
                  ->orWhereHas('user', fn($q) => $q->where('name', 'like', '%' . $request->search . '%'));
        }

        $history = $query->get();
        $filename = 'laporan-riwayat-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($history) {
            $handle = fopen('php://output', 'w');

            // BOM for Excel UTF-8 compatibility
            fputs($handle, "\xEF\xBB\xBF");

            // Header row
            fputcsv($handle, [
                'No',
                'Nama Mahasiswa',
                'Email',
                'Barang',
                'Kategori',
                'Jumlah',
                'Tgl Pinjam',
                'Rencana Kembali',
                'Tgl Dikembalikan',
                'Status',
                'Denda (Rp)',
            ]);

            foreach ($history as $index => $row) {
                $returnDate = $row->return_date ? $row->return_date->format('d/m/Y') : '-';
                $actualDate = $row->actual_return_date ? $row->actual_return_date->format('d/m/Y') : '-';
                
                fputcsv($handle, [
                    $index + 1,
                    $row->user->name ?? '-',
                    $row->user->email ?? '-',
                    $row->item->name ?? '-',
                    $row->item->category->name ?? '-',
                    $row->quantity,
                    $row->borrow_date ? $row->borrow_date->format('d/m/Y') : '-',
                    $returnDate,
                    $actualDate,
                    ucfirst($row->status),
                    $row->fine_amount,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
