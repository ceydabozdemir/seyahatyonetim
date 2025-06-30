<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class ExpenseReportController extends Controller
{
    private array $monthOrder = [
        'Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran',
        'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık',
    ];

    /**
     * Rapor oluşturma form sayfasını göster
     */
    public function index()
    {
        $users = User::whereNotNull('name')->pluck('name', 'id');
        return view('reports.expense-form', compact('users'));
    }

    /**
     * Seçilen parametrelere göre PDF raporu oluştur
     */
    public function generateReport(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:daily,weekly,monthly,yearly',
            'user_id' => 'nullable|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $reportType = $request->report_type;
        $userId = $request->user_id;
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        // Verileri hazırla
        $reportData = $this->prepareReportData($reportType, $userId, $startDate, $endDate);

        if (empty($reportData['labels'])) {
            return redirect()->back()->with('error', 'Seçilen tarih aralığında veya kullanıcı için gider verisi bulunamadı.');
        }

        // PDF oluştur
        $pdf = Pdf::loadView('reports.expense-report', [
            'reportData' => $reportData,
            'reportType' => $reportType,
            'startDate' => $startDate->format('d.m.Y'),
            'endDate' => $endDate->format('d.m.Y'),
            'user' => $userId ? User::find($userId) : null,
        ])->setOptions([
            'dpi' => 150,
            'defaultFont' => 'Arial',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ]);

        // PDF dosyası adı oluştur
        $fileName = 'gider-raporu';
        $fileName .= $userId ? '_' . str_slug(User::find($userId)->name) : '_tum-calisanlar';
        $fileName .= '_' . $reportType;
        $fileName .= '_' . now()->format('YmdHis') . '.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Rapor verilerini hazırla
     */
    private function prepareReportData($reportType, $userId, $startDate, $endDate)
    {
        $query = Expense::query()
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate);

        // Belirli bir kullanıcı seçilmişse filtrele
        if ($userId) {
            $query->where('user_id', $userId);
        }

        $dateFormat = $this->getDateFormatByReportType($reportType);
        $groupByFormat = 'date_group';

        // Rapor tipine göre SQL formatını ayarla
        switch ($reportType) {
            case 'daily':
                $query->selectRaw("DATE_FORMAT(created_at, '%Y-%m-%d') as date_group, expense_type, SUM(amount) as total");
                $groupByFormat = 'date_group';
                break;

            case 'weekly':
                $query->selectRaw("CONCAT(YEAR(created_at), '-', WEEK(created_at)) as date_group,
                                  CONCAT(DATE_FORMAT(DATE_SUB(created_at, INTERVAL WEEKDAY(created_at) DAY), '%d.%m.%Y'),
                                  ' - ',
                                  DATE_FORMAT(DATE_ADD(DATE_SUB(created_at, INTERVAL WEEKDAY(created_at) DAY), INTERVAL 6 DAY), '%d.%m.%Y'))
                                  as week_range, expense_type, SUM(amount) as total");
                $groupByFormat = 'week_range';
                break;

            case 'monthly':
                $query->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as date_group,
                                  DATE_FORMAT(created_at, '%M %Y') as month_name,
                                  expense_type, SUM(amount) as total");
                $groupByFormat = 'month_name';
                break;

            case 'yearly':
                $query->selectRaw("DATE_FORMAT(created_at, '%Y') as date_group, expense_type, SUM(amount) as total");
                $groupByFormat = 'date_group';
                break;
        }

        // Sorguyu gruplandır ve sonuçları al
        $results = $query->groupBy(['date_group', 'expense_type'])
            ->orderBy('date_group')
            ->get();

        // Tablo için veri yapısını oluştur
        $tableData = [];
        $uniqueDates = $results->pluck($groupByFormat)->unique()->values()->toArray();

        foreach ($uniqueDates as $date) {
            $row = ['date' => $date];
            $row['hotel'] = 0;
            $row['food'] = 0;
            $row['transport'] = 0;
            $row['total'] = 0;

            foreach ($results->where($groupByFormat, $date) as $result) {
                $row[$result->expense_type] = $result->total;
                $row['total'] += $result->total;
            }
            $tableData[] = $row;
        }

        // Grafik için veri yapısını oluştur
        $reportData = [
            'labels' => $uniqueDates,
            'datasets' => [
                'hotel' => ['label' => 'Konaklama', 'data' => []],
                'food' => ['label' => 'Yemek', 'data' => []],
                'transport' => ['label' => 'Ulaşım', 'data' => []],
            ],
            'totals' => [
                'hotel' => 0,
                'food' => 0,
                'transport' => 0,
                'overall' => 0,
            ],
            'tableData' => $tableData, // Tablo için veri
        ];

        // Her tarih için boş veri oluştur
        foreach ($uniqueDates as $date) {
            $reportData['datasets']['hotel']['data'][$date] = 0;
            $reportData['datasets']['food']['data'][$date] = 0;
            $reportData['datasets']['transport']['data'][$date] = 0;
        }

        // Verileri yerleştir
        foreach ($results as $result) {
            $date = $result->$groupByFormat;
            $type = $result->expense_type;
            $amount = $result->total;

            if (isset($reportData['datasets'][$type])) {
                $reportData['datasets'][$type]['data'][$date] = $amount;
                $reportData['totals'][$type] += $amount;
                $reportData['totals']['overall'] += $amount;
            }
        }

        // Her expense türü için verileri düzenli dizilere dönüştür
        foreach ($reportData['datasets'] as $type => $dataset) {
            $orderedData = [];
            foreach ($uniqueDates as $date) {
                $orderedData[] = $dataset['data'][$date];
            }
            $reportData['datasets'][$type]['data'] = $orderedData;
        }

        return $reportData;
    }

    /**
     * Rapor tipine göre tarih formatını al
     */
    private function getDateFormatByReportType($reportType)
    {
        switch ($reportType) {
            case 'daily':
                return '%d.%m.%Y';
            case 'weekly':
                return '%v. Hafta %Y';
            case 'monthly':
                return '%M %Y';
            case 'yearly':
                return '%Y';
            default:
                return '%d.%m.%Y';
        }
    }

    /**
     * Grafik URL'si oluştur
     */
    private function generateChartUrl($labels, $datasets, $title)
    {
        $config = [
            'type' => 'bar',
            'data' => [
                'labels' => $labels,
                'datasets' => array_values($datasets),
            ],
            'options' => [
                'plugins' => [
                    'legend' => [
                        'display' => true,
                        'position' => 'bottom',
                    ],
                    'title' => [
                        'display' => true,
                        'text' => $title,
                    ],
                ],
                'scales' => [
                    'y' => [
                        'beginAtZero' => true,
                    ],
                ],
            ],
        ];

        return 'data:image/png;base64,' . base64_encode(
                file_get_contents('https://quickchart.io/chart?c=' . urlencode(json_encode($config)))
            );
    }
}
