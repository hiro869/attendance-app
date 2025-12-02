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
    Schema::create('correction_requests', function (Blueprint $table) {
        $table->id();
        $table->foreignId('attendance_id')->constrained()->onDelete('cascade');
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->dateTime('request_start_time')->nullable();
        $table->dateTime('request_end_time')->nullable();
        $table->json('request_breaks')->nullable(); // 複数休憩の修正内容
        $table->text('note');
        $table->tinyInteger('status')->default(0); // 0=承認待ち,1=承認済み
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('correction_requests');
}

};
