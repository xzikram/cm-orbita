<?php

namespace App\Modules\Document\Services;

use App\Models\DocumentTemplate;
use App\Models\Patient;
use App\Models\ProcessedDocument;
use Barryvdh\DomPDF\Facade\Pdf as DomPDF;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use setasign\Fpdi\Fpdi;

class PdfGeneratorService
{
    public function __construct(protected DocumentNumberService $numberService) {}

    /**
     * Process an uploaded PDF and wrap it with a generated cover page.
     */
    public function processDocument(
        UploadedFile $originalFile,
        ?Patient $patient,
        DocumentTemplate $template,
        ?int $documentTypeId,
        int $userId
    ): ProcessedDocument {
        $uuid = (string) Str::uuid();
        $documentType = $documentTypeId ? \App\Models\DocumentType::find($documentTypeId) : null;
        $documentNumber = $this->numberService->generateNumber($documentType);

        // 1. Store Original PDF
        $originalFilename = "{$uuid}_original.pdf";
        $originalPath = $originalFile->storeAs('documents/original', $originalFilename, 'public');

        // 2. Wrap Original Pages using FPDI
        $fpdi = new Fpdi();

        // 4b. Add Original Pages
        $absoluteOriginalPath = Storage::disk('public')->path($originalPath);
        $pageCountOriginal = $fpdi->setSourceFile($absoluteOriginalPath);
        for ($pageNo = 1; $pageNo <= $pageCountOriginal; $pageNo++) {
            $templateId = $fpdi->importPage($pageNo);
            $size = $fpdi->getTemplateSize($templateId);
            $fpdi->AddPage($size['orientation'], [$size['width'], $size['height']]);

            // Retrieve margins (default values in mm: top=40, bottom=30, left=20, right=20)
            $marginLeft = (int)($template->margin_left ?? 20);
            $marginTop = (int)($template->margin_top ?? 40);
            $marginRight = (int)($template->margin_right ?? 20);
            $marginBottom = (int)($template->margin_bottom ?? 30);

            // Calculate shrink size for original page to fit between margins
            $newWidth = $size['width'] - $marginLeft - $marginRight;
            $newHeight = $size['height'] - $marginTop - $marginBottom;

            // Draw original page content shrunk to fit inside margins
            $fpdi->useTemplate($templateId, $marginLeft, $marginTop, $newWidth, $newHeight);

            // Draw Header Image (Kop) aligned with page margins
            if ($template->header_logo_path) {
                $headerImgPath = Storage::disk('public')->path($template->header_logo_path);
                if (file_exists($headerImgPath)) {
                    list($imgWidth, $imgHeight) = getimagesize($headerImgPath);
                    $aspectRatio = $imgHeight > 0 ? $imgWidth / $imgHeight : 1;
                    
                    if ($aspectRatio < 1.0) {
                        // Portrait template sheet: overlay full page
                        $fpdi->Image($headerImgPath, 0, 0, $size['width'], $size['height']);
                    } else {
                        // Landscape/cropped logo: scale and center in the margin
                        $maxW = $newWidth;
                        $maxH = max(10, $marginTop - 15);
                        
                        $w = $maxW;
                        $h = $w / $aspectRatio;
                        
                        if ($h > $maxH) {
                            $h = $maxH;
                            $w = $h * $aspectRatio;
                        }
                        
                        $x = $marginLeft + ($newWidth - $w) / 2;
                        $y = 5; 
                        
                        $fpdi->Image($headerImgPath, $x, $y, $w, $h);
                    }
                }
            }

            // Draw Footer Image aligned with page margins
            if ($template->footer_logo_path) {
                $footerImgPath = Storage::disk('public')->path($template->footer_logo_path);
                if (file_exists($footerImgPath)) {
                    list($imgWidth, $imgHeight) = getimagesize($footerImgPath);
                    $aspectRatio = $imgHeight > 0 ? $imgWidth / $imgHeight : 1;
                    
                    if ($aspectRatio < 1.0) {
                        // Portrait template sheet: overlay full page
                        $fpdi->Image($footerImgPath, 0, 0, $size['width'], $size['height']);
                    } else {
                        // Landscape/cropped logo: scale and center in the bottom margin
                        $maxW = $newWidth;
                        $maxH = max(8, $marginBottom - 10);
                        
                        $w = $maxW;
                        $h = $w / $aspectRatio;
                        
                        if ($h > $maxH) {
                            $h = $maxH;
                            $w = $h * $aspectRatio;
                        }
                        
                        $x = $marginLeft + ($newWidth - $w) / 2;
                        $y = $size['height'] - $marginBottom + 2;
                        
                        $fpdi->Image($footerImgPath, $x, $y, $w, $h);
                    }
                }
            }

            // Draw Disclaimer Text
            if ($template->disclaimer_text) {
                $fpdi->SetFont('Helvetica', '', 8);
                $fpdi->SetTextColor(150, 150, 150);
                // Position at y=page height - 8
                $fpdi->SetXY(0, $size['height'] - 8);
                $fpdi->Cell($size['width'], 5, $template->disclaimer_text, 0, 0, 'C');
            }

            // Add Watermark if template has one
            if (!empty($template->watermark_text)) {
                $fpdi->SetFont('Helvetica', 'B', 50);
                $fpdi->SetTextColor(240, 240, 240); // very light grey for transparency look
                
                // Position watermark in the middle
                $fpdi->SetXY(20, $size['height'] / 2);
                $fpdi->Cell(0, 0, strtoupper($template->watermark_text), 0, 0, 'C');
            }
        }

        // 5. Save Final Generated PDF
        $generatedFilename = "{$uuid}_generated.pdf";
        $generatedRelativePath = "documents/generated/{$generatedFilename}";
        $generatedAbsolutePath = Storage::disk('public')->path($generatedRelativePath);
        
        if (!file_exists(dirname($generatedAbsolutePath))) {
            mkdir(dirname($generatedAbsolutePath), 0755, true);
        }
        $fpdi->Output($generatedAbsolutePath, 'F');

        // 6. Save to Database
        return ProcessedDocument::create([
            'uuid' => $uuid,
            'document_number' => $documentNumber,
            'clinic_id' => $patient?->clinic_id ?? (\App\Models\User::find($userId)?->clinic_id ?? auth()->user()?->clinic_id),
            'patient_id' => $patient?->id,
            'document_type_id' => $documentTypeId,
            'document_template_id' => $template->id,
            'original_file_path' => $originalPath,
            'generated_file_path' => $generatedRelativePath,
            'status' => 'generated',
            'created_by' => $userId,
        ]);
    }
}
