<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Export' }}</title>
    <style>
    body{
        font-family: Arial, Helvetica, sans-serif;
        margin:0;
    }

    .cover{
        position: relative;
        width: 100%;
        min-height: 100vh;
        padding: 60px 80px;
        box-sizing: border-box;
        page-break-after: always;
    }

    /* TITLE */
    .cover-title{
        text-align: center;
        font-size: 26px;
        font-weight: 700;
        margin-top: 20px;
    }

    /* PERIOD BLOCK */
    .period-block{
        margin-top: 50px;
    }

    .period-block .label{
        font-size: 13px;
        font-weight: 700;
        margin-bottom: 6px;
    }

    .period-block .period{
        font-size: 16px;
        font-weight: 700;
    }

    /* COMPANY CENTER */
    .company-center{
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 46px;
        font-weight: 800;
        letter-spacing: 2px;
    }

    /* FOOTER */
    .cover-footer{
        position: absolute;
        bottom: 60px;
        left: 80px;
    }

    .cover-footer .label{
        font-size: 12px;
        font-weight: 700;
    }

    .cover-footer .date{
        font-size: 12px;
        margin-top: 4px;
    }
    </style>

</head>
<body>
    @if(!empty($cover))
    <div class="cover">

        <div class="cover-title">
            {{ isset($title) ? strtoupper(trim(preg_replace('/\bExport\b/i','',$title))) : 'SALES REPORT' }}
        </div>

        <div class="period-block">
            <div class="label">PERIODE</div>
            <div class="period">
                {{ $cover['period'] ?? '' }}
            </div>
        </div>

        <div class="company-center">
            {{ $cover['company'] ?? 'UD KASEMI' }}
        </div>

        <div class="cover-footer">
            <div class="label">Tanggal Cetak</div>
            <div class="date">
                {{ $cover['exported_at'] ?? now()->format('d-m-Y H:i:s') }}
            </div>
        </div>

    </div>

    <div class="export-content">
        {!! $html !!}
        
        @php
            try {
                $exportedTs = isset($cover['exported_at']) ? \Carbon\Carbon::createFromFormat('d-m-Y H:i:s', $cover['exported_at'], config('app.timezone')) : now(config('app.timezone'));
            } catch (\Exception $e) {
                $exportedTs = now(config('app.timezone'));
            }
            $dayName = $exportedTs->locale('id')->translatedFormat('l');
            $ddmmyyyy = $exportedTs->format('d-m-Y');
            $userName = optional(auth()->user())->name ?: '';
        @endphp

        <div class="signature">
            <div class="signature-block">
                <div class="sig-location">{{ $dayName }}, {{ $ddmmyyyy }}</div>
                <div class="sig-space"></div>
                <div class="sig-name">{{ $userName }}</div>
            </div>
        </div>
    </div>
    @endif

    <style>
        /* ensure exported table starts on a new page after the cover and is visible */
        .export-content { page-break-before: always; padding: 18px 36px; }
        .export-content table { width:100%; border-collapse:collapse; }
        .export-content th, .export-content td { border:1px solid #222; padding:6px; }
        .signature { width:100%; display:flex; justify-content:flex-end; margin-top:28px; }
        .signature-block { width:260px; text-align:center; }
        .sig-space { height:70px; }
        .sig-name { font-weight:700; }
    </style>

    <script>
        // Auto-trigger print so user can save as PDF; don't close the window automatically to avoid blocking
        window.addEventListener('load', function(){ setTimeout(function(){ window.print(); }, 250); });
    </script>
</body>
</html>