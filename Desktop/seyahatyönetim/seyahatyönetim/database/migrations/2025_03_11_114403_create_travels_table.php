<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('travels', function (Blueprint $table) {
            $table->id();
            $table->string('destination'); // Gidilecek yer
            $table->date('departure_date'); // Gidiş tarihi
            $table->date('return_date')->nullable(); // Dönüş tarihi (opsiyonel)
            $table->string('expense_type'); // Gider türü
            $table->decimal('amount', 10, 2); // Tutar

            // Yeni eklenen alanlar
            $table->integer('kilometers')->nullable(); // Kilometre bilgisi
            $table->enum('transportation_vehicle', ['uçak', 'tren', 'elektrikli araç', 'dizel araç'])->nullable(); // Ulaşım aracı seçenekleri
            $table->string('accommodation_place')->nullable(); // Konaklanan yer
            $table->decimal('accommodation_cost', 10, 2)->nullable(); // Konaklama ücreti
            $table->enum('status', ['onay bekliyor', 'onaylandı', 'reddedildi'])->default('onay bekliyor');


            $table->timestamps(); // created_at ve updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('travels');
    }
};
