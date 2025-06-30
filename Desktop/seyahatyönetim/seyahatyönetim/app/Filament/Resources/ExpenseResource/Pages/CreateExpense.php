<?php

namespace App\Filament\Resources\ExpenseResource\Pages;

use App\Filament\Resources\ExpenseResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Expense;
use App\Services\ExchangeRateService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;

class CreateExpense extends CreateRecord
{
    protected static string $resource = ExpenseResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Amount hesaplama
        if ($data['expense_type'] === 'transport') {
            $validator = Validator::make($data, [
                'kilometers' => 'required|numeric|min:0|max:1000000',
                'transportation_vehicle' => 'required|exists:araclar,ad',
            ], [
                'kilometers.required' => 'Kilometre zorunludur.',
                'kilometers.max' => 'Kilometre 1 milyon km\'yi aşamaz.',
                'transportation_vehicle.required' => 'Ulaşım aracı zorunludur.',
                'transportation_vehicle.exists' => 'Seçilen araç geçersiz.',
            ]);

            if ($validator->fails()) {
                $this->halt();
            }

            $expense = new Expense();
            $data['amount'] = $expense->calculateTransportCost($data['kilometers'], $data['transportation_vehicle']);
        } elseif ($data['expense_type'] === 'hotel') {
            $validator = Validator::make($data, [
                'accommodation_cost' => 'required|numeric|min:0|max:99999999.99',
            ], [
                'accommodation_cost.required' => 'Konaklama ücreti zorunludur.',
                'accommodation_cost.max' => 'Konaklama ücreti 99,999,999.99 TL\'yi aşamaz.',
            ]);

            if ($validator->fails()) {
                $this->halt();
            }

            $data['amount'] = $data['accommodation_cost'];
        } elseif ($data['expense_type'] === 'food') {
            $validator = Validator::make($data, [
                'meal_cost' => 'required|numeric|min:0|max:99999999.99',
            ], [
                'meal_cost.required' => 'Yemek ücreti zorunludur.',
                'meal_cost.max' => 'Yemek ücreti 99,999,999.99 TL\'yi aşamaz.',
            ]);

            if ($validator->fails()) {
                $this->halt();
            }

            $data['amount'] = $data['meal_cost'];
        }

        // Döviz çevrimleri
        if (isset($data['amount'])) {
            $exchangeRateService = app(ExchangeRateService::class);

            try {
                // TL'den diğer para birimlerine çevir
                $rateEur = $exchangeRateService->getRate('TRY', 'EUR');
                $rateUsd = $exchangeRateService->getRate('TRY', 'USD');

                // Loglama: Döviz kurlarını ve çevrilmiş değerleri kontrol et
                Log::info('Exchange Rates', [
                    'amount' => $data['amount'],
                    'TRY_to_EUR' => $rateEur,
                    'TRY_to_USD' => $rateUsd,
                ]);

                // Çevrilmiş tutarları ata
                $data['amount_converted_try'] = $data['amount']; // TL olduğu için aynı
                $data['amount_converted_eur'] = round($data['amount'] * $rateEur, 2);
                $data['amount_converted_usd'] = round($data['amount'] * $rateUsd, 2);

                // Loglama: Kaydedilen değerleri kontrol et
                Log::info('Converted Amounts', [
                    'amount_converted_try' => $data['amount_converted_try'],
                    'amount_converted_eur' => $data['amount_converted_eur'],
                    'amount_converted_usd' => $data['amount_converted_usd'],
                ]);
            } catch (\Exception $e) {
                Log::error('Döviz Çevirme Hatası: ' . $e->getMessage());
                Notification::make()
                    ->title('Döviz Kuru Hatası')
                    ->danger()
                    ->body('Anlık döviz kurları alınamadı. Lütfen sistem yöneticisine başvurun.')
                    ->send();
                // Hata durumunda çevrilmiş tutarları null yap
                $data['amount_converted_eur'] = null;
                $data['amount_converted_usd'] = null;
            }
        }

        // Kullanıcı bilgisi ekleyelim
        $data['user_id'] = auth()->id();

        return $data;
    }
}
