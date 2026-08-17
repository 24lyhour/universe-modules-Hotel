<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Introduces a many-to-many link between rooms and the amenity catalog,
     * replacing the unused free-form `amenities` JSON column on hotel_rooms.
     */
    public function up(): void
    {
        Schema::create('hotel_amenity_room', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('hotel_rooms')->cascadeOnDelete();
            $table->foreignId('amenity_id')->constrained('hotel_amenities')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['room_id', 'amenity_id']);
        });

        if (Schema::hasColumn('hotel_rooms', 'amenities')) {
            Schema::table('hotel_rooms', function (Blueprint $table) {
                $table->dropColumn('amenities');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('hotel_rooms', 'amenities')) {
            Schema::table('hotel_rooms', function (Blueprint $table) {
                $table->json('amenities')->nullable()->after('view');
            });
        }

        Schema::dropIfExists('hotel_amenity_room');
    }
};
