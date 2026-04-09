<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            if (!Schema::hasColumn('hotels', 'uuid')) {
                $table->uuid('uuid')->unique()->after('id');
            }
            if (!Schema::hasColumn('hotels', 'hotel_category_id')) {
                $table->unsignedBigInteger('hotel_category_id')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('hotels', 'provinces')) {
                $table->string('provinces', 100)->nullable()->after('country');
            }
            if (!Schema::hasColumn('hotels', 'price_level')) {
                $table->string('price_level')->nullable()->after('star_rating');
            }
            if (!Schema::hasColumn('hotels', 'discount_price')) {
                $table->decimal('discount_price', 10, 2)->nullable()->after('price_per_night');
            }
            if (!Schema::hasColumn('hotels', 'currency')) {
                $table->string('currency', 10)->default('USD')->after('discount_price');
            }
            if (!Schema::hasColumn('hotels', 'total_rooms')) {
                $table->unsignedInteger('total_rooms')->default(0)->after('featured_image');
            }
            if (!Schema::hasColumn('hotels', 'total_floors')) {
                $table->unsignedInteger('total_floors')->default(0)->after('total_rooms');
            }
            if (!Schema::hasColumn('hotels', 'latitude')) {
                $table->decimal('latitude', 10, 7)->nullable()->after('amenities');
            }
            if (!Schema::hasColumn('hotels', 'longitude')) {
                $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            }
            if (!Schema::hasColumn('hotels', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('status');
            }
            if (!Schema::hasColumn('hotels', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('is_featured');
            }
            if (!Schema::hasColumn('hotels', 'updated_by')) {
                $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
            }
        });

        // Change status from enum to string if needed
        if (Schema::hasColumn('hotels', 'status')) {
            Schema::table('hotels', function (Blueprint $table) {
                $table->string('status')->default('active')->change();
            });
        }

        // Backfill UUIDs for existing rows
        $hotels = \Illuminate\Support\Facades\DB::table('hotels')->whereNull('uuid')->get();
        foreach ($hotels as $hotel) {
            \Illuminate\Support\Facades\DB::table('hotels')
                ->where('id', $hotel->id)
                ->update(['uuid' => (string) \Illuminate\Support\Str::uuid()]);
        }
    }

    public function down(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $columns = ['uuid', 'hotel_category_id', 'provinces', 'price_level', 'discount_price', 'currency', 'total_rooms', 'total_floors', 'latitude', 'longitude', 'is_featured', 'created_by', 'updated_by'];
            foreach ($columns as $col) {
                if (Schema::hasColumn('hotels', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
