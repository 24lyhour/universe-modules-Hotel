<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The Hotel module moved from a single `price_per_night` column to a
 * range (`min_price` / `max_price`) but the original migration still
 * declares price_per_night as NOT NULL with no default — so seeders
 * that omit it fail on insert.
 *
 * Make it nullable so legacy code keeps working and new code can
 * ignore it. (Drop entirely once nothing reads it.)
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('hotels') || !Schema::hasColumn('hotels', 'price_per_night')) {
            return;
        }

        Schema::table('hotels', function (Blueprint $table) {
            $table->decimal('price_per_night', 10, 2)->nullable()->change();
        });
    }

    public function down(): void
    {
        // No-op on rollback: making a nullable column NOT NULL again
        // would fail if any rows have NULL, and we don't want to
        // backfill arbitrary defaults during a rollback.
    }
};
