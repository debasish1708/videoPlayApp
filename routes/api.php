<?php

use App\Http\Controllers\Admin\CustomerSupportController;
use App\Http\Controllers\Api\ExploreController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\VideoSearchController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/home',HomeController::class)
    ->name('api.home');

Route::get('/explore/{genre?}', ExploreController::class)
    ->name('api.explore');

// Route::get('/videos/search',VideoSearchController::class)
//     ->name('api.search.videos');

Route::post('/customer-support',[CustomerSupportController::class, 'store'])
    ->name('api.customer-support.store');
