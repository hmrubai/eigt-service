<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\CategoryController;
use App\Http\Middleware\JWTMiddleware;

Route::middleware([JWTMiddleware::class])->group(function () {
    Route::get('class', [CategoryController::class, 'index'])->name('category.index');
    Route::post('class', [CategoryController::class, 'store'])->name('category.store');
    Route::post('class/{id}', [CategoryController::class, 'update'])->name('category.update');
});