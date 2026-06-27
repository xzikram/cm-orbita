<?php

use App\Modules\Auth\Controllers\LoginController;
use App\Modules\Dashboard\Controllers\DashboardController;
use App\Modules\FollowUp\Controllers\ExaminationController;
use App\Modules\FollowUp\Controllers\FollowUpScheduleController;
use App\Modules\FollowUp\Controllers\PatientController;
use App\Modules\MasterData\Controllers\AuditLogController;
use App\Modules\MasterData\Controllers\DoctorController;
use App\Modules\FollowUp\Controllers\ReminderMonitorController;
use Illuminate\Support\Facades\Route;

// ── Public Routes ──
Route::get('/', fn() => redirect()->route('login'));

// ── Public Verification Route (DPC) ──
Route::get('/verify/{uuid}', [\App\Modules\Document\Controllers\DocumentVerificationController::class, 'verify'])->name('dpc.verify');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
});

// ── Authenticated Routes ──
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard')
        ->middleware('permission:dashboard.view');

    // Profile Settings
    Route::get('/profile', [\App\Modules\Auth\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/password', [\App\Modules\Auth\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // ── Master Data ──
    Route::prefix('master-data')->name('master-data.')->middleware('permission:doctors.view')->group(function () {
        Route::post('doctors/delete-all', [DoctorController::class, 'deleteAll'])->name('doctors.deleteAll');
        Route::resource('clinics', \App\Modules\MasterData\Controllers\ClinicController::class)->except('show');
        Route::resource('doctors', DoctorController::class)->except('show');
    });

    // ── Follow-Up Module ──
    Route::prefix('follow-up')->name('follow-up.')->group(function () {
        // Patients
        Route::post('patients/delete-all', [PatientController::class, 'deleteAll'])
            ->name('patients.deleteAll')
            ->middleware('permission:patients.view');
        Route::resource('patients', PatientController::class)
            ->middleware('permission:patients.view');

        // Examinations
        Route::resource('examinations', ExaminationController::class)
            ->only(['index', 'create', 'store', 'show'])
            ->middleware('permission:examinations.view');

        // Schedules
        Route::get('schedules', [FollowUpScheduleController::class, 'index'])
            ->name('schedules.index')
            ->middleware('permission:follow-up.view');

        Route::get('schedules/{schedule}/record', [FollowUpScheduleController::class, 'recordVisit'])
            ->name('schedules.record')
            ->middleware('permission:follow-up.record-visit');

        Route::post('schedules/{schedule}/record', [FollowUpScheduleController::class, 'storeVisit'])
            ->name('schedules.store-visit')
            ->middleware('permission:follow-up.record-visit');
    });

    // ── Reminders ──
    Route::prefix('reminders')->name('reminders.')->middleware('permission:reminders.view')->group(function () {
        Route::get('/', [ReminderMonitorController::class, 'index'])->name('index');
        Route::get('/logs', [ReminderMonitorController::class, 'logs'])->name('logs');
    });

    // ── Communication Center (CCFP) ──
    Route::prefix('communication')->name('communication.')->group(function () {
        Route::resource('email-accounts', \App\Modules\Communication\Controllers\EmailAccountController::class)
            ->middleware('permission:communication.email-accounts.manage');
        
        Route::middleware('permission:communication.email-templates.manage')->group(function () {
            Route::post('email-templates/delete-all', [\App\Modules\Communication\Controllers\EmailTemplateController::class, 'deleteAll'])->name('email-templates.deleteAll');
            Route::resource('email-templates', \App\Modules\Communication\Controllers\EmailTemplateController::class);
        });
        
        Route::resource('whatsapp-templates', \App\Modules\Communication\Controllers\WhatsAppTemplateController::class)
            ->middleware('permission:communication.whatsapp-templates.manage');
        
        Route::middleware('permission:communication.document-types.manage')->group(function () {
            Route::post('document-types/delete-all', [\App\Modules\Communication\Controllers\DocumentTypeController::class, 'deleteAll'])->name('document-types.deleteAll');
            Route::resource('document-types', \App\Modules\Communication\Controllers\DocumentTypeController::class);
        });
        
        Route::get('whatsapp/status', [\App\Modules\Communication\Controllers\DocumentDeliveryController::class, 'whatsappStatus'])
            ->name('whatsapp.status')
            ->middleware('permission:communication.whatsapp.manage');

        Route::get('whatsapp/check-connection', [\App\Modules\Communication\Controllers\DocumentDeliveryController::class, 'checkWhatsAppConnection'])
            ->name('whatsapp.checkConnection');
        
        Route::middleware('permission:communication.deliveries.manage')->group(function () {
            Route::post('deliveries/{delivery}/mark-as-sent', [\App\Modules\Communication\Controllers\DocumentDeliveryController::class, 'markAsSent'])->name('deliveries.markAsSent');
            Route::resource('deliveries', \App\Modules\Communication\Controllers\DocumentDeliveryController::class)->only(['index', 'create', 'store', 'show']);
        });
    });

    // ── Document Processing Center (DPC) ──
    Route::prefix('dpc')->name('dpc.')->group(function () {
        Route::post('templates/delete-all', [\App\Modules\Document\Controllers\DocumentTemplateController::class, 'deleteAll'])->name('templates.deleteAll');
        Route::resource('templates', \App\Modules\Document\Controllers\DocumentTemplateController::class);

        Route::post('processing/delete-all', [\App\Modules\Document\Controllers\DocumentProcessingController::class, 'deleteAll'])->name('processing.deleteAll');
        Route::resource('processing', \App\Modules\Document\Controllers\DocumentProcessingController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
    });

    // ── Audit Logs ──
    Route::get('/audit-logs', [AuditLogController::class, 'index'])
        ->name('audit.index')
        ->middleware('permission:audit.view');

    // ── Administration (User & Access Management) ──
    Route::prefix('administration')->name('administration.')->group(function () {
        Route::resource('users', \App\Modules\MasterData\Controllers\UserController::class)->except('show');
        Route::resource('roles', \App\Modules\MasterData\Controllers\RoleController::class)->except('show');
    });
});

