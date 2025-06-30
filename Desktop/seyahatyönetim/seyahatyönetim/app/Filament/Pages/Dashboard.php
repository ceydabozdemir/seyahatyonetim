<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

// Widgetlar
use App\Filament\Widgets\EmployeeDailyExpenses;
use App\Filament\Widgets\ExpenseChart;
use App\Filament\Widgets\PdfExportButton;
use App\Filament\Widgets\BlogPostsChart;
use App\Filament\Widgets\EmployeeExpensesChart;
use App\Filament\Widgets\NewExpenseChart;

class Dashboard extends BaseDashboard
{
    /**
     * Gösterilecek widget'lar
     */
    public function getWidgets(): array
    {
        return [
            EmployeeDailyExpenses::class,
            // ExpenseChart::class, // Hata çözüldükten sonra sırayla ekleyin
            // PdfExportButton::class,
            // BlogPostsChart::class,
            // EmployeeExpensesChart::class,
            // NewExpenseChart::class,
        ];
    }

    /**
     * Sayfa düzeni: 2 sütunlu layout
     */
    public function getColumns(): int | array
    {
        return 2;
    }
}
