<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('cinema_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('role_id')->nullable()->constrained()->onDelete('set null');
            $table->boolean('is_active')->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['cinema_id']);
            $table->dropForeign(['role_id']);
            $table->dropColumn(['cinema_id', 'role_id', 'is_active']);
        });
    }
};

