<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Belirli bir view için widget verisi gönderme
        View::composer('dashboard', function ($view) {
            $view->with('sidebarWidget', $this->getSidebarWidgetData());
        });

        // Tüm view'lar için widget verisi gönderme
        View::share('footerWidget', $this->getFooterWidgetData());
    }

    /**
     * Kenar çubuğu widget'ı için verileri getirir.
     *
     * @return array
     */
    protected function getSidebarWidgetData()
    {
        // Widget için gerekli verileri burada hazırlayın (örneğin veritabanından veri çekme)
        return ['title' => 'Kenar Çubuğu Bilgisi', 'content' => 'Bu bir kenar çubuğu widget içeriğidir.'];
    }

    /**
     * Altbilgi widget'ı için verileri getirir.
     *
     * @return array
     */
    protected function getFooterWidgetData()
    {
        // Widget için gerekli verileri burada hazırlayın
        return ['copyright' => '© 2025 Tüm Hakları Saklıdır.'];
    }
}
