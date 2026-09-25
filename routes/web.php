<?php

use App\Http\Controllers\SeoController;
use App\Livewire\Shop\CartPage;
use App\Livewire\Shop\Checkout;
use App\Livewire\Shop\OrderConfirmation;
use App\Livewire\Shop\ProductPage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Route;

Route::get('/health', static fn (): JsonResponse => response()->json(['status' => 'ok']));

Route::view('/', 'home')->name('home');

Route::livewire('products/{product}', ProductPage::class)->name('products.show');
Route::livewire('cart', CartPage::class)->name('cart');
Route::livewire('checkout', Checkout::class)->name('checkout');
Route::livewire('orders/{order}', OrderConfirmation::class)->name('orders.show');

Route::get('robots.txt', [SeoController::class, 'robots'])->name('seo.robots');
Route::get('sitemap.xml', [SeoController::class, 'sitemap'])->name('seo.sitemap');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
