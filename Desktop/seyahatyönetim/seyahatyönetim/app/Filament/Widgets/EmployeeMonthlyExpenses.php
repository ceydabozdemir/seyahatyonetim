<?php

namespace App\Filament\Widgets;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class EmployeeMonthlyExpenses extends ApexChartWidget
{
    protected static ?string $heading = 'Aylık Giderler (Çalışan & Ay Seçimli)';
    protected static ?int $sort = 1;

    public ?string $selectedEmployee = null;
    public ?string $selectedMonth = null;

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

        // Mevcut ayın başlangıç tarihi
        $currentMonthStart = now()->startOfMonth()->format('Y-m-d');

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

            DatePicker::make('selectedMonth')
                ->label('Ay Seçin')
                ->default($currentMonthStart)
                ->displayFormat('F Y') // Ay adı ve yıl formatı (Ocak 2025 gibi)
                ->reactive()
                ->required()
                ->afterStateUpdated(function ($state) {
                    // Seçilen tarihi ayın başlangıcına ayarla
                    $this->selectedMonth = Carbon::parse($state)->startOfMonth()->format('Y-m-d');
                    $this->updateOptions();
                }),
        ];
    }

    public function updateOptions(): void
    {
        $this->dispatch('update-employee-monthly-expenses-chart', chartId: $this->getId());
    }

    protected function getOptions(): array
    {
        if (!$this->selectedEmployee || !$this->selectedMonth) {
            return [
                'chart' => [
                    'type' => 'bar',
                    'height' => 300,
                ],
                'series' => [],
                'xaxis' => ['categories' => []],
                'noData' => [
                    'text' => 'Lütfen çalışan ve ay seçin.',
                    'align' => 'center',
                    'verticalAlign' => 'middle',
                ],
            ];
        }

        // Seçilen ayın başlangıç ve bitiş tarihlerini hesapla
        $monthStart = Carbon::parse($this->selectedMonth);
        $monthEnd = (clone $monthStart)->endOfMonth();

        // Ay adını al
        $monthName = $monthStart->locale('tr')->translatedFormat('F Y');

        // Gider türleri
        $categories = [
            'hotel' => 'Konaklama',
            'food' => 'Yemek',
            'transport' => 'Ulaşım',
        ];

        // Veritabanından çalışan için seçilen aydaki giderleri çek
        $rawData = DB::table('expenses')
            ->selectRaw('expense_type, SUM(amount) as total')
            ->where('user_id', $this->selectedEmployee)
            ->whereBetween('expense_date', [$monthStart->format('Y-m-d 00:00:00'), $monthEnd->format('Y-m-d 23:59:59')])
            ->groupBy('expense_type')
            ->get();

        // Veri yapısını hazırla - tek seri, tüm kategoriler
        $categoryLabels = array_values($categories);
        $categoryValues = [];

        foreach ($categories as $typeKey => $typeLabel) {
            $record = $rawData->first(function ($item) use ($typeKey) {
                return strtolower(trim($item->expense_type)) === $typeKey;
            });

            $categoryValues[] = $record ? (float) $record->total : 0;
        }

        // Aylık toplam giderleri hesapla
        $totalMonthlyExpense = array_sum($categoryValues);

        Log::info('📅 Aylık Gider Grafiği', [
            'employee' => $this->selectedEmployee,
            'month' => $monthName,
            'month_start' => $monthStart->format('Y-m-d'),
            'month_end' => $monthEnd->format('Y-m-d'),
            'categories' => $categoryLabels,
            'values' => $categoryValues,
            'total' => $totalMonthlyExpense,
            'raw' => $rawData->toArray(),
        ]);

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 350,
            ],
            'series' => [
                [
                    'name' => 'Tutar',
                    'data' => $categoryValues,
                ],
            ],
            'xaxis' => [
                'categories' => $categoryLabels,
            ],
            'title' => [
                'text' => $monthName . ' Ayı Giderleri - Toplam: ' . number_format($totalMonthlyExpense, 2, ',', '.') . ' ₺',
            ],
        ];
    }
}
