<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hotel_rooms', function (Blueprint $table) {
            if (!Schema::hasColumn('hotel_rooms', 'total_room')) {
                $table->unsignedInteger('total_room')->default(1)->after('name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('hotel_rooms', function (Blueprint $table) {
            if (Schema::hasColumn('hotel_rooms', 'total_room')) {
                $table->dropColumn('total_room');
            }
        });
    }
};
