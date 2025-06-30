<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('araclar', function (Blueprint $table) {
            $table->id();
            $table->string('ad'); // Araç adı
            $table->decimal('yakit_tuketimi', 5, 2); // Km başına yakıt tüketimi (litre/km)
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('araclar');
    }
};
