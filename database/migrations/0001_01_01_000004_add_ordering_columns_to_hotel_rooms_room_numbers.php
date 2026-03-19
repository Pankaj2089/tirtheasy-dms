<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hotel', function (Blueprint $table) {
            if (!Schema::hasColumn('hotel', 'ordering')) {
                $table->integer('ordering')->default(0)->after('title');
            }
        });

        Schema::table('rooms', function (Blueprint $table) {
            if (!Schema::hasColumn('rooms', 'ordering')) {
                $table->integer('ordering')->default(0)->after('title');
            }
        });

        Schema::table('room_numbers', function (Blueprint $table) {
            if (!Schema::hasColumn('room_numbers', 'ordering')) {
                $table->integer('ordering')->default(0)->after('full_title');
            }
        });
    }

    public function down(): void
    {
        Schema::table('hotel', function (Blueprint $table) {
            if (Schema::hasColumn('hotel', 'ordering')) {
                $table->dropColumn('ordering');
            }
        });

        Schema::table('rooms', function (Blueprint $table) {
            if (Schema::hasColumn('rooms', 'ordering')) {
                $table->dropColumn('ordering');
            }
        });

        Schema::table('room_numbers', function (Blueprint $table) {
            if (Schema::hasColumn('room_numbers', 'ordering')) {
                $table->dropColumn('ordering');
            }
        });
    }
};

