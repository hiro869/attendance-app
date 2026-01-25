<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // 例えば、statusフィールドに'default'として '勤務外' を設定
public function up()
{
    Schema::table('attendances', function (Blueprint $table) {
        $table->string('status')->default('勤務外')->change(); // 既存のstatusフィールドを変更
    });
}

public function down()
{
    Schema::table('attendances', function (Blueprint $table) {
        $table->string('status')->nullable()->change(); // 変更を戻す場合
    });
}

};
