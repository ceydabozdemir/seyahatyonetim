<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class PdfExportButton extends Widget
{
    protected static string $view = 'filament.widgets.pdf-export-button';

    protected int|string|array $columnSpan = 1; // Dar alan

    public static function getSort(): int
    {
        return -999; // En yukarı çıkması için
    }
}
