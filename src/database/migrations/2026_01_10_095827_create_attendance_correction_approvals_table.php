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
        Schema::create('attendance_correction_approvals', function (Blueprint $table) {
    $table->id();
    $table->foreignId('correction_request_id')
          ->constrained('correction_requests')
          ->onDelete('cascade');

    $table->foreignId('admin_id')
          ->constrained('users')
          ->onDelete('cascade');

    $table->timestamp('approved_at');
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_correction_approvals');
    }
};
