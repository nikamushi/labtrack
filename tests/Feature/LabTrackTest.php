<?php

use App\Models\User;
use App\Models\Category;
use App\Models\Item;
use App\Models\Borrowing;

test('admin can access dashboard and view statistics', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->get('/admin/dashboard');

    $response->assertStatus(200);
});

test('student cannot access admin dashboard', function () {
    $student = User::factory()->create(['role' => 'student']);

    $response = $this->actingAs($student)->get('/admin/dashboard');

    $response->assertStatus(403);
});

test('admin can manage categories', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    // Create
    $response = $this->actingAs($admin)->post('/admin/categories', [
        'name' => 'Tools',
        'description' => 'Laboratory tools',
    ]);
    $response->assertRedirect(route('admin.categories.index'));
    $this->assertDatabaseHas('categories', ['name' => 'Tools']);

    $category = Category::first();

    // Edit
    $response = $this->actingAs($admin)->put("/admin/categories/{$category->id}", [
        'name' => 'Updated Tools',
        'description' => 'Updated desc',
    ]);
    $response->assertRedirect(route('admin.categories.index'));
    $this->assertDatabaseHas('categories', ['name' => 'Updated Tools']);

    // Delete
    $response = $this->actingAs($admin)->delete("/admin/categories/{$category->id}");
    $response->assertRedirect(route('admin.categories.index'));
    $this->assertDatabaseMissing('categories', ['name' => 'Updated Tools']);
});

test('admin can manage items', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $category = Category::create(['name' => 'Electronics']);

    // Create item
    $response = $this->actingAs($admin)->post('/admin/items', [
        'category_id' => $category->id,
        'name' => 'Arduino Uno',
        'stock' => 10,
        'condition' => 'good',
    ]);
    $response->assertRedirect(route('admin.items.index'));
    $this->assertDatabaseHas('items', [
        'name' => 'Arduino Uno',
        'stock' => 10,
        'condition' => 'good',
        'status' => 'available',
    ]);

    $item = Item::first();

    // Edit item (change condition to maintenance, status should auto-derive)
    $response = $this->actingAs($admin)->put("/admin/items/{$item->id}", [
        'category_id' => $category->id,
        'name' => 'Arduino Uno (Modified)',
        'stock' => 10,
        'condition' => 'maintenance',
    ]);
    $response->assertRedirect(route('admin.items.index'));
    $this->assertDatabaseHas('items', [
        'name' => 'Arduino Uno (Modified)',
        'condition' => 'maintenance',
        'status' => 'maintenance',
    ]);
});

test('admin can manage students', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    // Create student
    $response = $this->actingAs($admin)->post('/admin/students', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);
    $response->assertRedirect(route('admin.students.index'));
    $this->assertDatabaseHas('users', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'role' => 'student',
    ]);

    $student = User::where('email', 'john@example.com')->first();

    // Update student
    $response = $this->actingAs($admin)->put("/admin/students/{$student->id}", [
        'name' => 'John Doe Updated',
        'email' => 'john.updated@example.com',
    ]);
    $response->assertRedirect(route('admin.students.index'));
    $this->assertDatabaseHas('users', [
        'name' => 'John Doe Updated',
        'email' => 'john.updated@example.com',
    ]);

    // Delete student
    $response = $this->actingAs($admin)->delete("/admin/students/{$student->id}");
    $response->assertRedirect(route('admin.students.index'));
    $this->assertDatabaseMissing('users', [
        'email' => 'john.updated@example.com',
    ]);
});

