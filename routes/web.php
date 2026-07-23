<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Report\ReportController;
use App\Http\Controllers\Table\RestaurantTableController;
use App\Http\Controllers\Ticket\TicketController;
use Illuminate\Support\Facades\Route;

Route::get('/', DashboardController::class)->name('dashboard');

Route::resource('products', ProductController::class)->except(['show']);

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
