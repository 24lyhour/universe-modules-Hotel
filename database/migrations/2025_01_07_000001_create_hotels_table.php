<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('hotel_category_id')->nullable()->constrained('hotel_categories')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('address', 500);
            $table->string('city', 100);
            $table->string('country', 100)->default('Cambodia');
            $table->string('provinces', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->unsignedTinyInteger('star_rating')->default(3);
            $table->string('price_level')->nullable();
            $table->decimal('price_per_night', 10, 2);
            $table->decimal('discount_price', 10, 2)->nullable();
            $table->string('currency', 10)->default('USD');
            $table->string('featured_image')->nullable();
            $table->unsignedInteger('total_rooms')->default(0);
            $table->unsignedInteger('total_floors')->default(0);
            $table->json('gallery')->nullable();
            $table->json('amenities')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('status')->default('active');
            $table->boolean('is_featured')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status']);
            $table->index(['city']);
            $table->index(['star_rating']);
            $table->index(['is_featured']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotels');
    }
};
