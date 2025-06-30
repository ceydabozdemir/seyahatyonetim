<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Arac;
use App\Models\User;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    private array $monthOrder = [
        'Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran',
        'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık',
    ];

    public function index()
    {
        $expenses = Expense::with('user')->latest()->paginate(10);
        return view('expenses.index', compact('expenses'));
    }

    public function create()
    {
        $aracs = Arac::all();
        return view('expenses.create', compact('aracs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'expense_type' => 'required|in:hotel,food,transport',
            'accommodation_place' => 'required_if:expense_type,hotel|string|max:255',
            'accommodation_cost' => 'required_if:expense_type,hotel|numeric|min:0',
            'restaurant_name' => 'required_if:expense_type,food|string|max:255',
            'meal_cost' => 'required_if:expense_type,food|numeric|min:0',
            'transportation_vehicle' => 'required_if:expense_type,transport|exists:araclar,ad',
            'kilometers' => 'required_if:expense_type,transport|numeric|min:0',
            'expense_date' => 'required|date',
            'invoice_photo' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $amount = 0;
        if ($request->expense_type === 'hotel') {
            $amount = $request->accommodation_cost;
        } elseif ($request->expense_type === 'food') {
            $amount = $request->meal_cost;
        } elseif ($request->expense_type === 'transport') {
            $amount = (new Expense)->calculateTransportCost($request->kilometers, $request->transportation_vehicle);
            if ($amount <= 0) {
                return back()->withErrors(['transportation_vehicle' => 'Ulaşım maliyeti hesaplanamadı.']);
            }
        }

        // USD ve EUR dönüşümleri (örnek: sabit kurlar, gerçekte bir API kullanılabilir)
        $usdRate = 33.00; // Örnek: 1 USD = 33 TL
        $eurRate = 35.50; // Örnek: 1 EUR = 35.5 TL
        $amountUsd = $amount / $usdRate;
        $amountEur = $amount / $eurRate;

        Expense::create([
            'user_id' => auth()->id(),
            'expense_type' => $request->expense_type,
            'accommodation_place' => $request->accommodation_place,
            'accommodation_cost' => $request->accommodation_cost,
            'restaurant_name' => $request->restaurant_name,
            'meal_cost' => $request->meal_cost,
            'transportation_vehicle' => $request->transportation_vehicle,
            'kilometers' => $request->kilometers,
            'amount' => $amount,
            'amount_converted_usd' => $amountUsd,
            'amount_converted_eur' => $amountEur,
            'expense_date' => Carbon::parse($request->expense_date)->setTimezone('Europe/Istanbul'),
            'invoice_photo' => $request->file('invoice_photo') ? $request->file('invoice_photo')->store('receipts', 'public') : null,
        ]);

        return redirect()->route('expenses.index')->with('success', 'Gider başarıyla kaydedildi.');
    }

    public function getVehicleFuelConsumption(Request $request)
    {
        $vehicleName = $request->query('vehicle_name');
        $kilometers = $request->query('kilometers', 0);

        $arac = Arac::where('ad', $vehicleName)->first();
        if (!$arac) {
            return response()->json(['error' => 'Araç bulunamadı'], 404);
        }

        $amount = (new Expense)->calculateTransportCost($kilometers, $vehicleName);

        return response()->json([
            'yakit_tuketimi' => $arac->yakit_tuketimi,
            'calculated_amount' => $amount,
        ]);
    }

    private function getFilteredExpenses($startDate = null, $endDate = null, $userId = null)
    {
        $query = Expense::with('user');

        if ($startDate) {
            $start = Carbon::parse($startDate)->startOfDay()->setTimezone('Europe/Istanbul');
            $query->where('expense_date', '>=', $start);
        }

        if ($endDate) {
            $end = Carbon::parse($endDate)->endOfDay()->setTimezone('Europe/Istanbul');
            $query->where('expense_date', '<=', $end);
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        return $query->get();
    }

    public function downloadReport(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $userId = $request->input('user_id');

        // Filtrelenmiş giderleri al
        $expenses = $this->getFilteredExpenses($startDate, $endDate, $userId);
        $totalAmount = $expenses->sum('amount');
        $totalAmountUsd = $expenses->sum('amount_converted_usd');
        $totalAmountEur = $expenses->sum('amount_converted_eur');

        if ($expenses->isEmpty()) {
            return redirect()->back()->with('error', 'Belirtilen kriterlere uygun gider verisi bulunamadı.');
        }

        // User bilgisini al
        $user = $userId ? User::find($userId) : null;

        // Tarih formatını düzenle
        $formattedStartDate = $startDate ? Carbon::parse($startDate)->setTimezone('Europe/Istanbul')->format('d.m.Y') : 'Başlangıç';
        $formattedEndDate = $endDate ? Carbon::parse($endDate)->setTimezone('Europe/Istanbul')->format('d.m.Y') : now()->setTimezone('Europe/Istanbul')->format('d.m.Y');

        $pdf = Pdf::loadView('pdf.expense-report', [
            'expenses' => $expenses,
            'totalAmount' => $totalAmount,
            'totalAmountUsd' => $totalAmountUsd,
            'totalAmountEur' => $totalAmountEur,
            'startDate' => $formattedStartDate,
            'endDate' => $formattedEndDate,
            'user' => $user,
        ])->setOptions([
            'dpi' => 150,
            'defaultFont' => 'Arial',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ]);

        return $pdf->download('gider-raporu-' . now()->format('Y-m-d') . '.pdf');
    }

    public function downloadCharts()
    {
        $expenses = Expense::with('user')->get();

        if ($expenses->isEmpty()) {
            return redirect()->back()->with('error', 'Gider verisi bulunamadı. Lütfen önce gider kaydedin.');
        }

        $monthlyData = $expenses->groupBy(fn($e) => Carbon::parse($e->expense_date)->locale('tr')->translatedFormat('F'))
            ->map(function ($group) {
                return [
                    'label' => $group->first()->expense_date->locale('tr')->translatedFormat('F'),
                    'amount' => $group->sum('amount'),
                ];
            })->sortBy(fn($value) => array_search($value['label'], $this->monthOrder))
            ->values();

        $yearlyData = $expenses->groupBy(fn($e) => $e->expense_date->year)->map->sum('amount');

        $employeeData = $expenses->groupBy('user_id')->map(function ($group) {
            return [
                'name' => $group->first()->user->name ?? 'Bilinmeyen',
                'amount' => $group->sum('amount'),
            ];
        })->values();

        $totalAmount = $expenses->sum('amount');
        $categoryData = $totalAmount > 0
            ? $expenses->groupBy('expense_type')->map->sum('amount')
                ->map(fn($value) => round(($value / $totalAmount) * 100, 2))
            : [];

        $charts = [
            'monthlyChart' => $this->generateChartUrl('bar', $monthlyData->pluck('label')->toArray(), $monthlyData->pluck('amount')->toArray(), 'Aylık Giderler'),
            'yearlyChart' => $this->generateChartUrl('line', $yearlyData->keys()->toArray(), $yearlyData->values()->toArray(), 'Yıllık Giderler'),
            'employeeChart' => $this->generateChartUrl('bar', $employeeData->pluck('name')->toArray(), $employeeData->pluck('amount')->toArray(), 'Çalışan Bazlı Giderler'),
            'percentageChart' => $this->generatePieChartUrl($categoryData->keys()->toArray(), $categoryData->values()->toArray()),
        ];

        $pdf = Pdf::loadView('pdf.expense-charts-with-date-range', [
            'startDate' => 'Tüm Zamanlar',
            'endDate' => now()->format('d.m.Y'),
            'monthlyData' => [
                'rows' => $monthlyData,
                'total' => $totalAmount,
            ],
            ...$charts
        ])->setOptions([
            'dpi' => 150,
            'defaultFont' => 'Arial',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ]);

        return $pdf->download('gider-grafikleri.pdf');
    }

    private function generateChartUrl(string $type, array $labels, array $data, string $title): string
    {
        $chartData = [
            'type' => $type,
            'data' => [
                'labels' => $labels,
                'datasets' => [[
                    'label' => $title,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.5)',
                    'borderColor' => 'rgb(54, 162, 235)',
                    'borderWidth' => 1,
                    'data' => $data,
                ]],
            ],
            'options' => [
                'responsive' => true,
                'plugins' => [
                    'legend' => ['position' => 'top'],
                    'title' => ['display' => true, 'text' => $title],
                ],
            ],
        ];
        $encodedChartData = urlencode(json_encode($chartData));
        return "https://quickchart.io/chart?c={$encodedChartData}";
    }

    private function generatePieChartUrl(array $labels, array $data): string
    {
        $chartData = [
            'type' => 'pie',
            'data' => [
                'labels' => $labels,
                'datasets' => [[
                    'label' => '% Dağılım',
                    'data' => $data,
                    'backgroundColor' => [
                        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40',
                    ],
                ]],
            ],
            'options' => [
                'responsive' => true,
                'plugins' => [
                    'legend' => ['position' => 'right'],
                    'title' => ['display' => true, 'text' => '% Dağılım'],
                ],
            ],
        ];
        $encodedChartData = urlencode(json_encode($chartData));
        return "https://quickchart.io/chart?c={$encodedChartData}";
    }


    // --- Yeni eklenen fatura indirme methodu ---
    public function downloadInvoice($id)
    {
        $expense = Expense::find($id);

        if (!$expense) {
            return redirect()->back()->with('error', 'Gider bulunamadı.');
        }

        if (!$expense->invoice_photo) {
            return redirect()->back()->with('error', 'Fatura bulunamadı.');
        }

        $filePath = storage_path('app/public/' . $expense->invoice_photo);

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'Fatura dosyası mevcut değil.');
        }

        return response()->download($filePath, 'fatura-' . $expense->id . '.' . pathinfo($filePath, PATHINFO_EXTENSION));
    }
}
