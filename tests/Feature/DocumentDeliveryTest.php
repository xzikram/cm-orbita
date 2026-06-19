<?php

namespace Tests\Feature;

use App\Models\Clinic;
use App\Models\DocumentDelivery;
use App\Models\DocumentType;
use App\Models\EmailAccount;
use App\Models\EmailTemplate;
use App\Models\Patient;
use App\Models\User;
use App\Modules\Reminder\Contracts\WhatsAppProviderInterface;
use App\Modules\Reminder\DTOs\SendResult;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class DocumentDeliveryTest extends TestCase
{
    use RefreshDatabase;

    protected Clinic $clinic;
    protected User $user;
    protected DocumentType $documentType;
    protected EmailTemplate $emailTemplate;
    protected EmailAccount $emailAccount;

    protected function setUp(): void
    {
        parent::setUp();

        // Create initial lookup database data
        $this->clinic = Clinic::create([
            'name' => 'Klinik Utama Sehat',
            'code' => 'KUS01',
            'address' => 'Jl. Kesehatan Raya No. 1',
            'phone' => '021123456',
            'email' => 'info@kliniksehat.com',
            'is_active' => true,
        ]);

        $this->user = User::create([
            'name' => 'Dr. Budi',
            'email' => 'budi@kliniksehat.com',
            'password' => bcrypt('password'),
            'clinic_id' => $this->clinic->id,
            'is_active' => true,
        ]);

        $this->documentType = DocumentType::create([
            'clinic_id' => $this->clinic->id,
            'name' => 'Resep Kacamata',
            'code' => 'prescription',
            'is_active' => true,
        ]);

        $this->emailTemplate = EmailTemplate::create([
            'clinic_id' => $this->clinic->id,
            'name' => 'Kirim Resep Kacamata',
            'code' => 'prescription',
            'type' => 'prescription',
            'subject_template' => 'Resep Kacamata Anda - {{patient_name}}',
            'html_body' => '<p>Halo {{patient_name}}, ini dokumen Anda.</p>',
            'is_active' => true,
        ]);

        $this->emailAccount = EmailAccount::create([
            'clinic_id' => $this->clinic->id,
            'name' => 'SMTP Utama',
            'email_address' => 'noreply@kliniksehat.com',
            'smtp_host' => 'smtp.mailtrap.io',
            'smtp_port' => 2525,
            'smtp_username' => 'user',
            'smtp_password' => 'pass',
            'encryption' => 'tls',
            'is_default' => true,
            'is_active' => true,
        ]);
    }

    public function test_can_register_patient_on_the_fly_via_document_delivery(): void
    {
        $this->withoutExceptionHandling();

        // 1. Mock the WhatsApp Provider
        $mockProvider = $this->createMock(WhatsAppProviderInterface::class);
        $mockProvider->method('sendDocumentFile')
            ->willReturn(SendResult::success('msg-12345'));
        
        $this->app->instance(WhatsAppProviderInterface::class, $mockProvider);

        // 2. Assign permission to the user
        \Spatie\Permission\Models\Permission::findOrCreate('communication.deliveries.manage', 'web');
        $this->user->givePermissionTo('communication.deliveries.manage');

        // 3. Act as the authenticated user
        $this->actingAs($this->user);

        // 3. Create a real valid PDF using TCPDF
        $pdf = new \TCPDF();
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->AddPage();
        $pdf->Cell(0, 10, 'Test PDF content', 0, 1);
        $tempPdfPath = tempnam(sys_get_temp_dir(), 'test_pdf_') . '.pdf';
        $pdf->Output($tempPdfPath, 'F');

        $pdfFile = new \Illuminate\Http\UploadedFile(
            $tempPdfPath,
            'resep_kacamata.pdf',
            'application/pdf',
            null,
            true // test mode
        );

        // 4. Send the request
        $response = $this->post(route('communication.deliveries.store'), [
            'patient_id' => 'Budi Sudarsono', // Manual name
            'manual_dob' => '1990-05-15',     // Manual DOB
            'recipient_phone' => '081234567890',
            'channel' => 'whatsapp',
            'document_type_id' => $this->documentType->id,
            'email_template_id' => $this->emailTemplate->id,
            'document_pdf' => $pdfFile,
            'password_protect' => '1',
        ]);

        // Clean up the temp file
        @unlink($tempPdfPath);

        // 5. Assertions
        $response->assertRedirect(route('communication.deliveries.index'));
        $response->assertSessionHas('success');

        // Verify patient was created automatically
        $patient = Patient::where('name', 'Budi Sudarsono')->first();
        $this->assertNotNull($patient);
        $this->assertEquals($this->clinic->id, $patient->clinic_id);
        $this->assertEquals('081234567890', $patient->phone);
        $this->assertEquals('1990-05-15', $patient->date_of_birth->format('Y-m-d'));
        $this->assertTrue(str_starts_with($patient->medical_record_number, 'RM-'));

        // Verify document delivery record was created
        $delivery = DocumentDelivery::where('patient_id', $patient->id)->first();
        $this->assertNotNull($delivery);
        $this->assertEquals('sent', $delivery->status);
        $this->assertEquals('whatsapp', $delivery->channel);
    }
}
