<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Gider Raporu</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
    </style>
</head>
<body>
<h2>Gider Raporu (Aylık Toplam)</h2>

<table>
    <thead>
    <tr>
        <th>Ay</th>
        <th>Toplam Gider (₺)</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($expenses as $expense)
        <tr>
            <td>{{ \Carbon\Carbon::parse($expense->month . '-01')->translatedFormat('F Y') }}</td>
            <td>{{ number_format($expense->total, 2, ',', '.') }} ₺</td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
