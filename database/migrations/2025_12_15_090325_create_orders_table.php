<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')    // customer
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('vendor_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('product_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->integer('quantity');
            $table->decimal('price', 10, 2);

            $table->enum('status', [
                'placed',
                'paid',
                'packed',
                'shipped',
                'delivered',
                'cancelled'
            ])->default('placed');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
