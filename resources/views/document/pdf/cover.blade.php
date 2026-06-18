<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Cover Page</title>
    <style>
        @page {
            margin: 25px 30px;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .header {
            width: 100%;
            border-bottom: 2px solid #0d9488;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        .header table {
            width: 100%;
        }
        .header td {
            vertical-align: middle;
        }
        .logo-left {
            width: 30%;
        }
        .logo-right {
            width: 30%;
            text-align: right;
        }
        .header-title {
            width: 40%;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            color: #0d9488;
        }
        .logo {
            max-height: 80px;
            max-width: 150px;
        }
        .content {
            padding: 0 20px;
        }
        .document-title {
            text-align: center;
            font-size: 22px;
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 15px;
            text-transform: uppercase;
        }
        .patient-info {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        .patient-info th, .patient-info td {
            padding: 6px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }
        .patient-info th {
            width: 30%;
            color: #666;
        }
        .patient-info td {
            font-weight: bold;
        }
        .qr-section {
            text-align: center;
            margin-top: 20px;
        }
        .qr-code {
            margin-bottom: 5px;
        }
        .verification-text {
            font-size: 11px;
            color: #666;
        }
        .document-number {
            font-size: 14px;
            font-weight: bold;
            margin-top: 10px;
        }
        .footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        @if($template->header_logo_path)
            <div style="text-align: center; width: 100%;">
                <img src="{{ public_path('storage/' . $template->header_logo_path) }}" style="width: 100%; height: auto;">
            </div>
        @else
            <table>
                <tr>
                    <td class="logo-left">
                        <h2>{{ $patient?->clinic?->name ?? (auth()->user()->clinic->name ?? 'CLINIC') }}</h2>
                    </td>
                    <td class="header-title">
                        MEDICAL RECORD
                    </td>
                    <td class="logo-right">
                        <!-- Second logo space if needed -->
                    </td>
                </tr>
            </table>
        @endif
    </div>

    <div class="content">
        <div class="document-title">
            {{ $template->name }}
        </div>

        <table class="patient-info">
            <tr>
                <th>Nama Pasien</th>
                <td>{{ $patient?->name ?? '-' }}</td>
            </tr>
            <tr>
                <th>No. Rekam Medis (MRN)</th>
                <td>{{ $patient?->medical_record_number ?? '-' }}</td>
            </tr>
            <tr>
                <th>Klinik / Cabang</th>
                <td>{{ $patient?->clinic?->name ?? (auth()->user()->clinic->name ?? '-') }}</td>
            </tr>
            <tr>
                <th>Tanggal Pemeriksaan</th>
                <td>{{ $date }}</td>
            </tr>
        </table>

        <div class="qr-section">
            <div class="qr-code">
                <img src="{{ $qrCodeBase64 }}" width="120" height="120">
            </div>
            <div class="verification-text">
                Scan QR Code to verify document authenticity.<br>
                Or visit: {{ $verifyUrl }}
            </div>
            <div class="document-number">
                Doc No: {{ $documentNumber }}
            </div>
        </div>
    </div>

    <div class="footer">
        @if($template->footer_logo_path)
            <div style="text-align: center; margin-bottom: 8px;">
                <img src="{{ public_path('storage/' . $template->footer_logo_path) }}" style="height: 40px; width: auto;">
            </div>
        @endif
        @if($template->disclaimer_text)
            <p>{{ $template->disclaimer_text }}</p>
        @else
            <p>This is a computer generated document and does not require a physical signature.</p>
        @endif
    </div>
</body>
</html>
