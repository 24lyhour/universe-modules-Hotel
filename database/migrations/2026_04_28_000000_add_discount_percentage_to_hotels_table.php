<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The Hotel model has `discount_percentage` in $fillable + $casts and the
 * seeder writes to it, but no earlier migration creates the column.
 * Guarded so re-runs on existing DBs are safe.
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
