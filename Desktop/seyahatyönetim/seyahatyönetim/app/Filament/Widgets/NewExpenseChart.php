<?php

namespace App\Filament\Widgets;

use App\Models\Expense;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class NewExpenseChart extends ChartWidget
{
    protected static ?string $heading = 'Aylık Gider Yüzdelik Dağılımı';
    protected static ?string $maxHeight = '320px';

    public ?string $startDate = null;
    public ?string $endDate = null;

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getHeight(): string|int|null
    {
        return 300;
    }

    public function getData(): array
    {
        $chart = $this->generateChartData('percentage');

        return [
            'labels' => $chart['labels'],
            'datasets' => [[
                'label' => 'Yüzdelik Dağılım',
                'data' => $chart['data'],
                'backgroundColor' => $chart['colors'],
                'borderWidth' => 1,
            ]],
        ];
    }

    public function getPdfChartData(): array
    {
        $chart = $this->generateChartData('total');

        return [
            'labels' => $chart['labels'],
            'datasets' => [[
                'label' => 'Aylık Toplam Gider (TL)',
                'data' => $chart['data'],
                'backgroundColor' => $chart['colors'],
                'borderWidth' => 1,
            ]],
        ];
    }

    public function getTableData(): array
    {
        $chart = $this->generateChartData('total');

        $tableData = [];
        foreach ($chart['labels'] as $index => $month) {
            $tableData[] = [
                'month' => $month,
                'amount' => $chart['data'][$index] ?? 0,
            ];
        }

        return [
            'rows' => $tableData,
            'total' => array_sum($chart['data']),
        ];
    }

    protected function generateChartData(string $mode = 'percentage'): array
    {
        $year = Carbon::now()->year;

        $raw = Expense::selectRaw('MONTH(expense_date) as month, SUM(amount) as total')
            ->whereYear('expense_date', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $total = $raw->sum('total');

        $months = [
            'Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran',
            'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'
        ];

        $colors = [
            '#3b82f6', '#f59e0b', '#10b981', '#ef4444', '#8b5cf6', '#14b8a6',
            '#eab308', '#f43f5e', '#22d3ee', '#c084fc', '#6366f1', '#4ade80'
        ];

        $monthlyTotals = array_fill(0, 12, 0);
        foreach ($raw as $r) {
            $monthlyTotals[$r->month - 1] = $r->total;
        }

        $labels = [];
        $data = [];
        foreach ($monthlyTotals as $i => $value) {
            if ($value > 0) {
                $labels[] = $months[$i];
                $data[] = $mode === 'percentage' && $total > 0
                    ? round(($value / $total) * 100, 2)
                    : round($value, 2);
            }
        }

        if (empty($labels)) {
            return [
                'labels' => ['Veri Yok'],
                'data' => [100],
                'colors' => ['#cccccc'],
            ];
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'colors' => array_slice($colors, 0, count($data)),
        ];
    }
}
