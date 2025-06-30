<?php

return [

    'monthly_expense_chart' => [
        'type' => 'bar',
        'data' => [
            'labels' => ['Ocak', 'Şubat', 'Mart', 'Nisan'],
            'datasets' => [
                [
                    'label' => 'Giderler',
                    'data' => [1200, 950, 1350, 1100],
                    'backgroundColor' => '#3b82f6',
                ],
            ],
        ],
        'options' => [
            'plugins' => [
                'title' => [
                    'display' => true,
                    'text' => 'Aylık Giderler',
                ],
            ],
        ],
    ],

    'yearly_expense_chart' => [
        'type' => 'line',
        'data' => [
            'labels' => ['2021', '2022', '2023', '2024'],
            'datasets' => [
                [
                    'label' => 'Yıllık Gider',
                    'data' => [14400, 15800, 17300, 16900],
                    'borderColor' => '#10b981',
                    'fill' => false,
                ],
            ],
        ],
    ],

    'employee_expense_chart' => [
        'type' => 'bar',
        'data' => [
            'labels' => ['Ayşe', 'Ali', 'Mehmet', 'Zeynep'],
            'datasets' => [
                [
                    'label' => 'Toplam Gider',
                    'data' => [4200, 3900, 4700, 4400],
                    'backgroundColor' => '#f59e0b',
                ],
            ],
        ],
    ],

    'percentage_change_chart' => [
        'type' => 'line',
        'data' => [
            'labels' => ['Ocak', 'Şubat', 'Mart', 'Nisan'],
            'datasets' => [
                [
                    'label' => 'Yüzdelik Değişim',
                    'data' => [0, 5, -3, 8],
                    'backgroundColor' => 'rgba(59,130,246,0.2)',
                    'borderColor' => '#3b82f6',
                    'fill' => true,
                ],
            ],
        ],
    ],

];
