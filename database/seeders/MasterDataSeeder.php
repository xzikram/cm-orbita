<?php

namespace Database\Seeders;

use App\Models\FollowUpStatus;
use App\Models\LensCondition;
use App\Models\ReminderTemplate;
use App\Models\DocumentType;
use App\Models\EmailTemplate;
use App\Models\Clinic;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $clinicId = Clinic::first()?->id;

        // ── Follow-Up Statuses ──
        $statuses = [
            ['name' => 'Hadir', 'slug' => 'hadir', 'color' => '#10B981', 'icon' => 'check-circle', 'sort_order' => 1],
            ['name' => 'Tidak Hadir', 'slug' => 'tidak-hadir', 'color' => '#EF4444', 'icon' => 'x-circle', 'sort_order' => 2],
            ['name' => 'Reschedule', 'slug' => 'reschedule', 'color' => '#F59E0B', 'icon' => 'calendar', 'sort_order' => 3],
        ];

        foreach ($statuses as $status) {
            FollowUpStatus::create($status);
        }

        // ── Lens Conditions ──
        $conditions = [
            ['name' => 'Baik', 'slug' => 'baik', 'color' => '#10B981', 'sort_order' => 1],
            ['name' => 'Kotor', 'slug' => 'kotor', 'color' => '#F59E0B', 'sort_order' => 2],
            ['name' => 'Rusak', 'slug' => 'rusak', 'color' => '#EF4444', 'sort_order' => 3],
            ['name' => 'Hilang', 'slug' => 'hilang', 'color' => '#6B7280', 'sort_order' => 4],
            ['name' => 'Perlu Penggantian', 'slug' => 'perlu-penggantian', 'color' => '#8B5CF6', 'sort_order' => 5],
        ];

        foreach ($conditions as $condition) {
            LensCondition::create($condition);
        }

        // ── Reminder Templates ──
        ReminderTemplate::create([
            'clinic_id' => $clinicId,
            'name' => 'Reminder Kontrol Pasien',
            'type' => 'follow_up',
            'channel' => 'whatsapp',
            'content' => "Yth. {patient_name},\n\nIni adalah pengingat untuk jadwal kontrol lensa kontak Anda di {clinic_name}.\n\n📅 Tanggal: {scheduled_date}\n👨‍⚕️ Dokter: {doctor_name}\n📋 Kontrol: {follow_up_label}\n\nMohon hadir tepat waktu. Jika berhalangan, silakan hubungi kami untuk penjadwalan ulang.\n\nTerima kasih.\n{clinic_name}",
            'variables' => ['patient_name', 'clinic_name', 'scheduled_date', 'doctor_name', 'follow_up_label'],
            'is_default' => true,
            'is_active' => true,
        ]);

        ReminderTemplate::create([
            'clinic_id' => $clinicId,
            'name' => 'Reminder Kontrol untuk Dokter',
            'type' => 'follow_up',
            'channel' => 'whatsapp',
            'content' => "Yth. {doctor_name},\n\nPasien berikut memiliki jadwal kontrol:\n\n👤 Pasien: {patient_name}\n📋 No. RM: {medical_record_number}\n📅 Tanggal: {scheduled_date}\n🏥 Kontrol: {follow_up_label}\n\nTerima kasih.\n{clinic_name}",
            'variables' => ['doctor_name', 'patient_name', 'medical_record_number', 'scheduled_date', 'follow_up_label', 'clinic_name'],
            'is_default' => true,
            'is_active' => true,
        ]);

        ReminderTemplate::create([
            'clinic_id' => $clinicId,
            'name' => 'Reminder Pasien Tidak Hadir',
            'type' => 'follow_up',
            'channel' => 'whatsapp',
            'content' => "Yth. {patient_name},\n\nKami melihat Anda belum hadir untuk kontrol lensa kontak yang dijadwalkan pada {scheduled_date}.\n\nKontrol rutin sangat penting untuk kesehatan mata Anda. Silakan hubungi kami untuk menjadwalkan ulang.\n\n📞 {clinic_phone}\n\nTerima kasih.\n{clinic_name}",
            'variables' => ['patient_name', 'scheduled_date', 'clinic_phone', 'clinic_name'],
            'is_default' => false,
            'is_active' => true,
        ]);

        // ── Document Types ──
        $documentTypes = [
            ['code' => 'medical_resume', 'name' => 'Resume Medis', 'clinic_id' => $clinicId],
            ['code' => 'prescription', 'name' => 'Resep Kacamata / Lensa', 'clinic_id' => $clinicId],
            ['code' => 'follow_up_letter', 'name' => 'Surat Rencana Kontrol', 'clinic_id' => $clinicId],
            ['code' => 'referral_letter', 'name' => 'Surat Rujukan', 'clinic_id' => $clinicId],
        ];

        foreach ($documentTypes as $docType) {
            DocumentType::updateOrCreate(['code' => $docType['code']], $docType);
        }

        // ── Email Templates ──
        $emailTemplates = [
            [
                'code' => 'medical_resume_delivery',
                'name' => 'Pengiriman Resume Medis',
                'subject_template' => 'Resume Medis Pasien: {patient_name}',
                'html_body' => '<p>Halo {patient_name},</p><p>Berikut kami lampirkan dokumen <strong>Resume Medis</strong> hasil pemeriksaan Anda di {clinic_name}.</p><p>Terima kasih.</p>',
                'variables' => ['patient_name', 'clinic_name'],
                'clinic_id' => $clinicId,
            ],
            [
                'code' => 'prescription_delivery',
                'name' => 'Pengiriman Resep Kacamata',
                'subject_template' => 'Resep Kacamata Pasien: {patient_name}',
                'html_body' => '<p>Halo {patient_name},</p><p>Berikut kami lampirkan dokumen <strong>Resep Kacamata/Lensa Kontak</strong> Anda dari {clinic_name}.</p><p>Terima kasih.</p>',
                'variables' => ['patient_name', 'clinic_name'],
                'clinic_id' => $clinicId,
            ],
            [
                'code' => 'follow_up_letter_delivery',
                'name' => 'Pengiriman Rencana Kontrol',
                'subject_template' => 'Surat Rencana Kontrol: {patient_name}',
                'html_body' => '<p>Halo {patient_name},</p><p>Berikut kami lampirkan dokumen <strong>Rencana Kontrol Rutin</strong> Anda di {clinic_name}.</p><p>Terima kasih.</p>',
                'variables' => ['patient_name', 'clinic_name'],
                'clinic_id' => $clinicId,
            ]
        ];

        foreach ($emailTemplates as $template) {
            EmailTemplate::updateOrCreate(['code' => $template['code']], $template);
        }
    }
}
