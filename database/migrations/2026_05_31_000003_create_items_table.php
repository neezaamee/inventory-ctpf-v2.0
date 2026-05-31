<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('restrict');
            $table->string('name', 150);
            $table->string('sku', 100)->unique();
            $table->string('size_attribute', 50)->nullable();
            $table->text('description')->nullable();
            $table->unsignedInteger('min_quantity')->default(10);
            $table->string('unit_of_measure', 20)->default('pcs');
            $table->boolean('is_trackable')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
