<?php

namespace App\Filament\Widgets;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class EmployeeDailyExpenses extends ApexChartWidget
{
    protected static ?string $heading = 'Günlük Giderler (Çalışan & Tarih Seçimli)';
    protected static ?int $sort = 1;
    protected static ?string $maxHeight = '322px';

    public ?string $selectedEmployee = null;
    public ?string $selectedDate = null;

    public static function isLazy(): bool
    {
        return false;
    }

    protected function getFormSchema(): array
    {
        $users = User::query()
            ->select('id', 'name')
            ->whereNotNull('name')
            ->pluck('name', 'id')
            ->toArray();

        return [
            Select::make('selectedEmployee')
                ->label('Çalışan Seçin')
                ->options($users)
                ->searchable()
                ->reactive()
                ->required()
                ->default(array_key_first($users))
                ->afterStateUpdated(function ($state) {
                    $this->selectedEmployee = $state;
                    $this->updateOptions();
                }),

            DatePicker::make('selectedDate')
                ->label('Tarih Seçin')
                ->default(today())
                ->reactive()
                ->required()
                ->afterStateUpdated(function ($state) {
                    $this->selectedDate = $state;
                    $this->updateOptions();
                }),
        ];
    }

    public function updateOptions(): void
    {
        $this->dispatch('update-employee-daily-expenses-chart', chartId: $this->getId());
    }

    protected function getOptions(): array
    {
        if (!$this->selectedEmployee || !$this->selectedDate) {
            return [
                'chart' => ['type' => 'bar', 'height' => 300],
                'series' => [],
                'xaxis' => ['categories' => []],
                'noData' => [
                    'text' => 'Lütfen çalışan ve tarih seçin.',
                    'align' => 'center',
                    'verticalAlign' => 'middle',
                ],
            ];
        }

        $selectedDay = Carbon::parse($this->selectedDate)->format('Y-m-d');

        $rawData = DB::table('expenses')
            ->selectRaw('expense_type, SUM(amount) as total')
            ->where('user_id', $this->selectedEmployee)
            ->whereDate('expense_date', $selectedDay)
            ->groupBy('expense_type')
            ->get();

        $categories = [
            'hotel' => 'Konaklama',
            'food' => 'Yemek',
            'transport' => 'Ulaşım',
        ];

        $series = [];

        foreach ($categories as $typeKey => $typeLabel) {
            $record = $rawData->first(function ($item) use ($typeKey) {
                return strtolower(trim($item->expense_type)) === $typeKey;
            });

            $series[] = [
                'name' => $typeLabel,
                'data' => [$record ? (float) $record->total : 0],
            ];
        }

        Log::info('📅 Tek Günlük Gider Grafiği', [
            'employee' => $this->selectedEmployee,
            'date' => $selectedDay,
            'series' => $series,
            'raw' => $rawData->toArray(),
        ]);

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 350,
            ],
            'xaxis' => [
                'categories' => [$selectedDay],
                'title' => ['text' => 'Tarih'],
            ],
            'yaxis' => [
                'title' => ['text' => 'Tutar (₺)'],
            ],
            'series' => $series,
            'plotOptions' => [
                'bar' => [
                    'horizontal' => false,
                    'columnWidth' => '55%',
                ],
            ],
            'dataLabels' => ['enabled' => false],
            'tooltip' => [
                'y' => [
                    'formatter' => fn($val) => number_format($val, 2, ',', '.') . ' ₺',
                ],
            ],
        ];
    }
}
