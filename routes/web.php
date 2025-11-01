<?php

use Illuminate\Support\Facades\Route;
use Modules\Customer\Http\Controllers\CustomerAuthController;
use Modules\Customer\Http\Controllers\CustomerDashboardController;
use Modules\Customer\Http\Controllers\CustomerEmailVerificationController;
use Modules\Customer\Http\Controllers\CustomerProductController;
use Modules\Order\Http\Controllers\AdminOrderController;
use Modules\Order\Http\Controllers\CartController;
use Modules\Order\Http\Controllers\OrderController;
use Modules\Prescription\Http\Controllers\AdminPrescriptionController;
use Modules\Prescription\Http\Controllers\PrescriptionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('auth.login');
})->middleware('guest');

Auth::routes(['register' => false]);

Route::group(['middleware' => 'auth'], function () {
    Route::get('/home', 'HomeController@index')
        ->name('home');

    Route::get('/sales-purchases/chart-data', 'HomeController@salesPurchasesChart')
        ->name('sales-purchases.chart');

    Route::get('/current-month/chart-data', 'HomeController@currentMonthChart')
        ->name('current-month.chart');

    Route::get('/payment-flow/chart-data', 'HomeController@paymentChart')
        ->name('payment-flow.chart');
});


// Customer Authentication Routes (Guest)
Route::prefix('customer')->name('customer.')->group(function () {
    Route::middleware('guest:customer')->group(function () {
        Route::get('/register', [CustomerAuthController::class, 'showRegisterForm'])->name('register');
        Route::post('/register', [CustomerAuthController::class, 'register']);
        Route::get('/login', [CustomerAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [CustomerAuthController::class, 'login']);
    });

    // Customer Protected Routes
    Route::middleware(['auth:customer'])->group(function () {
        Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('logout');

        // Email Verification
        Route::get('/email/verify', [CustomerEmailVerificationController::class, 'notice'])
            ->name('verification.notice');
        Route::get('/email/verify/{id}/{hash}', [CustomerEmailVerificationController::class, 'verify'])
            ->middleware('signed')
            ->name('verification.verify');
        Route::post('/email/verification-notification', [CustomerEmailVerificationController::class, 'resend'])
            ->middleware('throttle:6,1')
            ->name('verification.send');

        // Verified Email Required Routes
        Route::middleware('verified.customer:customer')->group(function () {
            // Dashboard
            Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');

            // Prescriptions
            Route::prefix('prescriptions')->name('prescriptions.')->group(function () {
                Route::get('/', [PrescriptionController::class, 'index'])->name('index');
                Route::get('/create', [PrescriptionController::class, 'create'])->name('create');
                Route::post('/', [PrescriptionController::class, 'store'])->name('store');
                Route::get('/{prescription}', [PrescriptionController::class, 'show'])->name('show');
            });

            // Products (OTC browsing)
            Route::prefix('products')->name('products.')->group(function () {
                Route::get('/', [CustomerProductController::class, 'index'])->name('index');
                Route::get('/{product}', [CustomerProductController::class, 'show'])->name('show');
            });

            // Cart
            Route::prefix('cart')->name('cart.')->group(function () {
                Route::get('/', [CartController::class, 'index'])->name('index');
                Route::post('/add/{product}', [CartController::class, 'add'])->name('add');
                Route::patch('/{cartItem}', [CartController::class, 'update'])->name('update');
                Route::delete('/{cartItem}', [CartController::class, 'remove'])->name('remove');
                Route::delete('/', [CartController::class, 'clear'])->name('clear');
            });

            // Orders
            Route::prefix('orders')->name('orders.')->group(function () {
                Route::get('/', [OrderController::class, 'index'])->name('index');
                Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
                Route::post('/place-order', [OrderController::class, 'placeOrder'])->name('place');
                Route::get('/{order}', [OrderController::class, 'show'])->name('show');
            });
        });
    });
});

// Admin Routes for Customer Portal Management
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    // Customer Orders Management
    Route::prefix('customer-orders')->name('customer-orders.')->group(function () {
        Route::get('/', [AdminOrderController::class, 'index'])->name('index');
        Route::get('/{order}', [AdminOrderController::class, 'show'])->name('show');
        Route::patch('/{order}/update-status', [AdminOrderController::class, 'updateStatus'])->name('update-status');
    });

    // Prescriptions Management
    Route::prefix('prescriptions')->name('prescriptions.')->group(function () {
        Route::get('/', [AdminPrescriptionController::class, 'index'])->name('index');
        Route::get('/{prescription}', [AdminPrescriptionController::class, 'show'])->name('show');
        Route::patch('/{prescription}/status', [AdminPrescriptionController::class, 'updateStatus'])->name('update-status');
        Route::patch('/{prescription}/notes', [AdminPrescriptionController::class, 'updateNotes'])->name('update-notes');
        Route::get('/file/{file}/download', [AdminPrescriptionController::class, 'downloadFile'])->name('download-file');
        Route::get('/export/all', [AdminPrescriptionController::class, 'export'])->name('export');
    });
});
