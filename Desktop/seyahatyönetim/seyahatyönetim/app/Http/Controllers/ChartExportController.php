<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Filament\Widgets\BlogPostsChart;
use App\Filament\Widgets\ExpenseChart;
use App\Filament\Widgets\EmployeeExpensesChart;
use App\Filament\Widgets\NewExpenseChart;

class ChartExportController extends Controller
{
    public function download(Request $request): Response
    {
        $startDate = $request->input('start_date', '2025-01-01');
        $endDate = $request->input('end_date', now()->toDateString());

        $widgets = [
            'monthlyChart' => new NewExpenseChart(), // Tablo olarak kullanılacak
            'yearlyChart' => new ExpenseChart(),
            'employeeChart' => new EmployeeExpensesChart(),
            'percentageChart' => new NewExpenseChart(),
        ];

        $charts = [];
        $monthlyData = [];

        foreach ($widgets as $key => $widget) {
            try {
                if ($key === 'monthlyChart') {
                    if (method_exists($widget, 'getTableData')) {
                        $monthlyData = $widget->getTableData();
                    }
                    continue;
                }

                if (method_exists($widget, 'getPdfChartData')) {
                    $chartData = $widget->getPdfChartData();
                    $type = method_exists($widget, 'getType') ? $widget->getType() : 'bar';
                    $title = method_exists($widget, 'getHeading') ? $widget::getHeading() : null;

                    $chartUrl = $key === 'percentageChart'
                        ? $this->generatePercentageChartUrl($chartData, $title)
                        : $this->generateChartUrlFromRaw($chartData, $type, $title);
                } else {
                    $chartUrl = $this->generateChartUrl($widget);
                }

                $charts[$key] = $this->getBase64ChartImage($chartUrl);
            } catch (\Exception $e) {
                \Log::error("Error generating chart for $key: " . $e->getMessage());
                $charts[$key] = null;
            }
        }

        $pdf = Pdf::loadView('pdf.expense-charts', array_merge($charts, [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'monthlyData' => $monthlyData,
        ]));

        return $pdf->download('tum-grafik-raporu.pdf');
    }

    protected function generatePercentageChartUrl(array $data, ?string $title = null): string
    {
        $config = [
            'type' => 'bar',
            'data' => $data,
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'legend' => ['display' => true, 'position' => 'bottom'],
                    'title' => ['display' => !empty($title), 'text' => $title],
                    'tooltip' => ['enabled' => true],
                ],
                'scales' => [
                    'y' => [
                        'beginAtZero' => true,
                        'ticks' => [
                            'callback' => "value => value.toLocaleString('tr-TR') + ' ₺'"
                        ]
                    ],
                    'x' => ['grid' => ['display' => false]]
                ]
            ]
        ];

        return 'https://quickchart.io/chart?c=' . urlencode(json_encode($config)) . '&width=600&height=400';
    }

    protected function generateChartUrlFromRaw(array $data, string $type = 'bar', ?string $title = null): string
    {
        $config = [
            'type' => $type,
            'data' => $data,
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'legend' => ['display' => true, 'position' => 'bottom'],
                    'title' => ['display' => !empty($title), 'text' => $title],
                    'tooltip' => ['enabled' => true],
                ],
            ],
        ];

        if (in_array($type, ['bar', 'line'])) {
            $config['options']['scales'] = [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => "value => value.toLocaleString('tr-TR')"
                    ]
                ]
            ];
        }

        return 'https://quickchart.io/chart?c=' . urlencode(json_encode($config)) . '&width=600&height=400';
    }

    protected function generateChartUrl($chartWidget): string
    {
        $chartData = $chartWidget->getData();
        $type = $chartWidget->getType();
        $title = $chartWidget::getHeading();

        return $this->generateChartUrlFromRaw([
            'labels' => $chartData['labels'],
            'datasets' => $chartData['datasets'],
        ], $type, $title);
    }

    protected function getBase64ChartImage(string $url): ?string
    {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $imageData = curl_exec($ch);
            curl_close($ch);

            if (!$imageData) return null;
            return 'data:image/png;base64,' . base64_encode($imageData);
        } catch (\Exception $e) {
            \Log::error("Error fetching chart image: " . $e->getMessage());
            return null;
        }
    }
}
