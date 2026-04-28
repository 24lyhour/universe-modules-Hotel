<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Catch-up migration: a previous migration was recorded as ran while the
 * file was empty, so the `discount_percentage` column never made it onto
 * existing databases. Idempotent — guarded by Schema::hasColumn.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('hotels') || Schema::hasColumn('hotels', 'discount_percentage')) {
            return;
        }

        Schema::table('hotels', function (Blueprint $table) {
            $table->unsignedTinyInteger('discount_percentage')
                ->nullable()
                ->after('discount_price');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('hotels') || !Schema::hasColumn('hotels', 'discount_percentage')) {
            return;
        }

        Schema::table('hotels', function (Blueprint $table) {
            $table->dropColumn('discount_percentage');
        });
    }
};
