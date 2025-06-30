<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            // amount_original sütunu yoksa ekle
            if (!Schema::hasColumn('expenses', 'amount_original')) {
                $table->decimal('amount_original', 10, 2)->nullable()->after('amount');
            }
            // currency sütunu yoksa ekle
            if (!Schema::hasColumn('expenses', 'currency')) {
                $table->string('currency')->nullable()->after('amount_original');
            }
            // amount_converted_try sütunu yoksa ekle
            if (!Schema::hasColumn('expenses', 'amount_converted_try')) {
                $table->decimal('amount_converted_try', 10, 2)->nullable()->after('currency');
            }
            // amount_converted_eur sütunu yoksa ekle
            if (!Schema::hasColumn('expenses', 'amount_converted_eur')) {
                $table->decimal('amount_converted_eur', 10, 2)->nullable()->after('amount_converted_try');
            }
            // amount_converted_usd sütunu yoksa ekle
            if (!Schema::hasColumn('expenses', 'amount_converted_usd')) {
                $table->decimal('amount_converted_usd', 10, 2)->nullable()->after('amount_converted_eur');
            }
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $columns = ['amount_original', 'currency', 'amount_converted_try', 'amount_converted_eur', 'amount_converted_usd'];
            $table->dropColumn(array_filter($columns, fn($column) => Schema::hasColumn('expenses', $column)));
        });
    }
};
