<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RecordController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExpenseReportController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\Controller;

// Ana sayfa
Route::get('/', function () {
    return view('welcome');
});

// Dashboard (sadece giriş yapılmış ve e-posta doğrulanmış kullanıcılar için)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Laravel auth sistemini dahil et
require __DIR__.'/auth.php';

// Auth ile korunan tüm işlemler
Route::middleware('auth')->group(function () {

    // Kullanıcı profil işlemleri
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Gider ekleme (form + post)
    Route::get('/expenses/create', [ExpenseController::class, 'create'])->name('expenses.create');
    Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');

    // Gider işlemleri ve PDF/grafik raporlar
    Route::prefix('expenses')->name('expenses.')->controller(ExpenseController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/{id}/approve', 'approve')->name('approve');
        Route::post('/{id}/reject', 'reject')->name('reject');
        Route::get('/download-report', 'downloadReport')->name('download-report');
        Route::get('/download-charts', 'downloadCharts')->name('download-charts');

        // Yeni: Fatura indirme route'u
        Route::get('/{id}/download-invoice', 'downloadInvoice')->name('download-invoice');
    });

    // Kullanıcılar ve kayıt işlemleri
    Route::resource('users', UserController::class);
    Route::resource('records', RecordController::class);

    // Tekil gider gösterimi
    Route::get('/expense/{expenseId}', [Controller::class, 'showExpense'])->name('expense.show');

    // İzin işlemleri (Spatie Permission)
    Route::resource('permissions', PermissionController::class);
});
