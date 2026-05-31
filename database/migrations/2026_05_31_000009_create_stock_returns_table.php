<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_returns', function (Blueprint $table) {
            $table->id();
            $table->string('return_slip_no', 50)->unique();
            $table->foreignId('staff_id')->constrained('staff')->onDelete('restrict');
            $table->foreignId('received_by')->constrained('users')->onDelete('restrict');
            $table->date('return_date');
            $table->enum('reason', ['size_mismatch', 'wear_and_tear', 'transfer', 'retirement', 'other']);
            $table->enum('action_taken', ['restocked', 'condemned_disposed', 'sent_to_repairs']);
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_returns');
    }
};
