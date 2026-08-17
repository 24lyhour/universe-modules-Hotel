<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fills out the previously-stub hotel_room_policies table so room policies
     * become a manageable catalog (title, icon, description, status).
     */
    public function up(): void
    {
        if (!Schema::hasTable('hotel_room_policies')) {
            Schema::create('hotel_room_policies', function (Blueprint $table) {
                $table->id();
                $table->timestamps();
            });
        }

        Schema::table('hotel_room_policies', function (Blueprint $table) {
            if (!Schema::hasColumn('hotel_room_policies', 'uuid')) {
                $table->uuid('uuid')->nullable()->unique()->after('id');
            }
            if (!Schema::hasColumn('hotel_room_policies', 'title')) {
                $table->string('title')->after('uuid');
            }
            if (!Schema::hasColumn('hotel_room_policies', 'icon')) {
                $table->string('icon', 50)->nullable()->after('title');
            }
            if (!Schema::hasColumn('hotel_room_policies', 'description')) {
                $table->text('description')->nullable()->after('icon');
            }
            if (!Schema::hasColumn('hotel_room_policies', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('description');
            }
            if (!Schema::hasColumn('hotel_room_policies', 'sort_order')) {
                $table->unsignedInteger('sort_order')->default(0)->after('is_active');
            }
            if (!Schema::hasColumn('hotel_room_policies', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        Schema::table('hotel_room_policies', function (Blueprint $table) {
            foreach (['uuid', 'title', 'icon', 'description', 'is_active', 'sort_order', 'deleted_at'] as $col) {
                if (Schema::hasColumn('hotel_room_policies', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
