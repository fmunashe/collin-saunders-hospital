<?php

use App\Http\Controllers\DocsController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Report PDF exports.
//
// We authenticate with Nova's own Authenticate middleware (so the Nova login
// session is recognised) but deliberately DO NOT include Nova's Inertia handler
// (which would convert this non-Inertia GET into an SPA page load/redirect and
// swallow the binary PDF response).
//
// Two paths are exposed:
//   /nova/reports/{report}/pdf  — canonical, used by the in-app menu links
//   /reports/{report}/pdf       — a twin OUTSIDE the Nova SPA namespace so a
//                                 browser navigation / E2E download is never
//                                 intercepted by Nova's client-side router.
// Per-report permission checks are enforced inside the controller.
Route::middleware(['web', \Laravel\Nova\Http\Middleware\Authenticate::class])
    ->group(function () {
        Route::get('/nova/reports/{report}/pdf', [ReportController::class, 'download'])
            ->name('reports.download');

        Route::get('/reports/{report}/pdf', [ReportController::class, 'download'])
            ->name('reports.download.direct');

        // Documentation portal — served from the (non-public) /docs folder and
        // gated behind Nova authentication so only logged-in staff can view it.
        // Markdown files are rendered to themed HTML so index links navigate to
        // readable pages instead of raw .md content.
        Route::get('/docs', [DocsController::class, 'index'])->name('docs.index');
        Route::get('/docs/assets/{file}', [DocsController::class, 'asset'])->name('docs.asset');
        Route::get('/docs/{file}', [DocsController::class, 'show'])->name('docs.show');
    });
