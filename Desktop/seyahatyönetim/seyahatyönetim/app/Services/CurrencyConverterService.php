<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CurrencyConverterService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.exchange_rate.key');
    }

    /**
     * İki para birimi arasında dönüştürme işlemi yapar.
     *
     * @param string $from Orijinal para birimi (örn: USD)
     * @param string $to Hedef para birimi (örn: TRY)
     * @param float $amount Dönüştürülecek miktar
     * @return float|null
     */
    public function convert($from, $to, $amount)
    {
        $response = Http::get("https://v6.exchangerate-api.com/v6/{$this->apiKey}/pair/{$from}/{$to}");

        if ($response->successful() && isset($response['conversion_rate'])) {
            $rate = $response['conversion_rate'];
            return round($amount * $rate, 2);
        }

        return null;
    }
}
