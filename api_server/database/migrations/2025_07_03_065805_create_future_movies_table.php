<?php
// database/migrations/xxxx_create_future_movies_table.php

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
        Schema::create('future_movies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('duration')->nullable(); // dakika
            $table->string('language', 10)->nullable();
            // TARİH FORMATINI DEĞİŞTİRDİK - string yerine date
            $table->date('release_date')->nullable(); // DATE formatı
            $table->string('genre', 100)->nullable();
            $table->text('poster_url')->nullable();
            $table->string('imdb_raiting', 10)->nullable();
            $table->enum('status', ['upcoming', 'pre_production', 'in_production', 'post_production'])
                  ->default('upcoming');
            $table->timestamps();

            // Indexes
            $table->index('status');
            $table->index('genre');
            $table->index('release_date');
            $table->index(['status', 'release_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('future_movies');
    }
};