<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hall_id')->constrained();
            $table->string('row');
            $table->integer('number');
            $table->enum('status', ['Blank', 'Filled', 'In Another Basket'])
          ->default('Blank');
            $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('seats');
    }
};
