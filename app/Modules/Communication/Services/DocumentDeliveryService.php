<?php

namespace App\Modules\Communication\Services;

use App\Core\Services\AuditLogService;
use App\Models\DocumentDelivery;
use App\Models\DocumentType;
use App\Models\EmailAccount;
use App\Models\EmailTemplate;
use App\Models\Patient;
use App\Models\WhatsAppAccount;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Mail\Message;
use App\Modules\Reminder\Channels\WhatsAppChannel;
use App\Modules\Reminder\DTOs\ReminderDTO;

class DocumentDeliveryService
{
    public function __construct(
        protected DynamicMailerService $mailerService,
        protected AuditLogService $auditLogService,
        protected WhatsAppChannel $whatsAppChannel
    ) {}

    /**
     * Send a document via email or WhatsApp.
     * 
     * @param Patient $patient
     * @param DocumentType $documentType
     * @param EmailTemplate $template
     * @param EmailAccount|null $account
     * @param UploadedFile $file
     * @param string|null $recipientEmail
     * @param int $userId
     * @param string $channel
     * @param string|null $recipientPhone
     * @param string|null $password
     * @return DocumentDelivery
     */
    public function sendDocument(
        Patient $patient,
        DocumentType $documentType,
        EmailTemplate $template,
        ?EmailAccount $account,
        UploadedFile $file,
        ?string $recipientEmail,
        int $userId,
        string $channel = 'email',
        ?string $recipientPhone = null,
        ?string $password = null,
        ?int $processedDocumentId = null
    ): DocumentDelivery {
        // 1. Temporarily store the file
        $fileName = $file->getClientOriginalName();
        $tempPath = $file->storeAs('temp_documents', uniqid() . '_' . $fileName, 'local');
        $absolutePath = Storage::disk('local')->path($tempPath);

        // Encrypt PDF if password is provided
        if (!empty($password)) {
            try {
                $tempProtectedPath = 'temp_documents/enc_' . uniqid() . '_' . $fileName;
                $absoluteProtectedPath = Storage::disk('local')->path($tempProtectedPath);
                
                if (!file_exists(dirname($absoluteProtectedPath))) {
                    mkdir(dirname($absoluteProtectedPath), 0755, true);
                }

                $this->encryptPdf($absolutePath, $absoluteProtectedPath, $password);

                // Clean up the unprotected temp file and point to protected file
                Storage::disk('local')->delete($tempPath);
                $tempPath = $tempProtectedPath;
                $absolutePath = $absoluteProtectedPath;
            } catch (\Exception $e) {
                Log::error('PDF encryption failed: ' . $e->getMessage());
                Storage::disk('local')->delete($tempPath);
                throw new \Exception('Gagal mengenkripsi dokumen PDF: ' . $e->getMessage());
            }
        }

        // 2. Parse Template Variables
        $subject = $this->parseVariables($template->subject_template, $patient);
        $subject = str_replace(['{{document_name}}', '{document_name}'], $documentType->name, $subject);
        $subject = str_replace(['{{clinic_name}}', '{clinic_name}'], $patient->clinic->name ?? config('app.name'), $subject);

        $htmlBody = $this->parseVariables($template->html_body, $patient);
        $htmlBody = str_replace(['{{document_name}}', '{document_name}'], $documentType->name, $htmlBody);
        $htmlBody = str_replace(['{{clinic_name}}', '{clinic_name}'], $patient->clinic->name ?? config('app.name'), $htmlBody);

        if ($channel === 'whatsapp') {
            // Store the file persistently in public disk for download
            $publicPath = 'deliveries/' . uniqid() . '_' . $fileName;
            Storage::disk('public')->writeStream($publicPath, Storage::disk('local')->readStream($tempPath));

            $fileUrl = asset(Storage::url($publicPath));

            // Convert HTML template body to plain text for WhatsApp
            $textBody = preg_replace('/<br\s*\/?>/i', "\n", $htmlBody);
            $textBody = preg_replace('/<\/p>/i', "\n\n", $textBody);
            $textBody = strip_tags($textBody);
            $textBody = html_entity_decode($textBody, ENT_QUOTES, 'UTF-8');
            $textBody = trim(preg_replace("/\n{3,}/", "\n\n", $textBody));

            if (!empty($password)) {
                $textBody .= "\n\nPassword untuk membuka file PDF: Tanggal Lahir Anda (Format: DDMMYYYY, contoh: " . $patient->date_of_birth->format('dmY') . ").";
            }

            // Create Delivery Record (Pending)
            $delivery = DocumentDelivery::create([
                'clinic_id' => $patient->clinic_id,
                'patient_id' => $patient->id,
                'document_type_id' => $documentType->id,
                'email_template_id' => $template->id,
                'processed_document_id' => $processedDocumentId,
                'sent_by' => $userId,
                'channel' => 'whatsapp',
                'recipient_phone' => $recipientPhone,
                'attachment_name' => $fileName,
                'attachment_path' => $publicPath,
                'status' => 'pending',
            ]);

            try {
                $provider = app(\App\Modules\Reminder\Contracts\WhatsAppProviderInterface::class);
                $result = $provider->sendDocumentFile(
                    phone: $recipientPhone,
                    fileUrl: $fileUrl,
                    filename: $fileName,
                    caption: $textBody
                );

                if ($result->success) {
                    $delivery->update([
                        'status' => 'sent',
                        'sent_at' => now(),
                    ]);

                    $this->auditLogService->logCreated('DocumentDelivery', $delivery->id, [
                        'action' => 'Sent Document WhatsApp',
                        'patient' => $patient->name,
                        'document' => $documentType->name,
                    ]);
                } else {
                    throw new \Exception($result->error ?? 'WhatsApp provider failed to send document file');
                }
            } catch (\Exception $e) {
                Log::error('Failed to send document WhatsApp: ' . $e->getMessage());

                $delivery->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);

                $this->auditLogService->logCreated('DocumentDeliveryFailed', $delivery->id, [
                    'error' => $e->getMessage(),
                ]);

                // Clean up local temp file
                Storage::disk('local')->delete($tempPath);

                throw $e;
            }

            // Clean up local temp file
            Storage::disk('local')->delete($tempPath);

            return $delivery;
        } else {
            // Create Delivery Record (Pending)
            $delivery = DocumentDelivery::create([
                'clinic_id' => $patient->clinic_id,
                'patient_id' => $patient->id,
                'email_account_id' => $account?->id,
                'document_type_id' => $documentType->id,
                'email_template_id' => $template->id,
                'processed_document_id' => $processedDocumentId,
                'sent_by' => $userId,
                'channel' => 'email',
                'recipient_email' => $recipientEmail,
                'subject' => $subject,
                'attachment_name' => $fileName,
                'status' => 'pending',
            ]);

            // Configure Dynamic Mailer and Send
            try {
                $this->mailerService->setMailer($account);
                $mailer = $this->mailerService->getMailer();

                $mailer->html($htmlBody, function (Message $message) use ($recipientEmail, $subject, $absolutePath, $fileName, $account) {
                    $message->to($recipientEmail)
                            ->subject($subject)
                            ->from($account->email_address, $account->name)
                            ->attach($absolutePath, [
                                'as' => $fileName,
                                'mime' => mime_content_type($absolutePath),
                            ]);
                });

                // If success, update delivery status
                $delivery->update([
                    'status' => 'sent',
                    'sent_at' => now(),
                ]);

                // Audit
                $this->auditLogService->logCreated('DocumentDelivery', $delivery->id, [
                    'action' => 'Sent Document Email',
                    'patient' => $patient->name,
                    'document' => $documentType->name,
                ]);

                // Clean up file
                Storage::disk('local')->delete($tempPath);

            } catch (\Exception $e) {
                Log::error('Failed to send document email: ' . $e->getMessage());
                
                $delivery->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);

                // Audit failure
                $this->auditLogService->logCreated('DocumentDeliveryFailed', $delivery->id, [
                    'error' => $e->getMessage(),
                ]);

                Storage::disk('local')->delete($tempPath);

                throw $e;
            }
        }

        return $delivery;
    }

    /**
     * Password protect a PDF file using TcpdfFpdi.
     */
    protected function encryptPdf(string $sourcePath, string $outputPath, string $password): void
    {
        $pdf = new \setasign\Fpdi\TcpdfFpdi();
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        
        $pdf->SetProtection(array('print', 'copy'), $password);
        
        $pageCount = $pdf->setSourceFile($sourcePath);
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $templateId = $pdf->importPage($pageNo);
            $size = $pdf->getTemplateSize($templateId);
            
            $pdf->AddPage($size['orientation'], array($size['width'], $size['height']));
            $pdf->useTemplate($templateId);
        }
        
        $pdf->Output($outputPath, 'F');
    }

    /**
     * Helper to parse variables in text
     */
    protected function parseVariables(string $text, Patient $patient): string
    {
        $replacements = [
            '{{patient_name}}' => $patient->name,
            '{patient_name}' => $patient->name,
            '{{mrn}}' => $patient->medical_record_number,
            '{mrn}' => $patient->medical_record_number,
            '{{medical_record_number}}' => $patient->medical_record_number,
            '{medical_record_number}' => $patient->medical_record_number,
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $text);
    }

    /**
     * Reconstruct the plain text message body for WhatsApp Web sending.
     */
    public function getWhatsAppMessageBody(DocumentDelivery $delivery): string
    {
        $patient = $delivery->patient;
        $template = $delivery->emailTemplate;
        $documentType = $delivery->documentType;
        
        $htmlBody = $this->parseVariables($template->html_body, $patient);
        $htmlBody = str_replace(['{{document_name}}', '{document_name}'], $documentType->name, $htmlBody);
        $htmlBody = str_replace(['{{clinic_name}}', '{clinic_name}'], $patient->clinic->name ?? config('app.name'), $htmlBody);

        // Convert HTML template body to plain text for WhatsApp
        $textBody = preg_replace('/<br\s*\/?>/i', "\n", $htmlBody);
        $textBody = preg_replace('/<\/p>/i', "\n\n", $textBody);
        $textBody = strip_tags($textBody);
        $textBody = html_entity_decode($textBody, ENT_QUOTES, 'UTF-8');
        $textBody = trim(preg_replace("/\n{3,}/", "\n\n", $textBody));

        $fileUrl = asset(Storage::url($delivery->attachment_path));
        $textBody .= "\n\nSilakan unduh dokumen Anda melalui tautan berikut:\n" . $fileUrl;
        
        if ($patient->date_of_birth) {
            $textBody .= "\n\nPassword untuk membuka file PDF: Tanggal Lahir Anda (Format: DDMMYYYY, contoh: " . $patient->date_of_birth->format('dmY') . ").";
        }
        
        return $textBody;
    }
}