test('borrowing flow lifecycle works correctly', function () {
    $student = User::factory()->create(['role' => 'student']);
    $admin = User::factory()->create(['role' => 'admin']);
    $category = Category::create(['name' => 'Network']);
    $item = Item::create([
        'category_id' => $category->id,
        'name' => 'Switch Cisco',
        'stock' => 5,
        'condition' => 'good',
        'status' => 'available',
    ]);

    // Student requests borrow
    $response = $this->actingAs($student)->post('/student/borrowings', [
        'item_id' => $item->id,
        'quantity' => 2,
        'borrow_date' => now()->format('Y-m-d'),
        'return_date' => now()->addDays(3)->format('Y-m-d'),
    ]);
    $response->assertRedirect(route('student.borrowings.index'));
    $this->assertDatabaseHas('borrowings', [
        'user_id' => $student->id,
        'item_id' => $item->id,
        'quantity' => 2,
        'status' => 'pending',
    ]);

    // Stock should not change while pending
    $item->refresh();
    expect($item->stock)->toBe(5);

    $borrowing = Borrowing::first();

    // Admin approves borrow
    $response = $this->actingAs($admin)->patch("/admin/borrowings/{$borrowing->id}/approve");
    $response->assertSessionHasNoErrors();
    $this->assertDatabaseHas('borrowings', [
        'id' => $borrowing->id,
        'status' => 'approved',
    ]);

    // Stock should decrease
    $item->refresh();
    expect($item->stock)->toBe(3);

    // Student requests return
    $response = $this->actingAs($student)->post("/student/borrowings/{$borrowing->id}/return");
    $response->assertSessionHasNoErrors();
    $this->assertDatabaseHas('borrowings', [
        'id' => $borrowing->id,
        'status' => 'return_requested',
    ]);

    // Admin approves return
    $response = $this->actingAs($admin)->patch("/admin/returns/{$borrowing->id}/approve");
    $response->assertSessionHasNoErrors();
    $this->assertDatabaseHas('borrowings', [
        'id' => $borrowing->id,
        'status' => 'returned',
    ]);

    // Stock should increase
    $item->refresh();
    expect($item->stock)->toBe(5);
});

test('returning on time has no fine', function () {
    $student = User::factory()->create(['role' => 'student']);
    $admin = User::factory()->create(['role' => 'admin']);
    $category = Category::create(['name' => 'Tools']);
    $item = Item::create([
        'category_id' => $category->id,
        'name' => 'Hammer',
        'stock' => 5,
        'condition' => 'good',
        'status' => 'available',
    ]);

    $borrowing = Borrowing::create([
        'user_id' => $student->id,
        'item_id' => $item->id,
        'quantity' => 1,
        'borrow_date' => now()->subDays(3)->format('Y-m-d'),
        'return_date' => now()->addDays(2)->format('Y-m-d'),
        'status' => 'return_requested',
    ]);

    $response = $this->actingAs($admin)->patch("/admin/returns/{$borrowing->id}/approve");
    $response->assertSessionHasNoErrors();
    
    $borrowing->refresh();
    expect($borrowing->fine_amount)->toBe(0);
});

test('returning late triggers a fine amount', function () {
    $student = User::factory()->create(['role' => 'student']);
    $admin = User::factory()->create(['role' => 'admin']);
    $category = Category::create(['name' => 'Tools']);
    $item = Item::create([
        'category_id' => $category->id,
        'name' => 'Screwdriver',
        'stock' => 5,
        'condition' => 'good',
        'status' => 'available',
    ]);

    // return_date is 3 days ago, so actual_return_date (today) is 3 days late
    $borrowing = Borrowing::create([
        'user_id' => $student->id,
        'item_id' => $item->id,
        'quantity' => 1,
        'borrow_date' => now()->subDays(6)->format('Y-m-d'),
        'return_date' => now()->subDays(3)->format('Y-m-d'),
        'status' => 'return_requested',
    ]);

    $response = $this->actingAs($admin)->patch("/admin/returns/{$borrowing->id}/approve");
    $response->assertSessionHasNoErrors();
    
    $borrowing->refresh();
    // 3 days * Rp 5,000 = Rp 15,000
    expect($borrowing->fine_amount)->toBe(15000);
});

test('admin can export history to CSV', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $response = $this->actingAs($admin)->get('/admin/history/export');
    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
});

test('admin can export items to CSV', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $response = $this->actingAs($admin)->get('/admin/items/export');
    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
});

