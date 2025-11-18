<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SupportTicketController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PartyController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\BankAccountController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::get('locale/{locale}', function (string $locale) {
    if (in_array($locale, ['en', 'hi'], true)) {
        session(['app_locale' => $locale]);
    }

    return back();
})->name('locale.switch');

Route::get('/dashboard', [DashboardController::class, '__invoke'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('products/import', [ProductController::class, 'import'])->name('products.import');
    Route::get('products/export', [ProductController::class, 'export'])->name('products.export');
    Route::get('products/search', [ProductController::class, 'search'])->name('products.search');
    Route::get('customers/search', [CustomerController::class, 'search'])->name('customers.search');

    Route::get('invoices/create', [SaleController::class, 'createInvoice'])->name('invoices.create');
    Route::get('sales/{sale}/invoice', [SaleController::class, 'invoice'])->name('sales.invoice');
    Route::post('settings/backup', [SettingController::class, 'runBackup'])->name('settings.backup');
    Route::get('settings/backup/{file}', [SettingController::class, 'downloadBackup'])->name('settings.download');

    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingController::class, 'update'])->name('settings.update');

    Route::post('users/bulk-delete', [UserController::class, 'bulkDestroy'])->name('users.bulk-destroy');
    Route::post('customers/bulk-delete', [CustomerController::class, 'bulkDestroy'])->name('customers.bulk-destroy');

    Route::resources([
        'products' => ProductController::class,
        'categories' => CategoryController::class,
        'customers' => CustomerController::class,
        'sales' => SaleController::class,
        'tickets' => SupportTicketController::class,
        'users' => UserController::class,
        'parties' => PartyController::class,
        'purchases' => PurchaseController::class,
        'expenses' => ExpenseController::class,
        'payments' => PaymentController::class,
        'quotations' => QuotationController::class,
        'stock-adjustments' => StockAdjustmentController::class,
        'bank-accounts' => BankAccountController::class,
    ]);

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
});

require __DIR__.'/auth.php';
