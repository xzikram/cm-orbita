<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentDelivery extends Model
{
    protected $fillable = [
        'clinic_id', 'patient_id', 'email_account_id', 'whatsapp_account_id', 'document_type_id',
        'email_template_id', 'processed_document_id', 'sent_by', 'channel', 'recipient_email', 
        'recipient_phone', 'subject', 'attachment_name', 'attachment_path',
        'status', 'error_message', 'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function processedDocument(): BelongsTo
    {
        return $this->belongsTo(ProcessedDocument::class, 'processed_document_id');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class)->withTrashed();
    }

    public function emailAccount(): BelongsTo
    {
        return $this->belongsTo(EmailAccount::class);
    }

    public function whatsappAccount(): BelongsTo
    {
        return $this->belongsTo(WhatsAppAccount::class);
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function emailTemplate(): BelongsTo
    {
        return $this->belongsTo(EmailTemplate::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Get detailed status categorization and presentation for UI.
     */
    public function getStatusInfoAttribute(): array
    {
        if (in_array($this->status, ['sent', 'success'])) {
            return [
                'type' => 'sent',
                'label' => 'SENT',
                'class' => 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 ring-emerald-600/20',
                'description' => 'Terkirim ke ' . ($this->channel === 'whatsapp' ? 'WhatsApp' : 'Email'),
                'can_resend' => false,
            ];
        }

        if ($this->status === 'failed') {
            $err = strtolower($this->error_message ?? '');
            
            if (str_contains($err, 'tidak terdaftar') || str_contains($err, 'no lid') || str_contains($err, 'lid tidak ditemukan')) {
                return [
                    'type' => 'unregistered',
                    'label' => 'TIDAK ADA WA',
                    'class' => 'bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 ring-amber-600/20',
                    'description' => 'Nomor HP tidak terdaftar di WhatsApp',
                    'can_resend' => true,
                ];
            }

            if (str_contains($err, 'format') || str_contains($err, 'digit') || str_contains($err, 'terlalu panjang')) {
                return [
                    'type' => 'invalid_format',
                    'label' => 'FORMAT SALAH',
                    'class' => 'bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 ring-amber-600/20',
                    'description' => 'Format nomor HP kurang / salah digit',
                    'can_resend' => true,
                ];
            }

            if (str_contains($err, 'belum terhubung') || str_contains($err, 'terputus') || str_contains($err, 'tidak ada whatsapp client') || str_contains($err, '503')) {
                return [
                    'type' => 'gateway_offline',
                    'label' => 'GATEWAY OFFLINE',
                    'class' => 'bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 ring-rose-600/20',
                    'description' => 'WhatsApp Gateway belum terhubung',
                    'can_resend' => true,
                ];
            }

            return [
                'type' => 'failed',
                'label' => 'FAILED',
                'class' => 'bg-red-50 dark:bg-red-950/40 text-red-700 dark:text-red-400 ring-red-600/20',
                'description' => $this->error_message ?? 'Gagal mengirim dokumen',
                'can_resend' => ($this->channel === 'whatsapp'),
            ];
        }

        return [
            'type' => 'pending',
            'label' => strtoupper($this->status),
            'class' => 'bg-yellow-50 dark:bg-yellow-950/40 text-yellow-800 dark:text-yellow-400 ring-yellow-600/20',
            'description' => 'Menunggu pengiriman',
            'can_resend' => false,
        ];
    }
}
