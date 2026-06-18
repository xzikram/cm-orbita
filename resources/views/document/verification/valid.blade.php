<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Verification - {{ $document->document_number }}</title>
    <!-- We can use tailwind via CDN for this public page for simplicity -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-lg w-full bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
        <div class="bg-teal-600 px-6 py-8 text-center">
            <svg class="mx-auto h-16 w-16 text-white mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h1 class="text-2xl font-bold text-white">Document Verified</h1>
            <p class="text-teal-100 mt-2 text-sm">Dokumen ini diterbitkan secara sah oleh sistem klinik dan keasliannya terjamin.</p>
        </div>

        <div class="p-6 space-y-4">
            <div>
                <p class="text-sm text-gray-500 font-medium">Nomor Dokumen</p>
                <p class="text-lg font-bold text-gray-900">{{ $document->document_number }}</p>
            </div>
            
            <hr class="border-gray-100">

            <div>
                <p class="text-sm text-gray-500 font-medium">Klinik Penerbit</p>
                <p class="text-base text-gray-900">{{ $document->clinic->name ?? '-' }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500 font-medium">Jenis Dokumen</p>
                <p class="text-base text-gray-900">{{ $document->documentType->name ?? '-' }}</p>
            </div>

            <hr class="border-gray-100">
            
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-sm text-gray-500 font-medium mb-1">Diterbitkan Pada</p>
                <p class="text-gray-900 font-medium">{{ $document->created_at->format('d F Y H:i:s') }} WIB</p>
            </div>

            <div class="mt-6 text-center text-xs text-gray-400">
                <p>Status: <span class="text-green-600 font-bold uppercase">{{ $document->status }}</span></p>
                <p class="mt-2">ID: {{ $document->uuid }}</p>
            </div>
        </div>
    </div>

</body>
</html>
