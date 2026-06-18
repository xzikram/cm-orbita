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
    public function __construct(protected DocumentDeliveryService $deliveryService) {}

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

        return view('communication.deliveries.create', compact('patients', 'documentTypes', 'templates', 'accounts', 'selectedPatient'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'document_type_id' => 'required|exists:document_types,id',
            'email_template_id' => 'required|exists:email_templates,id',
            'channel' => 'required|in:email,whatsapp',
            'email_account_id' => 'required_if:channel,email|nullable|exists:email_accounts,id',
            'recipient_email' => 'required_if:channel,email|nullable|email',
            'recipient_phone' => 'required_if:channel,whatsapp|nullable|string',
            'document_pdf' => 'required|file|mimes:pdf|max:10240', // Max 10MB
            'password_protect' => 'nullable|boolean',
        ]);

        $patient = Patient::findOrFail($request->patient_id);
        $documentType = DocumentType::findOrFail($request->document_type_id);
        $template = EmailTemplate::findOrFail($request->email_template_id);
        $account = $request->channel === 'email' ? EmailAccount::findOrFail($request->email_account_id) : null;

        $password = null;
        if ($request->boolean('password_protect')) {
            if ($patient->date_of_birth) {
                $password = $patient->date_of_birth->format('dmY');
            } else {
                return back()->withInput()->with('error', 'Pasien tidak memiliki tanggal lahir untuk dijadikan password proteksi PDF.');
            }
        }

        try {
            $delivery = $this->deliveryService->sendDocument(
                patient: $patient,
                documentType: $documentType,
                template: $template,
                account: $account,
                file: $request->file('document_pdf'),
                recipientEmail: $request->recipient_email,
                userId: Auth::id(),
                channel: $request->channel,
                recipientPhone: $request->recipient_phone,
                password: $password
            );

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
                $response = \Illuminate\Support\Facades\Http::timeout(3)->get($url . '/status');
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
}
