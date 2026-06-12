<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use Illuminate\Http\Request;

class FineController extends Controller
{
    /**
     * Admin: show all borrowings that have a fine.
     */
    public function index(Request $request)
    {
        $query = Borrowing::with(['user', 'item.category'])
            ->where('status', 'returned')
            ->where('fine_amount', '>', 0)
            ->orderByDesc('actual_return_date');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('item', fn($i) => $i->where('name', 'like', "%{$search}%"));
            });
        }

        $fines      = $query->paginate(20)->withQueryString();
        $totalFines = Borrowing::where('status', 'returned')->sum('fine_amount');

        return view('admin.fines.index', compact('fines', 'totalFines'));
    }

    /**
     * Export fine report as CSV.
     */
    public function export()
    {
        $fines = Borrowing::with(['user', 'item.category'])
            ->where('status', 'returned')
            ->where('fine_amount', '>', 0)
            ->orderByDesc('actual_return_date')
            ->get();

        $filename = 'laporan-denda-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($fines) {
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
                'Keterlambatan (Hari)',
                'Denda (Rp)',
            ]);

            foreach ($fines as $index => $fine) {
                $returnDate = $fine->return_date ? $fine->return_date->format('d/m/Y') : '-';
                $actualDate = $fine->actual_return_date ? $fine->actual_return_date->format('d/m/Y') : '-';
                $overdueDays = 0;
                if ($fine->return_date && $fine->actual_return_date) {
                    $overdueDays = max(0, $fine->return_date->diffInDays($fine->actual_return_date, false));
                }

                fputcsv($handle, [
                    $index + 1,
                    $fine->user->name ?? '-',
                    $fine->user->email ?? '-',
                    $fine->item->name ?? '-',
                    $fine->item->category->name ?? '-',
                    $fine->quantity,
                    $fine->borrow_date ? $fine->borrow_date->format('d/m/Y') : '-',
                    $returnDate,
                    $actualDate,
                    $overdueDays,
                    $fine->fine_amount,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
