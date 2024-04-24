<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\CategoryController;
use App\Http\Middleware\JWTMiddleware;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::middleware([JWTMiddleware::class])->group(function () {
    Route::get('class', [CategoryController::class, 'index'])->name('category.index');
    Route::post('class', [CategoryController::class, 'store'])->name('category.store');
});