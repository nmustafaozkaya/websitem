<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('showtime_id')->constrained('showtimes')->onDelete('cascade');  
            $table->foreignId('seat_id')->constrained('seats')->onDelete('cascade');          
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('price', 8, 2);
            $table->string('customer_type')->default('adult');                                
            $table->decimal('discount_rate', 5, 2)->default(0);                              
            $table->string('status')->default('sold');
            $table->timestamps();
            
            $table->unique(['showtime_id', 'seat_id']);                                         
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};