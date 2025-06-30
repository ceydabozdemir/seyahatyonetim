<?php

namespace App\Filament\Widgets;

use Filament\Widgets\BarChartWidget;
use App\Models\User;
use Carbon\Carbon;

class EmployeeExpensesChart extends BarChartWidget
{
    protected static ?string $heading = 'Çalışan Bazlı Giderler';
    protected static ?string $minHeight = '500px';

    protected function getData(): array
    {
        $employees = User::with('giderler')->get();
        $colors = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'];

        $data = [];
        $labels = [];

        foreach ($employees as $employee) {
            $totalExpense = $employee->giderler()
                ->whereBetween('expense_date', [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()])
                ->sum('amount');

            $data[] = $totalExpense ?: 0;
            $labels[] = $employee->name;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Toplam Gider',
                    'data' => $data,
                    'backgroundColor' => array_slice($colors, 0, count($data)),
                    'borderColor' => array_slice($colors, 0, count($data)),
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
            'options' => [
                'scales' => [
                    'y' => [
                        'beginAtZero' => true,
                    ],
                ],
            ],
        ];
    }
}
