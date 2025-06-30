<?php

namespace App\Filament\Widgets;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class EmployeeWeeklyExpenses extends ApexChartWidget
{
    protected static ?string $heading = 'Haftalık Giderler (Çalışan & Hafta Seçimli)';
    protected static ?int $sort = 1;

    public ?string $selectedEmployee = null;
    public ?string $selectedWeekStart = null;

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

        // Mevcut haftanın başlangıç tarihi (Pazartesi)
        $currentWeekStart = now()->startOfWeek()->format('Y-m-d');

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

            DatePicker::make('selectedWeekStart')
                ->label('Hafta Başlangıcı Seçin')
                ->default($currentWeekStart)
                ->reactive()
                ->required()
                ->afterStateUpdated(function ($state) {
                    // Seçilen tarihi haftanın başlangıcına (Pazartesi) ayarla
                    $this->selectedWeekStart = Carbon::parse($state)->startOfWeek()->format('Y-m-d');
                    $this->updateOptions();
                }),
        ];
    }

    public function updateOptions(): void
    {
        $this->dispatch('update-employee-weekly-expenses-chart', chartId: $this->getId());
    }

    protected function getOptions(): array
    {
        if (!$this->selectedEmployee || !$this->selectedWeekStart) {
            return [
                'chart' => ['type' => 'bar', 'height' => 300],
                'series' => [],
                'xaxis' => ['categories' => []],
                'noData' => [
                    'text' => 'Lütfen çalışan ve hafta seçin.',
                    'align' => 'center',
                    'verticalAlign' => 'middle',
                ],
            ];
        }

        // Seçilen haftanın başlangıç ve bitiş tarihlerini hesapla
        $weekStart = Carbon::parse($this->selectedWeekStart);
        $weekEnd = (clone $weekStart)->addDays(6); // Pazartesi - Pazar arası 7 gün

        // Hafta içindeki tüm günlerin tarihlerini (Y-m-d formatında) al
        $weekDays = collect();
        for($i = 0; $i < 7; $i++) {
            $weekDays->push((clone $weekStart)->addDays($i)->format('Y-m-d'));
        }

        // Gider türleri
        $categories = [
            'hotel' => 'Konaklama',
            'food' => 'Yemek',
            'transport' => 'Ulaşım',
        ];

        // Veritabanından çalışan için seçilen haftadaki giderleri çek
        $rawData = DB::table('expenses')
            ->selectRaw('expense_type, DATE(expense_date) as date, SUM(amount) as total')
            ->where('user_id', $this->selectedEmployee)
            ->whereBetween('expense_date', [$weekStart->format('Y-m-d 00:00:00'), $weekEnd->format('Y-m-d 23:59:59')])
            ->groupBy('expense_type', 'date')
            ->get();

        // Her gider türü için verileri işle
        $series = [];
        foreach ($categories as $typeKey => $typeLabel) {
            $weeklyData = [];

            // Haftanın her günü için veri ekle
            foreach ($weekDays as $day) {
                $record = $rawData->first(function ($item) use ($typeKey, $day) {
                    return strtolower(trim($item->expense_type)) === $typeKey && $item->date === $day;
                });

                $weeklyData[] = $record ? (float) $record->total : 0;
            }

            $series[] = [
                'name' => $typeLabel,
                'data' => $weeklyData,
            ];
        }

        // Günleri etiketler olarak formatla (Örn: "Pzt 12.05" şeklinde)
        $formattedDays = $weekDays->map(function ($day) {
            $date = Carbon::parse($day);
            $dayName = mb_substr($date->locale('tr')->dayName, 0, 3); // Türkçe gün ismi (ilk 3 harf)
            return $dayName . ' ' . $date->format('d.m');
        })->toArray();

        Log::info('📅 Haftalık Gider Grafiği', [
            'employee' => $this->selectedEmployee,
            'week_start' => $weekStart->format('Y-m-d'),
            'week_end' => $weekEnd->format('Y-m-d'),
            'series' => $series,
            'raw' => $rawData->toArray(),
        ]);

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 350,
            ],
            'xaxis' => [
                'categories' => $formattedDays,
                'title' => ['text' => 'Günler'],
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
            'title' => [
                'text' => $weekStart->format('d.m.Y') . ' - ' . $weekEnd->format('d.m.Y') . ' arası giderler',
                'align' => 'center',
            ],
        ];
    }
}
