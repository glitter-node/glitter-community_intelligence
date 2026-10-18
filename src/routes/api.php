<?php

use Illuminate\Support\Facades\Route;
use Modules\Glitter\CommunityIntelligence\Http\Controllers\Admin\IntelligenceController;

Route::prefix('admin')->middleware(['auth:sanctum'])->name('admin.')->group(function (): void {
    Route::middleware('permission:admin,glitter-community_intelligence.intelligence.view')->group(function (): void {
        Route::get('/briefing', [IntelligenceController::class, 'briefing'])->name('briefing');
        Route::get('/signals', [IntelligenceController::class, 'signals'])->name('signals.index');
        Route::get('/signals/{id}/summary', [IntelligenceController::class, 'signalSummary'])->whereNumber('id')->name('signals.summary');
        Route::get('/incidents', [IntelligenceController::class, 'incidents'])->name('incidents.index');
        Route::get('/incidents/{id}', [IntelligenceController::class, 'incident'])->whereNumber('id')->name('incidents.show');
    });
    Route::get('/signals/{id}', [IntelligenceController::class, 'signal'])->whereNumber('id')->middleware('permission:admin,glitter-community_intelligence.intelligence.view_sensitive')->name('signals.show');
    Route::post('/signals/{id}/reviews', [IntelligenceController::class, 'review'])->whereNumber('id')->middleware('permission:admin,glitter-community_intelligence.intelligence.review')->name('signals.reviews.store');
    Route::get('/rules', [IntelligenceController::class, 'rules'])->middleware('permission:admin,glitter-community_intelligence.intelligence.configure')->name('rules.index');
    Route::put('/rules/{id}', [IntelligenceController::class, 'updateRule'])->whereNumber('id')->middleware('permission:admin,glitter-community_intelligence.intelligence.configure')->name('rules.update');
    Route::get('/access-logs', [IntelligenceController::class, 'audits'])->middleware('permission:admin,glitter-community_intelligence.intelligence.audit')->name('access-logs.index');
});
