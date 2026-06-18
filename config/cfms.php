<?php

return [

    /*
    |--------------------------------------------------------------------------
    | CFMS Application Configuration
    |--------------------------------------------------------------------------
    */

    'name' => env('CFMS_CLINIC_NAME', 'Clinical Follow-Up Management System'),

    /*
    |--------------------------------------------------------------------------
    | Follow-Up Schedule Intervals
    |--------------------------------------------------------------------------
    | Define the default follow-up intervals after initial examination.
    | Values are in days.
    */
    'follow_up_intervals' => [
        ['label' => 'Hari ke-1', 'days' => 1],
        ['label' => 'Hari ke-7', 'days' => 7],
        ['label' => '1 Bulan', 'days' => 30],
        ['label' => '3 Bulan', 'days' => 90],
        ['label' => '6 Bulan', 'days' => 180],
        ['label' => '1 Tahun', 'days' => 365],
    ],

    /*
    |--------------------------------------------------------------------------
    | Reminder Settings
    |--------------------------------------------------------------------------
    */
    'reminder' => [
        // Default channel for sending reminders
        'default_channel' => env('CFMS_REMINDER_CHANNEL', 'whatsapp'),

        // How many hours before the follow-up date to send reminder
        'hours_before' => env('CFMS_REMINDER_HOURS_BEFORE', 24),

        // Maximum retry attempts for failed reminders
        'max_retries' => env('CFMS_REMINDER_MAX_RETRIES', 3),

        // Retry delay in minutes
        'retry_delay' => env('CFMS_REMINDER_RETRY_DELAY', 30),

        // Available channels
        'channels' => [
            'whatsapp',
            // 'sms',    // Future
            // 'email',  // Future
            // 'push',   // Future
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Audit Log Settings
    |--------------------------------------------------------------------------
    */
    'audit' => [
        'enabled' => env('CFMS_AUDIT_ENABLED', true),
        'log_reads' => env('CFMS_AUDIT_LOG_READS', false),
        'retention_days' => env('CFMS_AUDIT_RETENTION_DAYS', 365),
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    */
    'security' => [
        'max_login_attempts' => env('CFMS_MAX_LOGIN_ATTEMPTS', 5),
        'lockout_minutes' => env('CFMS_LOCKOUT_MINUTES', 15),
        'session_lifetime' => env('SESSION_LIFETIME', 120),
        'password_min_length' => 8,
    ],

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */
    'per_page' => env('CFMS_PER_PAGE', 15),

    /*
    |--------------------------------------------------------------------------
    | Supported Modules
    |--------------------------------------------------------------------------
    | Register available modules here. Future modules can be added to this list.
    */
    'modules' => [
        'follow_up' => [
            'enabled' => true,
            'name' => 'Follow-Up Pasien Lensa Kontak',
            'icon' => 'eye',
        ],
        // Future modules:
        // 'appointment' => ['enabled' => false, 'name' => 'Appointment Management'],
        // 'telemedicine' => ['enabled' => false, 'name' => 'Telemedicine'],
        // 'patient_education' => ['enabled' => false, 'name' => 'Patient Education'],
        // 'medication_reminder' => ['enabled' => false, 'name' => 'Reminder Obat'],
        // 'surgery_reminder' => ['enabled' => false, 'name' => 'Reminder Operasi'],
        // 'mcu_reminder' => ['enabled' => false, 'name' => 'Reminder MCU'],
        // 'vaccine_reminder' => ['enabled' => false, 'name' => 'Reminder Vaksin'],
        // 'chronic_reminder' => ['enabled' => false, 'name' => 'Reminder Pasien Kronis'],
        // 'home_care' => ['enabled' => false, 'name' => 'Home Care'],
        // 'marketing' => ['enabled' => false, 'name' => 'Medical Marketing'],
        // 'crm' => ['enabled' => false, 'name' => 'CRM Klinik'],
        // 'inventory' => ['enabled' => false, 'name' => 'Inventory Non SIMRS'],
        // 'quality_control' => ['enabled' => false, 'name' => 'Quality Control Klinik'],
    ],
];
