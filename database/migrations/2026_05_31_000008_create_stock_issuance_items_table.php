<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_issuance_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_issuance_id')->constrained('stock_issuances')->onDelete('cascade');
            $table->foreignId('batch_id')->constrained('inventory_batches')->onDelete('restrict');
            $table->unsignedInteger('quantity');
            $table->unsignedInteger('returned_quantity')->default(0);
            $table->enum('status', ['active', 'returned', 'damaged', 'lost'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_issuance_items');
    }
};
