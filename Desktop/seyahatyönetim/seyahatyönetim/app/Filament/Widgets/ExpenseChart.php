<?php

namespace App\Filament\Widgets;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use App\Models\Expense;
use Carbon\Carbon;

class ExpenseChart extends ApexChartWidget
{
    protected static ?string $heading = 'Yıllık Gider Grafiği';
    protected static ?string $maxHeight = '50px';

    protected function getOptions(): array
    {
        // Geçerli yılı alıyoruz
        $currentYear = Carbon::now()->year;

        // Veritabanından yalnızca geçerli yılın giderlerini çekiyoruz
        $expenses = Expense::selectRaw('MONTH(expense_date) as month, SUM(amount) as total')
            ->whereYear('expense_date', $currentYear)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Aylar için dinamik etiketler (Ocak'tan Aralık'a kadar)
        $months = [
            'Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran',
            'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'
        ];

        // Aylar ve gider verisi eksik olabilir, bu yüzden her ay için 0 veri ekliyoruz
        $monthlyExpenses = array_map(function ($monthIndex) use ($expenses) {
            return $expenses[$monthIndex + 1] ?? 0; // Eğer o ayda veri yoksa 0 ekle
        }, array_keys($months));

        // Grafik seçenekleri (Livewire tarafından desteklenen basit bir dizi)
        return [
            'chart' => [
                'type' => 'line',
                'height' => 350,
            ],
            'series' => [
                [
                    'name' => 'Toplam Gider',
                    'data' => $monthlyExpenses, // Dinamik veri
                ],
            ],
            'xaxis' => [
                'categories' => $months, // Dinamik aylar
            ],
            'yaxis' => [
                'title' => [
                    'text' => 'TL',
                ],
            ],
            'colors' => ['#3b82f6'], // Çizgi rengi
            'stroke' => [
                'width' => 3, // Çizgi kalınlığı
            ],
        ];
    }
}
