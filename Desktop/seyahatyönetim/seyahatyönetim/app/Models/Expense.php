<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Events\ExpenseAdded;

class Expense extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'expense_type',
        'expense_date', // Tarih alanı
        'accommodation_place',
        'accommodation_cost',
        'restaurant_name',
        'meal_cost',
        'transportation_vehicle',
        'kilometers',
        'amount',
        'invoice_photo',
        'amount_converted_try',
        'amount_converted_eur',
        'amount_converted_usd',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * Burada expense_date otomatik Carbon objesine dönüşür.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expense_date' => 'datetime',
        'amount' => 'float',
        'accommodation_cost' => 'float',
        'meal_cost' => 'float',
        'kilometers' => 'float',
        'amount_converted_try' => 'float',
        'amount_converted_eur' => 'float',
        'amount_converted_usd' => 'float',
    ];

    /**
     * The relationships to eager load on every query.
     *
     * @var array<string>
     */
    protected $with = ['user'];

    /**
     * The events that the model dispatches.
     *
     * @var array<string, string>
     */
    protected $dispatchesEvents = [
        'created' => ExpenseAdded::class,
    ];

    /**
     * Get the user that owns the expense.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculate the transportation cost based on kilometers and vehicle.
     *
     * @param float|null $kilometers The distance traveled in kilometers
     * @param string|null $vehicleName The name of the vehicle
     * @return float The calculated transportation cost in TRY
     */
    public function calculateTransportCost($kilometers, $vehicleName): float
    {
        // Kilometre veya araç adı yoksa 0 döndür
        if (empty($kilometers) || empty($vehicleName)) {
            \Log::warning('Missing kilometers or vehicle name for transport cost calculation', [
                'kilometers' => $kilometers,
                'vehicleName' => $vehicleName,
            ]);
            return 0.0;
        }

        try {
            // Seçilen aracı bul
            $arac = Arac::where('ad', $vehicleName)->first();

            if (!$arac) {
                \Log::warning('Vehicle not found for transport cost calculation', [
                    'vehicleName' => $vehicleName,
                ]);
                return 0.0;
            }

            // Sabit yakıt fiyatı (TL/litre)
            $yakitFiyat = 40;

            // Yakıt tüketimi (litre/km) * gidilen mesafe * yakıt fiyatı
            $amount = $arac->yakit_tuketimi * (float) $kilometers * $yakitFiyat;

            // DECIMAL(10,2) için üst sınır
            $maxAmount = 99999999.99;

            // Hesaplanan tutarı üst sınıra göre sınırla
            $calculatedAmount = min($amount, $maxAmount);

            \Log::info('Transport cost calculated successfully', [
                'kilometers' => $kilometers,
                'vehicleName' => $vehicleName,
                'yakit_tuketimi' => $arac->yakit_tuketimi,
                'calculated_amount' => $calculatedAmount,
            ]);

            return $calculatedAmount;
        } catch (\Exception $e) {
            \Log::error('Error calculating transport cost', [
                'error' => $e->getMessage(),
                'kilometers' => $kilometers,
                'vehicleName' => $vehicleName,
            ]);
            return 0.0;
        }
    }
}
