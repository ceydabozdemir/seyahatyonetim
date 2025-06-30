<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Gider ismi
            $table->string('expense_type'); // Gider türü
            $table->string('accommodation_place')->nullable(); // Konaklama yeri
            $table->decimal('accommodation_cost', 10, 2)->nullable(); // Konaklama ücreti
            $table->string('restaurant_name')->nullable(); // Restoran adı
            $table->decimal('meal_cost', 10, 2)->nullable(); // Yemek ücreti
            $table->string('transportation_vehicle')->nullable(); // Ulaşım aracı
            $table->decimal('kilometers', 10, 2)->nullable(); // Kilometre
            $table->decimal('amount', 10, 2); // Toplam gider tutarı
            $table->string('invoice_photo')->nullable(); // Fatura fotoğrafı
            $table->timestamps(); // Zaman damgaları
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
