<?php

use App\Http\Controllers\SeoController;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Route;

Route::get('/health', static fn (): JsonResponse => response()->json(['status' => 'ok']));

Route::view('/', 'home')->name('home');

Route::get('robots.txt', [SeoController::class, 'robots'])->name('seo.robots');
Route::get('sitemap.xml', [SeoController::class, 'sitemap'])->name('seo.sitemap');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
