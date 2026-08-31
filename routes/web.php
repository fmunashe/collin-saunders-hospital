<?php

use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Report PDF exports — protected by Nova authentication.
// Per-report permission checks are enforced inside the controller.
Route::middleware(['nova', config('nova.guard') ? 'auth:'.config('nova.guard') : 'auth'])
    ->get('/nova/reports/{report}/pdf', [ReportController::class, 'download'])
    ->name('reports.download');
