<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SeoResultController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\EnsureWhatsAppIsVerified;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Auth routes
Auth::routes();

// WhatsApp Verification Routes
Route::get('/verify', [VerificationController::class, 'show'])->name('verification.notice');
Route::post('/verify', [VerificationController::class, 'verify'])->name('verification.verify');
Route::post('/verify/resend', [VerificationController::class, 'resend'])->name('verification.resend');

// Dashboard and protected routes (requires WhatsApp verification)
Route::middleware([EnsureWhatsAppIsVerified::class])->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Content routes
    Route::resource('contents', ContentController::class);

    // SEO Results routes
    Route::get('seo-results', [SeoResultController::class, 'index'])->name('seo-results.index');
    Route::get('seo-results/{seoResult}', [SeoResultController::class, 'show'])->name('seo-results.show');
    Route::post('seo-results/reanalyze/{content}', [SeoResultController::class, 'reanalyze'])->name('seo-results.reanalyze');
    
    // Profile management routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update-name', [ProfileController::class, 'updateName'])->name('profile.update.name');
    Route::post('/profile/update-email', [ProfileController::class, 'updateEmail'])->name('profile.update.email');
    Route::post('/profile/update-phone', [ProfileController::class, 'updatePhone'])->name('profile.update.phone');
    Route::post('/profile/update-password', [ProfileController::class, 'updatePassword'])->name('profile.update.password');
    Route::post('/profile/request-verification', [ProfileController::class, 'requestVerificationCode'])->name('profile.verification.request');
});

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// API routes for real-time analysis
Route::post('api/analyze-content', [ApiController::class, 'analyzeContent'])->name('api.analyze-content');
Route::post('api/suggest-title', [App\Http\Controllers\Api\AiSuggestionController::class, 'suggestTitle'])->name('api.suggest-title');
Route::post('api/suggest-meta-description', [App\Http\Controllers\Api\AiSuggestionController::class, 'suggestMetaDescription'])->name('api.suggest-meta-description');
