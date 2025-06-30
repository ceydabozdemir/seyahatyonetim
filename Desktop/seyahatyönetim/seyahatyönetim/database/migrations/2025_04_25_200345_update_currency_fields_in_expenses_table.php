<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            // amount_original ve currency sütunlarını kaldır
            $columnsToDrop = ['amount_original', 'currency'];
            $table->dropColumn(array_filter($columnsToDrop, fn($column) => Schema::hasColumn('expenses', $column)));

            // Çevrilmiş tutar sütunlarını ekle (varsa atla)
            if (!Schema::hasColumn('expenses', 'amount_converted_try')) {
                $table->decimal('amount_converted_try', 10, 2)->nullable()->after('amount');
            }
            if (!Schema::hasColumn('expenses', 'amount_converted_eur')) {
                $table->decimal('amount_converted_eur', 10, 2)->nullable()->after('amount_converted_try');
            }
            if (!Schema::hasColumn('expenses', 'amount_converted_usd')) {
                $table->decimal('amount_converted_usd', 10, 2)->nullable()->after('amount_converted_eur');
            }
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            // Geri alma: çevrilmiş tutar sütunlarını kaldır
            $columnsToDrop = ['amount_converted_try', 'amount_converted_eur', 'amount_converted_usd'];
            $table->dropColumn(array_filter($columnsToDrop, fn($column) => Schema::hasColumn('expenses', $column)));

            // Geri alma: amount_original ve currency sütunlarını ekle
            if (!Schema::hasColumn('expenses', 'amount_original')) {
                $table->decimal('amount_original', 10, 2)->nullable()->after('amount');
            }
            if (!Schema::hasColumn('expenses', 'currency')) {
                $table->string('currency')->nullable()->after('amount_original');
            }
        });
    }
};
