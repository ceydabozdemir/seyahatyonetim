<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Grafik Raporu</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            padding: 20px;
        }
        .chart {
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
<h2>Grafik Raporu</h2>

<div class="chart">
    <h3>Genel Gider Grafiği</h3>
    {!! $charts['expenseChart'] !!}
</div>

<div class="chart">
    <h3>Yeni Gider Grafiği</h3>
    {!! $charts['newExpenseChart'] !!}
</div>

<div class="chart">
    <h3>Çalışan Gider Grafiği</h3>
    {!! $charts['employeeExpensesChart'] !!}
</div>

<div class="chart">
    <h3>Blog Gönderi Grafiği</h3>
    {!! $charts['blogPostChart'] !!}
</div>
</body>
</html>
