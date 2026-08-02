<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Report\ReportController;
use App\Http\Controllers\Table\RestaurantTableController;
use App\Http\Controllers\Ticket\TicketController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Waiter\WaiterController;
use App\Infrastructure\Persistence\Eloquent\Models\User;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'active'])->group(function (): void {
    Route::middleware('role:'.User::ROLE_ADMIN.','.User::ROLE_CAJA)->group(function (): void {
        Route::get('/', DashboardController::class)->name('dashboard');

        Route::resource('products', ProductController::class)->except(['show', 'destroy']);
        Route::patch('products/{product}/availability', [ProductController::class, 'availability'])
            ->whereNumber('product')
            ->name('products.availability');

        Route::resource('waiters', WaiterController::class)->except(['show', 'destroy']);
        Route::patch('waiters/{waiter}/availability', [WaiterController::class, 'availability'])
            ->whereNumber('waiter')
            ->name('waiters.availability');

        Route::get('reports/daily', [ReportController::class, 'daily'])->name('reports.daily');
        Route::get('reports/monthly', [ReportController::class, 'monthly'])->name('reports.monthly');
        Route::get('reports/daily-sales', [ReportController::class, 'dailySales'])->name('reports.daily-sales');
        Route::get('reports/sold-products', [ReportController::class, 'soldProducts'])->name('reports.sold-products');

        Route::get('tickets', [TicketController::class, 'index'])->name('tickets.index');
        Route::get('tickets/{order}', [TicketController::class, 'show'])
            ->whereNumber('order')
            ->name('tickets.show');
        Route::post('tickets/{order}/reprint', [TicketController::class, 'reprint'])
            ->whereNumber('order')
            ->name('tickets.reprint');
    });

    Route::middleware('role:'.User::ROLE_ADMIN)->group(function (): void {
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
        Route::get('users/{user}/edit', [UserController::class, 'edit'])->whereNumber('user')->name('users.edit');
        Route::match(['put', 'patch'], 'users/{user}', [UserController::class, 'update'])->whereNumber('user')->name('users.update');
        Route::patch('users/{user}/availability', [UserController::class, 'availability'])
            ->whereNumber('user')->name('users.availability');
        Route::patch('users/{user}/password', [UserController::class, 'password'])
            ->whereNumber('user')->name('users.password');
    });

    Route::middleware('role:'.User::ROLE_ADMIN.','.User::ROLE_CAJA.','.User::ROLE_MOZO)->group(function (): void {
        Route::get('tables', [RestaurantTableController::class, 'index'])->name('tables.index');
        Route::get('tables/{number}', [RestaurantTableController::class, 'show'])
            ->whereNumber('number')
            ->name('tables.show');
        Route::post('tables/{number}/products', [RestaurantTableController::class, 'addProduct'])
            ->whereNumber('number')
            ->name('tables.products.store');
        Route::post('tables/{number}/products/search', [RestaurantTableController::class, 'addProductByName'])
            ->whereNumber('number')
            ->name('tables.products.search');
        Route::post('tables/{number}/products/remove-unit', [RestaurantTableController::class, 'removeUnit'])
            ->whereNumber('number')
            ->name('tables.products.remove-unit');
        Route::patch('tables/{number}/products/quantity', [RestaurantTableController::class, 'updateQuantity'])
            ->whereNumber('number')
            ->name('tables.products.quantity');
        Route::delete('tables/{number}/products', [RestaurantTableController::class, 'removeProduct'])
            ->whereNumber('number')
            ->name('tables.products.destroy');
        Route::post('tables/{number}/close', [RestaurantTableController::class, 'close'])
            ->whereNumber('number')
            ->name('tables.close');
    });
});

require __DIR__.'/auth.php';
