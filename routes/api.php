<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\SubjectController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\BookmarkController;
use App\Http\Middleware\JWTMiddleware;

Route::middleware([JWTMiddleware::class])->group(function () 
{
    // Class routes
    Route::get('class', [CategoryController::class, 'index'])->name('category.index');
    Route::post('class', [CategoryController::class, 'store'])->name('category.store');
    Route::post('class/{id}', [CategoryController::class, 'update'])->name('category.update');
    Route::delete('class/{id}', [CategoryController::class, 'destroy'])->name('category.destroy');

    // Subject routes
    Route::get('subject', [SubjectController::class, 'index'])->name('subject.index');
    Route::get('subject-list-by-class-id/{category_id}', [SubjectController::class, 'subjectListByCategory'])->name('subject.subjectListByCategory');
    Route::post('subject/{id}', [SubjectController::class, 'update'])->name('subject.update');
    Route::delete('subject/{id}', [SubjectController::class, 'destroy'])->name('subject.destroy');
    Route::post('subject', [SubjectController::class, 'store'])->name('subject.store');

    // Chapter routes
    Route::get('chapter', [ChapterController::class, 'index'])->name('chapter.index');
    Route::get('chapter-list-by-subject-id/{subject_id}', [ChapterController::class, 'chapterListBySubject'])->name('chapter.chapterListBySubject');
    Route::post('chapter/{id}', [ChapterController::class, 'update'])->name('chapter.update');
    Route::delete('chapter/{id}', [ChapterController::class, 'destroy'])->name('chapter.destroy');
    Route::post('chapter', [ChapterController::class, 'store'])->name('chapter.store');

    // Content routes
    Route::get('content-list-by-chapter-id/{chapter_id}', [ContentController::class, 'contentListByChapter'])->name('content.contentListByChapter');
    // Route::post('chapter/{id}', [ContentController::class, 'update'])->name('content.update');
    Route::delete('content/{id}', [ContentController::class, 'destroy'])->name('content.destroy');
    Route::post('content-upload', [ContentController::class, 'store']);
    Route::get('content', [ContentController::class, 'index']);

    //Bookmark
    Route::post('add-remove-bookmark', [BookmarkController::class, 'store']);
    Route::get('bookmark-list', [BookmarkController::class, 'bookmarkList']);

});