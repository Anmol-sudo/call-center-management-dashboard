<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('p_b_x_status_snapshots', function (Blueprint $table) {
            $table->id();
            $table->string('status');
            $table->string('pbx_version')->nullable();
            $table->integer('uptime_seconds')->default(0);
            $table->integer('active_calls')->default(0);
            $table->integer('registered_extensions')->default(0);
            $table->integer('total_extensions')->default(0);
            $table->integer('max_concurrent_calls')->default(0);
            $table->timestamp('snapshot_taken_at')->useCurrent();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('p_b_x_status_snapshots');
    }
};

