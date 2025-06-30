{{-- resources/views/exports/dashboard-charts.blade.php --}}
    <!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Grafik Raporu</title>
    <style>
        body { font-family: sans-serif; }
        .chart { margin-bottom: 30px; }
    </style>
</head>
<body>
<h1>Dashboard Grafik Raporu</h1>

<div class="chart">
    <h2>Blog Gönderi Grafiği</h2>
    {!! app(\App\Filament\Widgets\BlogPostChart::class)->render() !!}
</div>

<div class="chart">
    <h2>Çalışan Gider Grafiği</h2>
    {!! app(\App\Filament\Widgets\EmployeeExpensesChart::class)->render() !!}
</div>

<div class="chart">
    <h2>Gider Dağılım Grafiği</h2>
    {!! app(\App\Filament\Widgets\ExpenseChart::class)->render() !!}
</div>

<div class="chart">
    <h2>Aylık Yüzdelik Gider Grefiği</h2>
    {!! app(\App\Filament\Widgets\NewExpenseChart::class)->render() !!}
</div>
</body>
</html>
