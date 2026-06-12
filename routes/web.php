<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\AdminStudentController;
use App\Http\Controllers\FineController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Dynamic dashboard route that redirects based on role
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin Routes Group
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
        Route::resource('categories', CategoryController::class);
        Route::get('/items/export', [ItemController::class, 'export'])->name('items.export');
        Route::resource('items', ItemController::class);
        Route::resource('students', AdminStudentController::class);
        
        Route::get('/borrowings', [BorrowingController::class, 'adminIndex'])->name('borrowings.index');
        Route::patch('/borrowings/{borrowing}/approve', [BorrowingController::class, 'approve'])->name('borrowings.approve');
        Route::patch('/borrowings/{borrowing}/reject', [BorrowingController::class, 'reject'])->name('borrowings.reject');
        
        Route::get('/returns', [ReturnController::class, 'adminIndex'])->name('returns.index');
        Route::patch('/returns/{borrowing}/approve', [ReturnController::class, 'approve'])->name('returns.approve');
        
        Route::get('/history/export', [HistoryController::class, 'export'])->name('history.export');
        Route::get('/history', [HistoryController::class, 'adminIndex'])->name('history.index');

        Route::get('/fines/export', [FineController::class, 'export'])->name('fines.export');
        Route::get('/fines', [FineController::class, 'index'])->name('fines.index');
    });

    // Student Routes Group
    Route::middleware(['role:student'])->prefix('student')->name('student.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'student'])->name('dashboard');
        
        Route::get('/items', [ItemController::class, 'studentIndex'])->name('items.index');
        Route::post('/borrowings', [BorrowingController::class, 'store'])->name('borrowings.store');
        Route::get('/borrowings', [BorrowingController::class, 'studentIndex'])->name('borrowings.index');
        Route::post('/borrowings/{borrowing}/return', [ReturnController::class, 'store'])->name('returns.store');
        
        Route::get('/history', [HistoryController::class, 'studentIndex'])->name('history.index');
    });
});

require __DIR__.'/auth.php';
