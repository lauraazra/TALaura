<?php

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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('buyer_name')->nullable();
            $table->timestamp('transaction_time')->nullable(); // Kolom waktu transaksi
            $table->integer('total_item');
            $table->decimal('total_price', 50, 2);
            $table->integer('void')->default(0); // Kolom void, default 0
            $table->timestamps(); // Kolom created_at dan updated_at

            $table->index(['user_id', 'void']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
