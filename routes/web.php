<?php
// Laravel POS With jQuery @ https://laravelcenter.com
use App\Http\Controllers\BalanceAdjustmentController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->match(['get', 'post'], '/login', [UserController::class, 'login'])->name('login');

Route::middleware('auth')->group(function () {
    Route::post('/user/logout', [UserController::class, 'logout']);
    Route::match(['get', 'post'], '/user/change-password', [UserController::class, 'changePassword']);

    Route::middleware('role:superadmin,admin')->group(function () {
        Route::view('/', 'layout.admin');
        Route::get('dashboard', DashboardController::class);
        Route::prefix('user')->controller(UserController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('form/{id?}', 'form');
            Route::match(['post', 'put'], 'submit', 'submit');
            Route::delete('delete', 'delete');
        });

        // Table
        Route::prefix('table')->controller(TableController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('form/{id?}',  'form');
            Route::match(['post', 'put'], 'submit', 'submit');
            Route::delete('delete', 'delete');
        });

        // Product
        Route::prefix('product')->controller(ProductController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('form/{id?}',  'form');
            Route::match(['post', 'put'], 'submit', 'submit');
            Route::delete('delete', 'delete');
        });

        // Product Category
        Route::prefix('product-category')->controller(ProductCategoryController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('form/{id?}',  'form');
            Route::match(['post', 'put'], 'submit', 'submit');
            Route::delete('delete', 'delete');
        });

        // Balance Adjustment
        Route::prefix('balance-adjustment')->controller(BalanceAdjustmentController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('form/{id?}',  'form');
            Route::match(['post', 'put'], 'submit', 'submit');
            Route::delete('delete', 'delete');
        });

        // Report
        Route::prefix('report')->controller(ReportController::class)->group(function () {
            Route::get('sale-summary', 'saleSummary');
            Route::get('product-summary', 'productSummary');
            Route::get('sale-history', 'saleHistory');
            Route::get('export-product-summary', 'exportProductSummary');
            Route::get('export-sale-history', 'exportSaleHistory');
            Route::get('show-order-detail/{id}', 'showOrderDetail');
        });
    });
    Route::middleware('role:cashier')->prefix('cashier')->controller(CashierController::class)->group(function () {
        // Cashier
        Route::get('/', 'index');
        Route::get('product/{category}', 'product');
        Route::get('table/{id}',  'table');
        Route::post('select-table', 'selectTable');
        Route::post('update-order-qty', 'updateOrderQty');
        Route::post('update-detail-discount', 'updateDetailDiscount');
        Route::delete('delete-order-product', 'deleteOrderProduct');
        Route::post('update-discount', 'updateDiscount');
        Route::post('add-to-order', 'addToOrder');
        Route::post('print-invoice', 'printInvoice');
        Route::match(['post', 'get'], 'make-payment', 'makePayment');
    });
});

Route::fallback(function () {
    return view('404');
});
