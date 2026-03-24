<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SearchController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('landing');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/admin/dashboard', DashboardController::class)
        ->name('admin.dashboard')
        ->middleware('permission:admin.access');

    Route::middleware('permission:admin.access')->group(function () {
        Route::get('/admin/search', SearchController::class)->name('admin.search');
    });
    
    // Rutas para clientes y proyectos
    Route::get('/admin/clients/markers', [App\Http\Controllers\Admin\ClientController::class, 'markers'])
        ->name('clients.markers')
        ->middleware('permission:clients.view');

    Route::resource('admin/clients', App\Http\Controllers\Admin\ClientController::class)
        ->names('clients');
    Route::resource('admin/projects', App\Http\Controllers\Admin\ProjectController::class)
        ->names('projects');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
