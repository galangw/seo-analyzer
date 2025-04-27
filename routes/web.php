<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SeoResultController;
use App\Http\Controllers\ApiController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Auth routes
Auth::routes();

// Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Content routes
Route::resource('contents', ContentController::class);

// SEO Results routes
Route::get('seo-results', [SeoResultController::class, 'index'])->name('seo-results.index');
Route::get('seo-results/{seoResult}', [SeoResultController::class, 'show'])->name('seo-results.show');
Route::post('seo-results/reanalyze/{content}', [SeoResultController::class, 'reanalyze'])->name('seo-results.reanalyze');

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// API routes for real-time analysis
Route::post('api/analyze-content', [ApiController::class, 'analyzeContent'])->name('api.analyze-content');
