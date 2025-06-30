<?php

namespace App\Filament\Widgets;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Filament\Forms\Components\Select;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class EmployeeYearlyExpenses extends ApexChartWidget
{
    protected static ?string $heading = 'Yıllık Giderler (Çalışan & Yıl Seçimli)';
    protected static ?int $sort = 1;

    public ?string $selectedEmployee = null;
    public ?string $selectedYear = null;

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

        // Mevcut yıl ve son 5 yıl için seçenekler oluştur
        $currentYear = now()->year;
        $years = [];
        for ($i = 0; $i <= 5; $i++) {
            $year = $currentYear - $i;
            $years[$year] = (string) $year;
        }

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

            Select::make('selectedYear')
                ->label('Yıl Seçin')
                ->options($years)
                ->reactive()
                ->required()
                ->default($currentYear)
                ->afterStateUpdated(function ($state) {
                    $this->selectedYear = $state;
                    $this->updateOptions();
                }),
        ];
    }

    public function updateOptions(): void
    {
        $this->dispatch('update-employee-yearly-expenses-chart', chartId: $this->getId());
    }

    protected function getOptions(): array
    {
        if (!$this->selectedEmployee || !$this->selectedYear) {
            return [
                'chart' => [
                    'type' => 'bar',
                    'height' => 300,
                ],
                'series' => [],
                'xaxis' => ['categories' => []],
                'noData' => [
                    'text' => 'Lütfen çalışan ve yıl seçin.',
                    'align' => 'center',
                    'verticalAlign' => 'middle',
                ],
            ];
        }

        // Seçilen yılın başlangıç ve bitiş tarihlerini hesapla
        $yearStart = Carbon::createFromDate($this->selectedYear, 1, 1)->startOfYear();
        $yearEnd = Carbon::createFromDate($this->selectedYear, 12, 31)->endOfYear();

        // Aylık veriler için
        $months = [];
        $monthNames = [];

        // Yılın her ayı için
        for ($month = 1; $month <= 12; $month++) {
            $date = Carbon::createFromDate($this->selectedYear, $month, 1);
            $months[] = $date->format('m');
            $monthNames[] = $date->locale('tr')->translatedFormat('F'); // Türkçe ay adları
        }

        // Gider türleri
        $categories = [
            'hotel' => 'Konaklama',
            'food' => 'Yemek',
            'transport' => 'Ulaşım',
        ];

        // Veritabanından çalışan için seçilen yıldaki aylık giderleri çek
        $rawData = DB::table('expenses')
            ->selectRaw('expense_type, MONTH(expense_date) as month, SUM(amount) as total')
            ->where('user_id', $this->selectedEmployee)
            ->whereBetween('expense_date', [$yearStart->format('Y-m-d 00:00:00'), $yearEnd->format('Y-m-d 23:59:59')])
            ->groupBy('expense_type', 'month')
            ->get();

        // Her gider türü için aylık verileri hazırla
        $series = [];
        foreach ($categories as $typeKey => $typeLabel) {
            $monthlyData = [];

            // Yılın her ayı için veri ekle
            foreach ($months as $month) {
                $record = $rawData->first(function ($item) use ($typeKey, $month) {
                    return strtolower(trim($item->expense_type)) === $typeKey && $item->month == $month;
                });

                $monthlyData[] = $record ? (float) $record->total : 0;
            }

            $series[] = [
                'name' => $typeLabel,
                'data' => $monthlyData,
            ];
        }

        // Yıllık toplam giderleri hesapla
        $totalYearlyExpense = 0;
        foreach ($series as $serie) {
            $totalYearlyExpense += array_sum($serie['data']);
        }

        Log::info('📅 Yıllık Gider Grafiği', [
            'employee' => $this->selectedEmployee,
            'year' => $this->selectedYear,
            'months' => $monthNames,
            'series' => $series,
            'total' => $totalYearlyExpense,
            'raw' => $rawData->toArray(),
        ]);

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 350,
                'stacked' => true,
                'toolbar' => [
                    'show' => true,
                    'tools' => [
                        'download' => true,
                        'selection' => true,
                        'zoom' => true,
                        'zoomin' => true,
                        'zoomout' => true,
                        'pan' => true,
                    ],
                ],
            ],
            'plotOptions' => [
                'bar' => [
                    'horizontal' => false,
                    'columnWidth' => '55%',
                ],
            ],
            'series' => $series,
            'xaxis' => [
                'categories' => $monthNames,
                'title' => [
                    'text' => 'Aylar',
                ],
            ],
            'yaxis' => [
                'title' => [
                    'text' => 'Tutar (₺)',
                ],
            ],
            'tooltip' => [
                'y' => [
                    'formatter' => 'function(val) { return new Intl.NumberFormat("tr-TR", { style: "currency", currency: "TRY" }).format(val) }',
                ],
            ],
            'title' => [
                'text' => $this->selectedYear . ' Yılı Giderleri - Toplam: ' . number_format($totalYearlyExpense, 2, ',', '.') . ' ₺',
            ],
            'legend' => [
                'position' => 'top',
            ],
        ];
    }
}
