<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('showtimes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('movie_id')->constrained()->onDelete('cascade');
            $table->foreignId('hall_id')->constrained()->onDelete('cascade');
            $table->decimal('price', 8, 2)->nullable();
            $table->datetime('start_time');
            $table->datetime('end_time');
            $table->date('date');
            $table->string('status')->default('active');
            $table->timestamps();
            
            $table->index(['date', 'start_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('showtimes');
    }
};
