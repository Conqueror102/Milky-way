<?php

use App\Livewire\Admin\Categories;
use App\Livewire\Admin\Orders;
use App\Livewire\Admin\Products;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'can:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::redirect('/', '/admin/products');

        Route::livewire('products', Products\Index::class)->name('products.index');
        Route::livewire('products/create', Products\Form::class)->name('products.create');
        Route::livewire('products/{product}/edit', Products\Form::class)->name('products.edit');

        Route::livewire('categories', Categories\Index::class)->name('categories.index');
        Route::livewire('categories/create', Categories\Form::class)->name('categories.create');
        Route::livewire('categories/{category}/edit', Categories\Form::class)->name('categories.edit');

        Route::livewire('orders', Orders\Index::class)->name('orders.index');
        Route::livewire('orders/{order}', Orders\Show::class)->name('orders.show');
    });
