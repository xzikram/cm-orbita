<?php

namespace App\Modules\Communication\Controllers;

use App\Models\DocumentDelivery;
use App\Models\DocumentType;
use App\Models\EmailAccount;
use App\Models\EmailTemplate;
use App\Models\Patient;
use App\Modules\Communication\Services\DocumentDeliveryService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class DocumentDeliveryController extends Controller
{
    public function __construct(
        protected DocumentDeliveryService $deliveryService,
        protected \App\Core\Services\PatientRegistrationService $registrationService
    ) {}

    public function index()
    {
        $deliveries = DocumentDelivery::with(['patient', 'documentType', 'sender'])
            ->where('clinic_id', Auth::user()->clinic_id)
            ->latest()
            ->paginate(config('cfms.per_page'));

        return view('communication.deliveries.index', compact('deliveries'));
    }

    public function create(Request $request)
    {
        $clinicId = Auth::user()->clinic_id;
        $patients = Patient::where('clinic_id', $clinicId)->active()->orderBy('name')->get();
        $documentTypes = \App\Models\DocumentType::where('clinic_id', $clinicId)->active()->orderBy('name')->get();
        $templates = \App\Models\EmailTemplate::where('clinic_id', $clinicId)->active()->orderBy('name')->get();
        $accounts = EmailAccount::where('clinic_id', $clinicId)->active()->get();

        $selectedPatient = $request->get('patient_id') ? Patient::find($request->get('patient_id')) : null;
        
        $processedDocs = collect();
        if ($request->has('processed_document_ids')) {
            $processedDocs = \App\Models\ProcessedDocument::whereIn('id', (array)$request->get('processed_document_ids'))
                ->where('clinic_id', $clinicId)
                ->get();
        } elseif ($request->has('processed_document_id')) {
            $singleDoc = \App\Models\ProcessedDocument::where('id', $request->get('processed_document_id'))
                ->where('clinic_id', $clinicId)
                ->first();
            if ($singleDoc) {
                $processedDocs = collect([$singleDoc]);
            }
        }

        $processedDoc = $processedDocs->first();

        // Cek status koneksi WhatsApp Gateway
        $provider = app(\App\Modules\Reminder\Contracts\WhatsAppProviderInterface::class);
        $whatsappConnected = true;
        if ($provider->getProviderName() === 'selfhosted') {
            $whatsappConnected = $provider->checkStatus();
        }

        return view('communication.deliveries.create', compact('patients', 'documentTypes', 'templates', 'accounts', 'selectedPatient', 'processedDoc', 'processedDocs', 'whatsappConnected'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|string|max:255',
            'document_type_id' => 'required|exists:document_types,id',
            'email_template_id' => 'required|exists:email_templates,id',
            'channel' => 'required|in:email,whatsapp',
            'email_account_id' => 'required_if:channel,email|nullable|exists:email_accounts,id',
            'recipient_email' => 'required_if:channel,email|nullable|email',
            'recipient_phone' => 'required_if:channel,whatsapp|nullable|string',
            'document_pdf' => 'nullable|file|mimes:pdf|max:10240',
            'document_pdfs' => 'nullable|array',
            'document_pdfs.*' => 'file|mimes:pdf|max:10240',
            'processed_document_id' => 'nullable|exists:processed_documents,id',
            'processed_document_ids' => 'nullable|array',
            'processed_document_ids.*' => 'exists:processed_documents,id',
            'password_protect' => 'nullable|boolean',
            'manual_dob' => 'nullable|date',
        ]);

        if (!$request->hasFile('document_pdf') && !$request->hasFile('document_pdfs') && !$request->filled('processed_document_id') && !$request->filled('processed_document_ids')) {
            return back()->withInput()->with('error', 'Silakan pilih atau unggah dokumen PDF terlebih dahulu.');
        }

        $patientId = $request->patient_id;
        
        if (is_numeric($patientId)) {
            $patient = Patient::findOrFail($patientId);
        } else {
            $dob = null;
            if ($request->filled('manual_dob')) {
                try {
                    $dob = \Carbon\Carbon::parse($request->manual_dob)->format('Y-m-d');
                } catch (\Exception $e) {
                    // ignore
                }
            }

            $patient = $this->registrationService->register([
                'name' => $patientId,
                'phone' => $request->recipient_phone,
                'email' => $request->recipient_email,
                'date_of_birth' => $dob,
                'registration_source' => 'document_delivery'
            ]);
        }

        $documentType = DocumentType::findOrFail($request->document_type_id);
        $template = EmailTemplate::findOrFail($request->email_template_id);
        $account = $request->channel === 'email' ? EmailAccount::findOrFail($request->email_account_id) : null;

        $processedDocs = collect();
        if ($request->has('processed_document_ids')) {
            $processedDocs = \App\Models\ProcessedDocument::whereIn('id', (array)$request->processed_document_ids)
                ->where('clinic_id', Auth::user()->clinic_id)
                ->get();
        }

        // Gather all files to send (either ProcessedDocument or UploadedFiles)
        $uploadedFiles = [];
        if ($request->hasFile('document_pdfs')) {
            $uploadedFiles = $request->file('document_pdfs');
        } elseif ($request->hasFile('document_pdf')) {
            $uploadedFiles = [$request->file('document_pdf')];
        }

        // Validasi koneksi WhatsApp Gateway jika mengirim lewat WA
        if ($request->channel === 'whatsapp') {
            $provider = app(\App\Modules\Reminder\Contracts\WhatsAppProviderInterface::class);
            if ($provider->getProviderName() === 'selfhosted' && !$provider->checkStatus()) {
                return back()->withInput()->with('error_html', 'WhatsApp Gateway belum terhubung. Silakan <a href="' . route('communication.whatsapp.status') . '" class="underline font-bold text-red-600 dark:text-red-400 hover:text-red-800">hubungkan WhatsApp Gateway</a> terlebih dahulu agar dapat mengirim pesan.');
            }
        }

        $password = null;
        if ($request->boolean('password_protect')) {
            if ($patient->date_of_birth) {
                $password = $patient->date_of_birth->format('dmY');
            } else {
                return back()->withInput()->with('error', 'Pasien tidak memiliki tanggal lahir untuk dijadikan password proteksi PDF.');
            }
        }

        try {
            if ($processedDocs->isNotEmpty()) {
                $deliveries = $this->deliveryService->sendMultipleDocuments(
                    patient: $patient,
                    documentType: $documentType,
                    template: $template,
                    account: $account,
                    processedDocs: $processedDocs,
                    recipientEmail: $request->recipient_email,
                    userId: Auth::id(),
                    channel: $request->channel,
                    recipientPhone: $request->recipient_phone,
                    password: $password
                );

                // Sinkronisasi info pasien & tipe dokumen ke semua ProcessedDocument jika kosong
                foreach ($processedDocs as $doc) {
                    $doc->update([
                        'patient_id' => $doc->patient_id ?? $patient->id,
                        'document_type_id' => $doc->document_type_id ?? $documentType->id,
                    ]);
                }
            } elseif (count($uploadedFiles) > 1) {
                $deliveries = $this->deliveryService->sendMultipleUploadedFiles(
                    patient: $patient,
                    documentType: $documentType,
                    template: $template,
                    account: $account,
                    files: $uploadedFiles,
                    recipientEmail: $request->recipient_email,
                    userId: Auth::id(),
                    channel: $request->channel,
                    recipientPhone: $request->recipient_phone,
                    password: $password
                );
            } else {
                $singleFile = count($uploadedFiles) === 1 ? $uploadedFiles[0] : null;

                if (!$singleFile && $request->filled('processed_document_id')) {
                    $processedDoc = \App\Models\ProcessedDocument::findOrFail($request->processed_document_id);
                    $filePath = \Illuminate\Support\Facades\Storage::disk('public')->path($processedDoc->generated_file_path);
                    $singleFile = new \Illuminate\Http\UploadedFile(
                        $filePath,
                        $processedDoc->original_filename ?? basename($processedDoc->generated_file_path),
                        'application/pdf',
                        null,
                        true // test mode
                    );
                }

                if (!$singleFile) {
                    throw new \Exception('Dokumen tidak ditemukan.');
                }

                $delivery = $this->deliveryService->sendDocument(
                    patient: $patient,
                    documentType: $documentType,
                    template: $template,
                    account: $account,
                    file: $singleFile,
                    recipientEmail: $request->recipient_email,
                    userId: Auth::id(),
                    channel: $request->channel,
                    recipientPhone: $request->recipient_phone,
                    password: $password,
                    processedDocumentId: $request->filled('processed_document_id') ? $request->processed_document_id : null
                );

                // Sinkronisasi info pasien & tipe dokumen ke ProcessedDocument jika sebelumnya kosong
                if ($request->filled('processed_document_id')) {
                    $processedDoc = \App\Models\ProcessedDocument::find($request->processed_document_id);
                    if ($processedDoc) {
                        $processedDoc->update([
                            'patient_id' => $processedDoc->patient_id ?? $patient->id,
                            'document_type_id' => $processedDoc->document_type_id ?? $documentType->id,
                        ]);
                    }
                }
            }

            if ($request->channel === 'whatsapp') {
                return redirect()->route('communication.deliveries.index')
                    ->with('success', 'Dokumen berhasil dikirim ke pasien via WhatsApp.');
            }

            return redirect()->route('communication.deliveries.index')
                ->with('success', 'Dokumen berhasil dikirim ke pasien via Email.');
        } catch (\Exception $e) {
            $errorMsg = $request->channel === 'whatsapp' 
                ? 'Gagal memproses dokumen WhatsApp: ' 
                : 'Gagal mengirim email: ';
            return back()->withInput()->with('error', $errorMsg . $e->getMessage());
        }
    }

    public function show(DocumentDelivery $delivery)
    {
        abort_if($delivery->clinic_id !== Auth::user()->clinic_id, 403);
        $delivery->load(['patient', 'documentType', 'emailTemplate', 'emailAccount', 'sender']);
        
        $whatsappMessage = '';
        if ($delivery->channel === 'whatsapp') {
            $whatsappMessage = $this->deliveryService->getWhatsAppMessageBody($delivery);
        }
        
        return view('communication.deliveries.show', compact('delivery', 'whatsappMessage'));
    }

    public function markAsSent(DocumentDelivery $delivery)
    {
        abort_if($delivery->clinic_id !== Auth::user()->clinic_id, 403);

        $delivery->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status pengiriman berhasil diperbarui.'
        ]);
    }

    public function whatsappStatus()
    {
        $config = config('whatsapp.providers.selfhosted');
        $url = $config['url'] ?? 'http://localhost:3000';
        
        $status = [
            'connected' => false,
            'qr' => null,
            'error' => null,
            'active_provider' => config('whatsapp.provider'),
        ];
        
        if ($status['active_provider'] === 'selfhosted') {
            try {
                $clientId = 'user-' . Auth::id();
                $response = \Illuminate\Support\Facades\Http::timeout(3)->get($url . '/status', [
                    'clientId' => $clientId
                ]);
                if ($response->successful()) {
                    $status['connected'] = $response->json('ready') === true;
                    $status['qr'] = $response->json('qr');
                } else {
                    $status['error'] = 'Gateway memberikan kode status: ' . $response->status();
                }
            } catch (\Exception $e) {
                $status['error'] = 'Tidak dapat terhubung ke WhatsApp Gateway lokal di ' . $url . '. Pastikan server Node.js sudah dijalankan dengan perintah "node server.js" di folder "whatsapp-gateway".';
            }
        }
        
        return view('communication.whatsapp.status', compact('status'));
    }

    public function checkWhatsAppConnection()
    {
        $provider = app(\App\Modules\Reminder\Contracts\WhatsAppProviderInterface::class);
        $connected = false;
        
        if ($provider->getProviderName() === 'selfhosted') {
            $connected = \Illuminate\Support\Facades\Cache::remember('wa_connected_' . Auth::id(), 10, function () use ($provider) {
                return $provider->checkStatus();
            });
        }
        
        return response()->json([
            'connected' => $connected,
            'url' => route('communication.whatsapp.status')
        ]);
    }
}
