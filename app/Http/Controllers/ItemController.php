<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::with('category')->latest()->paginate(15);
        return view('admin.items.index', compact('items'));
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
            'condition'   => 'required|in:good,damaged,lost',
        ]);

        $data = $request->only('category_id', 'name', 'stock', 'condition');
        $data['status'] = $data['stock'] > 0 ? 'available' : 'unavailable';

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
            'condition'   => 'required|in:good,damaged,lost',
        ]);

        $data = $request->only('category_id', 'name', 'stock', 'condition');
        // Only auto-update status if stock changed and item isn't currently borrowed
        $borrowedCount = $item->borrowings()->where('status', 'approved')->sum('quantity');
        $availableStock = $data['stock'] - $borrowedCount;
        $data['status'] = $availableStock > 0 ? 'available' : 'unavailable';

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
