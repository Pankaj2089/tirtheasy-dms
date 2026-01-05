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
        Schema::create('price_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_id');
            $table->unsignedBigInteger('hotel_id');
            $table->unsignedBigInteger('room_id');
            $table->integer('advance_booking_days')->nullable();
            $table->boolean('apply_dynamic_price')->default(0);
            $table->decimal('common_price', 10, 2)->nullable();
            $table->decimal('gst_percentage', 5, 2)->nullable();
            $table->decimal('january', 10, 2)->nullable();
            $table->decimal('february', 10, 2)->nullable();
            $table->decimal('march', 10, 2)->nullable();
            $table->decimal('april', 10, 2)->nullable();
            $table->decimal('may', 10, 2)->nullable();
            $table->decimal('june', 10, 2)->nullable();
            $table->decimal('july', 10, 2)->nullable();
            $table->decimal('august', 10, 2)->nullable();
            $table->decimal('september', 10, 2)->nullable();
            $table->decimal('october', 10, 2)->nullable();
            $table->decimal('november', 10, 2)->nullable();
            $table->decimal('december', 10, 2)->nullable();
            $table->timestamps();

            // Unique constraint to prevent duplicate settings for same vendor/hotel/room
            $table->unique(['vendor_id', 'hotel_id', 'room_id']);
            
            // Foreign keys (optional, can be added if needed)
            // $table->foreign('vendor_id')->references('id')->on('users');
            // $table->foreign('hotel_id')->references('id')->on('hotel');
            // $table->foreign('room_id')->references('id')->on('rooms');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_settings');
    }
};

