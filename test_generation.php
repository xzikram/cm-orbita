<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ProcessedDocument;
use App\Modules\Document\Services\PdfGeneratorService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

$latest = ProcessedDocument::latest()->first();
if (!$latest) {
    echo "No processed document found.\n";
    exit;
}

$template = $latest->documentTemplate;
$originalPath = Storage::disk('public')->path($latest->original_file_path);

echo "Latest Processed Document ID: " . $latest->id . "\n";
echo "Original PDF path: " . $originalPath . "\n";
echo "Template Name: " . $template->name . "\n";

$originalFile = new UploadedFile(
    $originalPath,
    basename($latest->original_file_path),
    'application/pdf',
    null,
    true // test mode
);

$service = app(PdfGeneratorService::class);
$newDoc = $service->processDocument(
    $originalFile,
    $latest->patient,
    $template,
    $latest->document_type_id,
    $latest->created_by
);

echo "New generated PDF relative path: " . $newDoc->generated_file_path . "\n";
$absolutePath = Storage::disk('public')->path($newDoc->generated_file_path);
echo "New generated PDF absolute path: " . $absolutePath . "\n";
