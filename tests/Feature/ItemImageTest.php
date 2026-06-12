<?php

use App\Models\User;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    // Buat admin user untuk autentikasi
    $this->admin = User::factory()->create([
        'role' => 'admin',
    ]);

    // Buat kategori untuk berelasi dengan item
    $this->category = Category::create([
        'name' => 'Alat Elektronik',
    ]);

    // Mock public storage disk
    Storage::fake('public');
});

test('admin can create item with image', function () {
    $file = UploadedFile::fake()->image('microscope.jpg');

    $response = $this->actingAs($this->admin)->post(route('admin.items.store'), [
        'category_id' => $this->category->id,
        'name'        => 'Mikroskop Binokuler Canggih',
        'stock'       => 5,
        'condition'   => 'good',
        'image'       => $file,
    ]);

    $response->assertRedirect(route('admin.items.index'));
    $response->assertSessionHas('success');

    // Pastikan item tersimpan di database beserta path gambar
    $item = Item::first();
    expect($item->image)->not->toBeNull();

    // Pastikan file gambar fisik terunggah di storage
    Storage::disk('public')->assertExists($item->image);
});

test('admin can update item image and old image is deleted', function () {
    $oldFile = UploadedFile::fake()->image('old_microscope.jpg');
    $item = Item::create([
        'category_id' => $this->category->id,
        'name'        => 'Mikroskop Lama',
        'stock'       => 2,
        'condition'   => 'good',
        'status'      => 'available',
        'image'       => $oldFile->store('items', 'public'),
    ]);

    // Pastikan gambar lama terunggah
    Storage::disk('public')->assertExists($item->image);
    $oldImagePath = $item->image;

    // Upload gambar baru
    $newFile = UploadedFile::fake()->image('new_microscope.jpg');

    $response = $this->actingAs($this->admin)->put(route('admin.items.update', $item), [
        'category_id' => $this->category->id,
        'name'        => 'Mikroskop Terupdate',
        'stock'       => 2,
        'condition'   => 'good',
        'image'       => $newFile,
    ]);

    $response->assertRedirect(route('admin.items.index'));
    
    // Refresh model item
    $item->refresh();

    // Pastikan gambar baru tersimpan dan gambar lama dihapus
    Storage::disk('public')->assertExists($item->image);
    Storage::disk('public')->assertMissing($oldImagePath);
});

test('image is deleted from storage when item is destroyed', function () {
    $file = UploadedFile::fake()->image('microscope_to_delete.jpg');
    $item = Item::create([
        'category_id' => $this->category->id,
        'name'        => 'Mikroskop Rusak Parah',
        'stock'       => 1,
        'condition'   => 'damaged',
        'status'      => 'available',
        'image'       => $file->store('items', 'public'),
    ]);

    Storage::disk('public')->assertExists($item->image);
    $imagePath = $item->image;

    $response = $this->actingAs($this->admin)->delete(route('admin.items.destroy', $item));
    $response->assertRedirect(route('admin.items.index'));

    // Pastikan item terhapus dari database
    $this->assertDatabaseMissing('items', ['id' => $item->id]);

    // Pastikan file gambar juga terhapus dari penyimpanan
    Storage::disk('public')->assertMissing($imagePath);
});

test('invalid image files are rejected', function () {
    // Bukan berkas gambar, melainkan berkas teks berganti nama
    $invalidFile = UploadedFile::fake()->create('document.pdf', 500, 'application/pdf');

    $response = $this->actingAs($this->admin)->post(route('admin.items.store'), [
        'category_id' => $this->category->id,
        'name'        => 'Mikroskop Palsu',
        'stock'       => 5,
        'condition'   => 'good',
        'image'       => $invalidFile,
    ]);

    $response->assertSessionHasErrors('image');
    expect(Item::count())->toBe(0);
});
