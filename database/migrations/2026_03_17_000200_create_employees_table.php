<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('extension')->unique();
            $table->string('department')->nullable();
            $table->foreignId('supervisor_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('offline');
            $table->timestamp('idle_since')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};

