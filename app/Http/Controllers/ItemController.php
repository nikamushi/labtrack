<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::with('category');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $items      = $query->latest()->paginate(15)->withQueryString();
        $allItems   = Item::all(); // for stats cards (unfiltered)
        $categories = Category::orderBy('name')->get();

        return view('admin.items.index', compact('items', 'allItems', 'categories'));

    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.items.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|string|max:150',
            'stock'       => 'required|integer|min:0',
            'condition'   => 'required|in:good,damaged,lost,maintenance',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $data = $request->only('category_id', 'name', 'stock', 'condition');
        // Auto-derive status from condition
        $data['status'] = match($data['condition']) {
            'maintenance' => 'maintenance',
            'lost'        => 'unavailable',
            default       => ($data['stock'] > 0 ? 'available' : 'unavailable'),
        };

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('items', 'public');
            $data['image'] = $imagePath;
        }

        Item::create($data);

        return redirect()->route('admin.items.index')
            ->with('success', 'Barang berhasil ditambahkan.');
    }

    public function show(Item $item)
    {
        return redirect()->route('admin.items.index');
    }

    public function edit(Item $item)
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.items.edit', compact('item', 'categories'));
    }

    public function update(Request $request, Item $item)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|string|max:150',
            'stock'       => 'required|integer|min:0',
            'condition'   => 'required|in:good,damaged,lost,maintenance',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $data = $request->only('category_id', 'name', 'stock', 'condition');
        // Auto-derive status from condition (takes priority over stock count)
        if ($data['condition'] === 'maintenance') {
            $data['status'] = 'maintenance';
        } elseif ($data['condition'] === 'lost') {
            $data['status'] = 'unavailable';
        } else {
            // For good/damaged: derive from net available stock
            $borrowedCount   = $item->borrowings()->where('status', 'approved')->sum('quantity');
            $availableStock  = $data['stock'] - $borrowedCount;
            $data['status']  = $availableStock > 0 ? 'available' : 'unavailable';
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($item->image && Storage::disk('public')->exists($item->image)) {
                Storage::disk('public')->delete($item->image);
            }
            $imagePath = $request->file('image')->store('items', 'public');
            $data['image'] = $imagePath;
        }

        $item->update($data);

        return redirect()->route('admin.items.index')
            ->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy(Item $item)
    {
        if ($item->borrowings()->whereIn('status', ['pending', 'approved', 'return_requested'])->exists()) {
            return redirect()->route('admin.items.index')
                ->with('error', 'Barang tidak dapat dihapus karena sedang dalam proses peminjaman.');
        }

        // Delete image if exists
        if ($item->image && Storage::disk('public')->exists($item->image)) {
            Storage::disk('public')->delete($item->image);
        }

        $item->delete();

        return redirect()->route('admin.items.index')
            ->with('success', 'Barang berhasil dihapus.');
    }

    /**
     * Student: view available items to borrow.
     */
    public function studentIndex(Request $request)
    {
        $query = Item::with('category')->where('status', 'available');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $items = $query->paginate(12)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('student.items.index', compact('items', 'categories'));
    }

    /**
     * Export items list as CSV.
     */
    public function export(Request $request)
    {
        $query = Item::with('category');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $items = $query->get();
        $filename = 'laporan-barang-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($items) {
            $handle = fopen('php://output', 'w');

            // BOM for Excel UTF-8 compatibility
            fputs($handle, "\xEF\xBB\xBF");

            // Header row
            fputcsv($handle, [
                'No',
                'Nama Barang',
                'Kategori',
                'Total Stok',
                'Kondisi',
                'Status',
            ]);

            foreach ($items as $index => $item) {
                fputcsv($handle, [
                    $index + 1,
                    $item->name,
                    $item->category->name ?? '-',
                    $item->stock,
                    ucfirst($item->condition),
                    ucfirst($item->status),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
