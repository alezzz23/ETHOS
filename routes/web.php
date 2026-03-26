<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SearchController;
use App\Http\Controllers\LandingAssistantController;
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

Route::post('/assistant/chat', LandingAssistantController::class)
    ->name('assistant.chat')
    ->middleware('throttle:30,1');

Route::post('/assistant/chat/clear', [LandingAssistantController::class, 'clearHistory'])
    ->name('assistant.chat.clear')
    ->middleware('throttle:30,1');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/admin/dashboard', DashboardController::class)
        ->name('admin.dashboard')
        ->middleware('permission:admin.access');

    Route::middleware('permission:admin.access')->group(function () {
        Route::get('/admin/search', SearchController::class)->name('admin.search');

        // Admin AI Chatbot
        Route::post('/admin/chat',         [App\Http\Controllers\Admin\DashboardChatController::class, 'chat'])->name('admin.chat')->middleware('throttle:40,1');
        Route::post('/admin/chat/clear',   [App\Http\Controllers\Admin\DashboardChatController::class, 'clearHistory'])->name('admin.chat.clear');
        Route::get ('/admin/chat/audit',   [App\Http\Controllers\Admin\DashboardChatController::class, 'auditLog'])->name('admin.chat.audit');
    });
    
    // Rutas para clientes y proyectos
    Route::get('/admin/clients/markers', [App\Http\Controllers\Admin\ClientController::class, 'markers'])
        ->name('clients.markers')
        ->middleware('permission:clients.view');

    Route::resource('admin/clients', App\Http\Controllers\Admin\ClientController::class)
        ->names('clients');
    Route::resource('admin/projects', App\Http\Controllers\Admin\ProjectController::class)
        ->names('projects');
    Route::resource('admin/users', App\Http\Controllers\Admin\UserController::class)
        ->names('users')
        ->only(['index', 'store', 'update', 'destroy']);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::post('/profile/preferences', [ProfileController::class, 'updatePreferences'])->name('profile.preferences');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
