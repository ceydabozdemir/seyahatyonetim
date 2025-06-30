<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class ExchangeRateService
{
    protected $client;
    protected $baseUrl = 'https://api.frankfurter.app';

    public function __construct()
    {
        $this->client = new Client();
    }

    public function getRate($from, $to)
    {
        try {
            $response = $this->client->get("{$this->baseUrl}/latest", [
                'query' => [
                    'from' => $from,
                    'to' => $to,
                ]
            ]);

            $data = json_decode($response->getBody(), true);

            if (!isset($data['rates'][$to])) {
                Log::error('ExchangeRateService: Rate not found', ['from' => $from, 'to' => $to]);
                throw new \Exception("{$from} → {$to} kuru bulunamadı.");
            }

            return $data['rates'][$to];
        } catch (\Exception $e) {
            Log::error('ExchangeRateService Error: ' . $e->getMessage(), [
                'from' => $from,
                'to' => $to,
            ]);
            throw $e;
        }
    }
}
