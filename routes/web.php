<?php

use App\Http\Controllers\Admin\ChecklistController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KnowledgeBaseController;
use App\Http\Controllers\Admin\PortalTokenController;
use App\Http\Controllers\Admin\ProposalController;
use App\Http\Controllers\Admin\SearchController;
use App\Http\Controllers\ClientPortalController;
use App\Http\Controllers\LandingAssistantController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\RestrictedTopicController;
use App\Http\Controllers\SurveyController;
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

    // ─── Project lifecycle endpoints ──────────────────────────────
    Route::patch('admin/projects/{project}/analyze', [App\Http\Controllers\Admin\ProjectController::class, 'analyze'])
        ->name('projects.analyze');
    Route::patch('admin/projects/{project}/approve', [App\Http\Controllers\Admin\ProjectController::class, 'approve'])
        ->name('projects.approve');
    Route::patch('admin/projects/{project}/start-execution', [App\Http\Controllers\Admin\ProjectController::class, 'startExecution'])
        ->name('projects.start-execution');
    Route::post('admin/projects/{project}/progress', [App\Http\Controllers\Admin\ProjectController::class, 'logProgress'])
        ->name('projects.progress');
    Route::patch('admin/projects/{project}/close', [App\Http\Controllers\Admin\ProjectController::class, 'close'])
        ->name('projects.close');
    Route::get('admin/projects/{project}/report', [App\Http\Controllers\Admin\ProjectController::class, 'progressReport'])
        ->name('projects.report')
        ->middleware('permission:projects.view');

    Route::resource('admin/users', App\Http\Controllers\Admin\UserController::class)
        ->names('users')
        ->only(['index', 'store', 'update', 'destroy']);

    // ─── Services (Module 1) ──────────────────────────────────────
    Route::resource('admin/services', ServiceController::class)
        ->names('services')
        ->only(['index', 'show', 'store', 'update']);
    Route::patch('admin/services/{service}/toggle-status', [ServiceController::class, 'toggleStatus'])
        ->name('services.toggle-status')
        ->middleware('permission:services.deactivate');
    Route::post('admin/services/{service}/calculate', [ServiceController::class, 'calculate'])
        ->name('services.calculate')
        ->middleware('permission:services.view');

    // ─── Restricted Topics (Module 8) ─────────────────────────────
    Route::resource('admin/restricted-topics', RestrictedTopicController::class)
        ->names('restricted-topics')
        ->only(['index', 'store', 'update', 'destroy']);

    // ─── Proposals (Module 4) ─────────────────────────────────────
    Route::resource('admin/proposals', ProposalController::class)
        ->names('proposals')
        ->only(['index', 'show', 'create', 'store', 'update']);
    Route::post('admin/proposals/{proposal}/generate-pdf', [ProposalController::class, 'generatePdf'])
        ->name('proposals.generate-pdf')
        ->middleware('permission:proposals.view');
    Route::patch('admin/proposals/{proposal}/send', [ProposalController::class, 'send'])
        ->name('proposals.send')
        ->middleware('permission:proposals.edit');
    Route::patch('admin/proposals/{proposal}/approve', [ProposalController::class, 'approve'])
        ->name('proposals.approve')
        ->middleware('permission:proposals.approve');
    Route::patch('admin/proposals/{proposal}/reject', [ProposalController::class, 'reject'])
        ->name('proposals.reject')
        ->middleware('permission:proposals.approve');

    // ─── Checklists (Module 6) ────────────────────────────────────
    Route::resource('admin/checklists', ChecklistController::class)
        ->names('checklists')
        ->only(['index', 'show']);
    Route::patch('admin/checklist-items/{item}/complete', [ChecklistController::class, 'completeItem'])
        ->name('checklist-items.complete')
        ->middleware('permission:proposals.edit');
    Route::patch('admin/checklist-items/{item}/assign', [ChecklistController::class, 'assignItem'])
        ->name('checklist-items.assign')
        ->middleware('permission:proposals.edit');

    // ─── Portal Tokens (Module 7) ─────────────────────────────────
    Route::post('admin/projects/{project}/portal-token', [PortalTokenController::class, 'store'])
        ->name('portal-tokens.store');
    Route::patch('admin/portal-tokens/{token}/revoke', [PortalTokenController::class, 'revoke'])
        ->name('portal-tokens.revoke');

    // ─── Knowledge Base & NPS Dashboard (Modules 9+10) ────────────
    Route::get('admin/knowledge-base', [KnowledgeBaseController::class, 'dashboard'])
        ->name('knowledge-base.dashboard');
    Route::post('admin/knowledge-base', [KnowledgeBaseController::class, 'store'])
        ->name('knowledge-base.store');
    Route::put('admin/knowledge-base/{entry}', [KnowledgeBaseController::class, 'update'])
        ->name('knowledge-base.update');
    Route::delete('admin/knowledge-base/{entry}', [KnowledgeBaseController::class, 'destroy'])
        ->name('knowledge-base.destroy');

    // ─── Chat Feedback (Module 10) ─────────────────────────────────
    Route::post('/admin/chat/feedback', [App\Http\Controllers\Admin\DashboardChatController::class, 'feedback'])
        ->name('admin.chat.feedback')
        ->middleware('throttle:20,1');
});

// ─── Client Portal (public, token-gated) ──────────────────────────
Route::get('/portal/{token}', [ClientPortalController::class, 'show'])
    ->name('client.portal')
    ->middleware('throttle:60,1');

// ─── Satisfaction Surveys (public, token-gated) ────────────────────
Route::get('/survey/{token}', [SurveyController::class, 'show'])
    ->name('survey.show')
    ->middleware('throttle:10,1');
Route::post('/survey/{token}', [SurveyController::class, 'store'])
    ->name('survey.store')
    ->middleware('throttle:5,1');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::post('/profile/preferences', [ProfileController::class, 'updatePreferences'])->name('profile.preferences');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
