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

// ── Public Event Registration ──
Route::get('/e/{code}', [\App\Modules\FollowUp\Controllers\EventController::class, 'registerForm'])->name('events.register');
Route::post('/e/{code}', [\App\Modules\FollowUp\Controllers\EventController::class, 'registerSubmit'])->name('events.register.submit');
Route::get('/e/{code}/ticket/{patient}', [\App\Modules\FollowUp\Controllers\EventController::class, 'ticket'])->name('events.ticket');

// ── Public Campaign Links ──
Route::get('/promo/{code}', [\App\Modules\FollowUp\Controllers\CampaignController::class, 'trackAndRedirect'])->name('campaign.track');
Route::post('/promo/{code}/register', [\App\Modules\FollowUp\Controllers\CampaignController::class, 'registerSubmit'])->name('campaign.register.submit');
Route::get('/promo/{code}/success/{patient}', [\App\Modules\FollowUp\Controllers\CampaignController::class, 'success'])->name('campaign.success');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
});

// ── Authenticated Routes ──
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Admission Scanner (Mobile)
    Route::get('/admission/scan', [\App\Modules\FollowUp\Controllers\AdmissionController::class, 'scanView'])->name('admission.scan');
    Route::post('/admission/check-in', [\App\Modules\FollowUp\Controllers\AdmissionController::class, 'checkIn'])->name('admission.check-in');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard')
        ->middleware('permission:dashboard.view');

    // Profile Settings
    Route::get('/profile', [\App\Modules\Auth\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/password', [\App\Modules\Auth\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // ── Master Data ──
    Route::prefix('master-data')->name('master-data.')->middleware('permission:doctors.view')->group(function () {
        Route::get('doctors/import', [DoctorController::class, 'showImportForm'])->name('doctors.import');
        Route::post('doctors/import', [DoctorController::class, 'importMapping'])->name('doctors.store-import');
        Route::post('doctors/delete-all', [DoctorController::class, 'deleteAll'])->name('doctors.deleteAll');
        Route::resource('clinics', \App\Modules\MasterData\Controllers\ClinicController::class)->except('show');
        Route::resource('doctors', DoctorController::class)->except('show');
    });

    // ── Follow-Up Module ──
    Route::prefix('follow-up')->name('follow-up.')->group(function () {
        // Patients Export & Import Mapping
        Route::get('patients/export-csv', [PatientController::class, 'exportCsv'])
            ->name('patients.export-csv')
            ->middleware('permission:patients.view');
        Route::get('patients/import-mapping', [PatientController::class, 'showImportForm'])
            ->name('patients.import-mapping')
            ->middleware('permission:patients.view');
        Route::post('patients/import-mapping', [PatientController::class, 'importNewMrMapping'])
            ->name('patients.store-import-mapping')
            ->middleware('permission:patients.view');
        Route::put('patients/{patient}/quick-update-rm', [PatientController::class, 'quickUpdateRm'])
            ->name('patients.quick-update-rm')
            ->middleware('permission:patients.view');

        Route::post('patients/delete-all', [PatientController::class, 'deleteAll'])
            ->name('patients.deleteAll')
            ->middleware('permission:patients.view');
        Route::resource('patients', PatientController::class)
            ->middleware('permission:patients.view');

        // Examinations Export & Import
        Route::get('examinations/export-csv', [ExaminationController::class, 'exportCsv'])
            ->name('examinations.export-csv')
            ->middleware('permission:examinations.view');
        Route::get('examinations/import', [ExaminationController::class, 'showImportForm'])
            ->name('examinations.import')
            ->middleware('permission:examinations.view');
        Route::post('examinations/import', [ExaminationController::class, 'importMapping'])
            ->name('examinations.store-import')
            ->middleware('permission:examinations.view');
        Route::get('examinations/create-downtime', [ExaminationController::class, 'createDowntime'])
            ->name('examinations.create-downtime')
            ->middleware('permission:examinations.view');
        Route::post('examinations/store-downtime', [ExaminationController::class, 'storeDowntime'])
            ->name('examinations.store-downtime')
            ->middleware('permission:examinations.view');
        Route::resource('examinations', ExaminationController::class)
            ->only(['index', 'create', 'store', 'show', 'destroy'])
            ->middleware('permission:examinations.view');

        // Schedules
        Route::get('schedules', [FollowUpScheduleController::class, 'index'])
            ->name('schedules.index')
            ->middleware('permission:follow-up.view');

        Route::post('schedules/{schedule}/send-reminder', [FollowUpScheduleController::class, 'sendReminder'])
            ->name('schedules.send-reminder')
            ->middleware('permission:follow-up.view');

        Route::get('schedules/{schedule}/record', [FollowUpScheduleController::class, 'recordVisit'])
            ->name('schedules.record')
            ->middleware('permission:follow-up.record-visit');

        Route::post('schedules/{schedule}/record', [FollowUpScheduleController::class, 'storeVisit'])
            ->name('schedules.store-visit')
            ->middleware('permission:follow-up.record-visit');

        // Events
        Route::get('events/export/all', [\App\Modules\FollowUp\Controllers\EventController::class, 'exportAllExcel'])
            ->name('events.export-all')
            ->middleware('permission:patients.view');
        Route::get('events/{event}/export', [\App\Modules\FollowUp\Controllers\EventController::class, 'exportExcel'])
            ->name('events.export')
            ->middleware('permission:patients.view');
        Route::resource('events', \App\Modules\FollowUp\Controllers\EventController::class)
            ->middleware('permission:patients.view');
        Route::patch('events/{event}/toggle-active', [\App\Modules\FollowUp\Controllers\EventController::class, 'toggleActive'])
            ->name('events.toggle-active')
            ->middleware('permission:patients.view');

        // Campaigns
        Route::resource('campaigns', \App\Modules\FollowUp\Controllers\CampaignController::class)
            ->middleware('permission:patients.view');
        Route::patch('campaigns/{campaign}/toggle-active', [\App\Modules\FollowUp\Controllers\CampaignController::class, 'toggleActive'])
            ->name('campaigns.toggle-active')
            ->middleware('permission:patients.view');
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
    Route::get('/deletion-logs', [AuditLogController::class, 'deletionLogs'])
        ->name('audit.deletion-logs')
        ->middleware('permission:audit.view');

    // ── Administration (User & Access Management) ──
    Route::prefix('administration')->name('administration.')->group(function () {
        Route::resource('users', \App\Modules\MasterData\Controllers\UserController::class)->except('show');
        Route::resource('roles', \App\Modules\MasterData\Controllers\RoleController::class)->except('show');
    });
});

