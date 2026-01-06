<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('sale_reference')->unique(); // Satış referans numarası
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone');
            $table->string('payment_method');
            $table->string('payment_status')->default('pending');
            $table->decimal('total_amount', 8, 2);
            $table->integer('ticket_count')->default(1); // Kaç bilet satıldı
            $table->json('ticket_breakdown')->nullable(); // Bilet tiplerinin detayı
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};