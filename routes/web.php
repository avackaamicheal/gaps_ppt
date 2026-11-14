<?php

use App\Http\Controllers\Admin\ActivityLogController as AdminActivityLogController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EnquiryController as AdminEnquiryController;
use App\Http\Controllers\Admin\InspectionController as AdminInspectionController;
use App\Http\Controllers\Admin\PropertyController as AdminPropertyController;
use App\Http\Controllers\Admin\PropertyDocumentController as AdminPropertyDocumentController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\TestimonialController as AdminTestimonialController;
use App\Http\Controllers\Public\EnquiryController as PublicEnquiryController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\PropertyController as PublicPropertyController;
use App\Http\Controllers\Public\SitemapController as PublicSitemapController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/properties', [PublicPropertyController::class, 'index'])->name('properties.index');
Route::get('/properties/{slug}', [PublicPropertyController::class, 'show'])->name('properties.show');
Route::get('/properties/{slug}/documents/{document}', [PublicPropertyController::class, 'downloadDocument'])->name('properties.documents.download');
Route::post('/enquiries', [PublicEnquiryController::class, 'store'])->name('enquiries.store');
Route::get('/sitemap.xml', [PublicSitemapController::class, 'index'])->name('sitemap');

// Admin Guest Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login']);
    });

    // Admin Authenticated Routes
    Route::middleware('auth')->group(function () {
        Route::get('/', function () {
            return redirect()->route('admin.dashboard');
        });
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

        // Property Management
        Route::resource('properties', AdminPropertyController::class);
        Route::patch('properties/{property}/status', [AdminPropertyController::class, 'updateStatus'])->name('properties.status');
        Route::delete('properties/{property}/images/{image}', [AdminPropertyController::class, 'deleteImage'])->name('properties.images.destroy');
        Route::post('properties/{property}/images/{image}/cover', [AdminPropertyController::class, 'setCoverImage'])->name('properties.images.cover');

        // Property Documents Management
        Route::get('properties/{property}/documents/{document}/download', [AdminPropertyDocumentController::class, 'download'])->name('properties.documents.download');
        Route::delete('properties/{property}/documents/{document}', [AdminPropertyDocumentController::class, 'destroy'])->name('properties.documents.destroy');

        // Phase 3: Enquiries Management & Offline Leads
        Route::resource('enquiries', AdminEnquiryController::class)->except(['edit']);

        // Phase 3: Customer Relationship Management (CRM)
        Route::resource('customers', AdminCustomerController::class);

        // Phase 3: Physical Site Inspections Scheduling
        Route::resource('inspections', AdminInspectionController::class);

        // Phase 4: Client Testimonials Management
        Route::resource('testimonials', AdminTestimonialController::class);
        Route::patch('testimonials/{testimonial}/toggle-publish', [AdminTestimonialController::class, 'togglePublish'])->name('testimonials.toggle-publish');

        // Phase 4: Website Settings & Agent Branding
        Route::get('settings', [AdminSettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [AdminSettingController::class, 'update'])->name('settings.update');

        // Phase 4: Administrative Activity Logs
        Route::get('activity-logs', [AdminActivityLogController::class, 'index'])->name('activity-logs.index');
    });
});
