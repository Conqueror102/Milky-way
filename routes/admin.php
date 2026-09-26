<?php

use App\Livewire\Admin\Categories;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Orders;
use App\Livewire\Admin\Products;
use App\Livewire\Admin\Site;
use App\Livewire\Admin\Transactions;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'can:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::livewire('/', Dashboard::class)->name('dashboard');

        Route::livewire('products', Products\Index::class)->name('products.index');
        Route::livewire('products/create', Products\Form::class)->name('products.create');
        Route::livewire('products/{product}/edit', Products\Form::class)->name('products.edit');

        Route::livewire('categories', Categories\Index::class)->name('categories.index');
        Route::livewire('categories/create', Categories\Form::class)->name('categories.create');
        Route::livewire('categories/{category}/edit', Categories\Form::class)->name('categories.edit');

        Route::livewire('orders', Orders\Index::class)->name('orders.index');
        Route::livewire('orders/{order}', Orders\Show::class)->name('orders.show');

        Route::livewire('transactions', Transactions\Index::class)->name('transactions.index');

        Route::livewire('site', Site\Index::class)->name('site.index');
        Route::livewire('site/{section}', Site\Edit::class)->name('site.edit');
    });
