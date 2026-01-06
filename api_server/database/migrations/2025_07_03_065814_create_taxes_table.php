<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('taxes', function (Blueprint $table) {
            $table->id();
            $table->string('name');                    // KDV, Hizmet Bedeli
            $table->enum('type', ['percentage', 'fixed', 'fixed_total'])
                  ->default('percentage');             // Vergi türü
            $table->decimal('rate', 8, 2);            // 20.00 (% için), 2.50 (₺ için)
            $table->enum('status', ['active', 'inactive'])
                  ->default('active');                 // Durum
            $table->integer('priority')->default(1);   // Hesaplama sırası
            $table->text('description')->nullable();   // Açıklama
            $table->timestamps();
            
            // İndeksler
            $table->index(['status', 'priority']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('taxes');
    }
};