<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ── Define Permissions ──
        $permissions = [
            // Dashboard
            'dashboard.view',

            // Master Data
            'clinics.view', 'clinics.create', 'clinics.edit', 'clinics.delete',
            'doctors.view', 'doctors.create', 'doctors.edit', 'doctors.delete',
            'ros.view', 'ros.create', 'ros.edit', 'ros.delete',
            'medass.view', 'medass.create', 'medass.edit', 'medass.delete',
            'users.view', 'users.create', 'users.edit', 'users.delete',
            'roles.view', 'roles.create', 'roles.edit', 'roles.delete',
            'reminder-templates.view', 'reminder-templates.create', 'reminder-templates.edit', 'reminder-templates.delete',

            // Patients
            'patients.view', 'patients.create', 'patients.edit', 'patients.delete',

            // Examinations
            'examinations.view', 'examinations.create', 'examinations.edit', 'examinations.delete',

            // Follow-Up
            'follow-up.view', 'follow-up.create', 'follow-up.edit', 'follow-up.delete',
            'follow-up.record-visit',

            // Reminders
            'reminders.view', 'reminders.send', 'reminders.manage',

            // Communication
            'communication.deliveries.manage',
            'communication.whatsapp.manage',
            'communication.email-templates.manage',
            'communication.whatsapp-templates.manage',
            'communication.document-types.manage',
            'communication.email-accounts.manage',

            // Audit
            'audit.view',

            // Settings
            'settings.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        // ── Define Roles ──
        $superAdmin = Role::findOrCreate('super-admin', 'web');
        $adminKlinik = Role::findOrCreate('admin-klinik', 'web');
        $dokter = Role::findOrCreate('dokter', 'web');
        $medAss = Role::findOrCreate('med-ass', 'web');
        $ro = Role::findOrCreate('ro', 'web');
        $petugasFollowUp = Role::findOrCreate('petugas-follow-up', 'web');

        // ── Assign Permissions ──

        // Super Admin gets all permissions
        $superAdmin->syncPermissions($permissions);

        // Admin Klinik
        $adminKlinik->syncPermissions([
            'dashboard.view',
            'clinics.view',
            'doctors.view', 'doctors.create', 'doctors.edit',
            'ros.view', 'ros.create', 'ros.edit',
            'medass.view', 'medass.create', 'medass.edit',
            'users.view', 'users.create', 'users.edit',
            'roles.view',
            'reminder-templates.view', 'reminder-templates.create', 'reminder-templates.edit',
            'patients.view', 'patients.create', 'patients.edit',
            'examinations.view', 'examinations.create', 'examinations.edit',
            'follow-up.view', 'follow-up.create', 'follow-up.edit', 'follow-up.record-visit',
            'reminders.view', 'reminders.send', 'reminders.manage',
            'communication.deliveries.manage',
            'communication.whatsapp.manage',
            'communication.email-templates.manage',
            'communication.whatsapp-templates.manage',
            'communication.document-types.manage',
            'communication.email-accounts.manage',
            'audit.view',
            'settings.manage',
        ]);

        // Dokter
        $dokter->syncPermissions([
            'dashboard.view',
            'patients.view',
            'examinations.view', 'examinations.create', 'examinations.edit',
            'follow-up.view', 'follow-up.record-visit',
            'reminders.view',
        ]);

        // Medical Assistant
        $medAss->syncPermissions([
            'dashboard.view',
            'patients.view', 'patients.create', 'patients.edit',
            'examinations.view', 'examinations.create',
            'follow-up.view', 'follow-up.record-visit',
            'reminders.view',
        ]);

        // Refraksionis Optisien
        $ro->syncPermissions([
            'dashboard.view',
            'patients.view',
            'examinations.view', 'examinations.create', 'examinations.edit',
            'follow-up.view', 'follow-up.record-visit',
            'reminders.view',
        ]);

        // Petugas Follow Up
        $petugasFollowUp->syncPermissions([
            'dashboard.view',
            'patients.view',
            'examinations.view',
            'follow-up.view', 'follow-up.create', 'follow-up.edit', 'follow-up.record-visit',
            'reminders.view', 'reminders.send',
        ]);
    }
}
