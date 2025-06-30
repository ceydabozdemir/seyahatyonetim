<?php

namespace App\Filament\Widgets;

use App\Models\Expense;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;

class BlogPostsChart extends ChartWidget
{
    protected static ?string $heading = 'Günlük Gider İstatistiği';

    protected int | string | array $columnSpan = 'full';

    protected static ?string $maxHeight = '800px';

    protected function getData(): array
    {
        // Laravel dilini Türkçe yap (sistem ayarı değilse bu şekilde manuel yapılabilir)
        App::setLocale('tr');

        $expenses = Expense::query()
            ->selectRaw('DATE(expense_date) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($expense) {
                $expense->date = Carbon::parse($expense->date)->translatedFormat('d F'); // Örn: 22 Nisan
                return $expense;
            });

        $labels = $expenses->pluck('date')->toArray();
        $data = $expenses->pluck('total')->toArray();

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Aylık Giderler',
                    'data' => $data,
                    'backgroundColor' => 'rgba(135, 206, 250, 0.6)',
                    'borderColor' => 'rgba(0, 191, 255, 1)',
                    'borderWidth' => 1,
                    'hoverBackgroundColor' => 'rgba(135, 206, 250, 0.8)',
                    'hoverBorderColor' => 'rgba(0, 191, 255, 1)',
                    'borderRadius' => 4,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): ?array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Toplam Gider (TL)',
                    ],
                ],
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Tarih',
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
                'tooltip' => [
                    'enabled' => true,
                ],
            ],
        ];
    }
}
