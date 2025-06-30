<?php

namespace App\Services;

class ChartService
{
    /**
     * Aylık giderler için grafik verilerini döndür.
     *
     * @return string
     */
    public function getMonthlyExpenseChartBase64()
    {
        // Aylık giderlerin gruplandırılması (örnek: 5 Mayıs, 6 Mayıs vb.)
        $monthlyData = $this->getMonthlyExpenseData();  // Bu veriyi veritabanından alabilirsiniz

        // QuickChart API'yi kullanarak grafik oluşturma
        $monthlyChartUrl = 'https://quickchart.io/chart?c=' . urlencode(json_encode([
                'type' => 'bar',
                'data' => [
                    'labels' => array_keys($monthlyData),
                    'datasets' => [[
                        'label' => 'Aylık Giderler',
                        'data' => array_values($monthlyData),
                        'backgroundColor' => 'rgba(54, 162, 235, 0.6)',
                    ]]
                ]
            ]));

        // Grafik URL'sini base64 formatına dönüştür
        return 'data:image/png;base64,' . base64_encode(file_get_contents($monthlyChartUrl));
    }

    /**
     * Yıllık giderler için grafik verilerini döndür.
     *
     * @return string
     */
    public function getYearlyExpenseChartBase64()
    {
        // Yıllık giderlerin gruplandırılması (örnek: Ocak, Şubat vb.)
        $yearlyData = $this->getYearlyExpenseData();  // Bu veriyi veritabanından alabilirsiniz

        // QuickChart API'yi kullanarak grafik oluşturma
        $yearlyChartUrl = 'https://quickchart.io/chart?c=' . urlencode(json_encode([
                'type' => 'line',
                'data' => [
                    'labels' => array_keys($yearlyData),
                    'datasets' => [[
                        'label' => 'Yıllık Giderler',
                        'data' => array_values($yearlyData),
                        'borderColor' => 'rgba(255, 99, 132, 1)',
                        'fill' => false,
                    ]]
                ]
            ]));

        // Grafik URL'sini base64 formatına dönüştür
        return 'data:image/png;base64,' . base64_encode(file_get_contents($yearlyChartUrl));
    }

    /**
     * Aylık giderlerin verilerini döndür.
     *
     * @return array
     */
    private function getMonthlyExpenseData()
    {
        // Burada veritabanından aylık giderleri çekeceksiniz, örnek veri:
        return [
            '1 May' => 1200,
            '2 May' => 900,
            '3 May' => 1500,
            // ...
        ];
    }

    /**
     * Yıllık giderlerin verilerini döndür.
     *
     * @return array
     */
    private function getYearlyExpenseData()
    {
        // Burada veritabanından yıllık giderleri çekeceksiniz, örnek veri:
        return [
            'Jan' => 12000,
            'Feb' => 15000,
            'Mar' => 10000,
            // ...
        ];
    }
}
