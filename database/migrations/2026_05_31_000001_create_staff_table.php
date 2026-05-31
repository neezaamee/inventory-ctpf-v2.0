<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('belt_no', 50)->unique();
            $table->string('cnic', 15)->unique();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('rank', 50);
            $table->enum('gender', ['male', 'female']);
            $table->string('phone_no', 20);
            $table->string('current_posting', 150);
            $table->enum('status', ['active', 'suspended', 'retired', 'transferred'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
