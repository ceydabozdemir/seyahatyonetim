<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Gider Raporu</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .chart-container {
            margin: 30px 0;
            text-align: center;
            page-break-inside: avoid; /* Prevents charts from breaking across pages */
        }
        .chart-container img {
            max-width: 520px;
            height: auto;
            display: block; /* Centers the image */
            margin: 0 auto;
        }
        .date-range {
            text-align: center;
            margin-bottom: 30px;
            padding: 10px;
            background-color: #f5f5f5;
            border-radius: 5px;
        }
        h1, h2 {
            font-family: 'DejaVu Sans', Arial, sans-serif;
        }
        h1 {
            font-size: 24px;
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }
        h2 {
            font-size: 18px;
            margin-bottom: 15px;
            color: #444;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        p {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 14px;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
<h1>Gider Grafik Raporu</h1>
<div class="date-range">
    <p><strong>Tarih Aralığı:</strong> {{ $startDate }} - {{ $endDate }}</p>
</div>

@php
    $chartTitles = [
        'monthlyChart' => 'Aylık Giderler',
        'yearlyChart' => 'Yıllık Giderler',
        'employeeChart' => 'Çalışan Giderleri',


    ];

    // Chart order based on time scale
    $chartOrder = [

        'monthlyChart',
        'yearlyChart',
        'employeeChart',

    ];
@endphp

@foreach ($chartOrder as $key)
    <div class="chart-container">
        <h2>{{ $chartTitles[$key] ?? $key }}</h2>
        @if (!empty($$key))
            <img src="{{ $$key }}" alt="{{ $chartTitles[$key] ?? $key }}">
        @else
            <p>Bu grafik için veri bulunamadı veya grafik oluşturulamadı.</p>
        @endif
    </div>
@endforeach

<div class="footer">
    <p>Rapor oluşturulma tarihi: {{ now()->format('d.m.Y H:i') }}</p>
</div>

</body>
</html>
