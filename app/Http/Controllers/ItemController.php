<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use Illuminate\Http\Request;

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
        ]);

        $data = $request->only('category_id', 'name', 'stock', 'condition');
        // Auto-derive status from condition
        $data['status'] = match($data['condition']) {
            'maintenance' => 'maintenance',
            'lost'        => 'unavailable',
            default       => ($data['stock'] > 0 ? 'available' : 'unavailable'),
        };

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
}
