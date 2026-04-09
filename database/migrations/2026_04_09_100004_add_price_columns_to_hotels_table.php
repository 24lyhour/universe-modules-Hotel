<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            if (!Schema::hasColumn('hotels', 'min_price')) {
                $table->decimal('min_price', 10, 2)->nullable()->after('price_level');
            }
            if (!Schema::hasColumn('hotels', 'max_price')) {
                $table->decimal('max_price', 10, 2)->nullable()->after('min_price');
            }
            if (!Schema::hasColumn('hotels', 'min_discount_price')) {
                $table->decimal('min_discount_price', 10, 2)->nullable()->after('max_price');
            }
            if (!Schema::hasColumn('hotels', 'max_discount_price')) {
                $table->decimal('max_discount_price', 10, 2)->nullable()->after('min_discount_price');
            }
        });
    }

    public function down(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->dropColumn(['min_price', 'max_price', 'min_discount_price', 'max_discount_price']);
        });
    }
};
