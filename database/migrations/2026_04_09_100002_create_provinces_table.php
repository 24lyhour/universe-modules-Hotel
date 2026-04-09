<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('provinces', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('name_kh')->nullable();
            $table->string('code', 10)->unique();
            $table->string('region')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active']);
            $table->index(['region']);
        });

        // Add province_id FK to hotels
        Schema::table('hotels', function (Blueprint $table) {
            $table->unsignedBigInteger('province_id')->nullable()->after('country');
        });
    }

    public function down(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            if (Schema::hasColumn('hotels', 'province_id')) {
                $table->dropColumn('province_id');
            }
        });

        Schema::dropIfExists('provinces');
    }
};
