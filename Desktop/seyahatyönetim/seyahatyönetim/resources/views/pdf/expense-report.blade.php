<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Gider Raporu</title>
    <style>
        @font-face {
            font-family: 'DejaVu Sans';
            font-style: normal;
            font-weight: normal;
            src: url("{{ public_path('fonts/DejaVuSans.ttf') }}") format('truetype');
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
            line-height: 1.5;
        }
        .date-range {
            text-align: center;
            margin-bottom: 30px;
            padding: 10px;
            background-color: #f5f5f5;
            border-radius: 5px;
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
        table {
            margin: 0 auto;
            border-collapse: collapse;
            width: 80%;
            font-size: 12px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        .no-data {
            color: #e74c3c;
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>
<body>

<h1>Gider Raporu</h1>

<div class="date-range">
    <p><strong>Tarih Aralığı:</strong> {{ $startDate }} - {{ $endDate }}</p>
    <p><strong>Çalışan:</strong> {{ $user ? $user->name : 'Tüm Çalışanlar' }}</p>
</div>

<h2>Gider Detayları</h2>
@if ($expenses->isNotEmpty())
    <table>
        <thead>
        <tr>
            <th>No</th>
            <th>Tarih</th>
            <th>Çalışan</th>
            <th>Gider Türü</th>
            <th>Tutar (TL)</th>
            <th>Tutar (USD)</th>
            <th>Tutar (EUR)</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($expenses as $expense)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ Carbon\Carbon::parse($expense->expense_date)->setTimezone('Europe/Istanbul')->format('d.m.Y') }}</td>
                <td>{{ $expense->user->name ?? 'Bilinmeyen' }}</td>
                <td>{{ ucfirst($expense->expense_type) }}</td>
                <td>{{ number_format($expense->amount, 2, ',', '.') }}</td>
                <td>{{ $expense->amount_converted_usd ? number_format($expense->amount_converted_usd, 2, ',', '.') : '-' }}</td>
                <td>{{ $expense->amount_converted_eur ? number_format($expense->amount_converted_eur, 2, ',', '.') : '-' }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="4"><strong>Toplam</strong></td>
            <td><strong>{{ number_format($totalAmount, 2, ',', '.') }} TL</strong></td>
            <td><strong>{{ number_format($totalAmountUsd, 2, ',', '.') }} USD</strong></td>
            <td><strong>{{ number_format($totalAmountEur, 2, ',', '.') }} EUR</strong></td>
        </tr>
        </tbody>
    </table>
@else
    <p class="no-data">Gider verisi bulunamadı.</p>
@endif

<div class="footer">
    <p>Rapor oluşturulma tarihi: {{ now()->setTimezone('Europe/Istanbul')->format('d.m.Y H:i') }}</p>
</div>

</body>
</html>
