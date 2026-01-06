<?php

// Bu migration'ı oluşturun:
// php artisan make:migration add_reservation_fields_to_seats_table

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seats', function (Blueprint $table) {
            $table->timestamp('reserved_at')->nullable()->after('status');
            $table->timestamp('reserved_until')->nullable()->after('reserved_at');
        });
    }

    public function down(): void
    {
        Schema::table('seats', function (Blueprint $table) {
            $table->dropColumn(['reserved_at', 'reserved_until']);
        });
    }
};  