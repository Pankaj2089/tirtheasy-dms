<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'checkin_checkout_status')) {
                $table->enum('checkin_checkout_status', ['checked_in', 'checked_out'])->nullable()->after('payment_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'checkin_checkout_status')) {
                $table->dropColumn('checkin_checkout_status');
            }
        });
    }
};

