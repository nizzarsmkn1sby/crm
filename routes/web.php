<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DealController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\AutomationWorkflowController;
use App\Http\Controllers\PipelineController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

// Auth routes (Breeze)
require __DIR__.'/auth.php';

// Email tracking (public)
Route::get('/email/track/{trackingId}', function ($trackingId) {
    app(\App\Services\EmailService::class)->trackOpen($trackingId);
    return response(base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'), 200)
        ->header('Content-Type', 'image/gif');
})->name('email.track-open');

// Authenticated routes
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Profile (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ─── Leads ────────────────────────────────────────────────────────────────
    Route::resource('leads', LeadController::class);
    Route::patch('/leads/{lead}/stage', [LeadController::class, 'updateStage'])->name('leads.update-stage');
    Route::post('/leads/{lead}/whatsapp', [LeadController::class, 'sendWhatsapp'])->name('leads.send-whatsapp');
    Route::post('/leads/{lead}/email', [LeadController::class, 'sendEmail'])->name('leads.send-email');
    Route::post('/leads/bulk', [LeadController::class, 'bulk'])->name('leads.bulk');

    // ─── Contacts ─────────────────────────────────────────────────────────────
    Route::resource('contacts', ContactController::class);

    // ─── Companies ────────────────────────────────────────────────────────────
    Route::resource('companies', CompanyController::class);

    // ─── Deals ────────────────────────────────────────────────────────────────
    Route::resource('deals', DealController::class);
    Route::patch('/deals/{deal}/stage', [DealController::class, 'updateStage'])->name('deals.update-stage');
    Route::post('/deals/{deal}/won', [DealController::class, 'markWon'])->name('deals.won');
    Route::post('/deals/{deal}/lost', [DealController::class, 'markLost'])->name('deals.lost');

    // ─── Pipeline (Kanban) ────────────────────────────────────────────────────
    Route::get('/pipeline', [PipelineController::class, 'index'])->name('pipeline.index');
    Route::get('/pipeline/leads', [PipelineController::class, 'leadsView'])->name('pipeline.leads');
    Route::post('/pipeline/reorder', [PipelineController::class, 'reorder'])->name('pipeline.reorder');
    Route::post('/pipeline/stages', [PipelineController::class, 'storeStage'])->name('pipeline.stages.store');
    Route::patch('/pipeline/stages/{stage}', [PipelineController::class, 'updateStage'])->name('pipeline.stages.update');
    Route::delete('/pipeline/stages/{stage}', [PipelineController::class, 'destroyStage'])->name('pipeline.stages.destroy');

    // ─── Activities ───────────────────────────────────────────────────────────
    Route::resource('activities', ActivityController::class)->only(['index', 'store', 'destroy']);

    // ─── Tasks ────────────────────────────────────────────────────────────────
    Route::resource('tasks', TaskController::class)->except(['create', 'show', 'edit']);
    Route::patch('/tasks/{task}/complete', [TaskController::class, 'complete'])->name('tasks.complete');

    // ─── Meetings / Calendar ──────────────────────────────────────────────────
    Route::resource('meetings', MeetingController::class)->except(['create', 'edit']);

    // ─── Documents ────────────────────────────────────────────────────────────
    Route::resource('documents', DocumentController::class)->only(['index', 'store', 'destroy']);
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');

    // ─── Campaigns ────────────────────────────────────────────────────────────
    Route::resource('campaigns', CampaignController::class);
    Route::post('/campaigns/{campaign}/send', [CampaignController::class, 'send'])->name('campaigns.send');

    // ─── Automation ───────────────────────────────────────────────────────────
    Route::resource('automation', AutomationWorkflowController::class);

    // ─── Reports ──────────────────────────────────────────────────────────────
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
    Route::get('/reports/leads', [ReportController::class, 'leads'])->name('reports.leads');

    // ─── Export (CSV + PDF) ───────────────────────────────────────────────────
    Route::get('/export/leads/csv', [ExportController::class, 'leadsExportCsv'])->name('export.leads.csv');
    Route::get('/export/leads/pdf', [ExportController::class, 'leadsExportPdf'])->name('export.leads.pdf');
    Route::get('/export/sales/csv', [ExportController::class, 'salesExportCsv'])->name('export.sales.csv');
    Route::get('/export/sales/pdf', [ExportController::class, 'salesExportPdf'])->name('export.sales.pdf');

    // ─── Global Search ────────────────────────────────────────────────────────
    Route::get('/search', [SearchController::class, 'index'])->name('search');
    Route::get('/search/quick', [SearchController::class, 'quick'])->name('search.quick');

    // ─── Notifications ────────────────────────────────────────────────────────
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');

    // ─── Settings ─────────────────────────────────────────────────────────────
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
});
