<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('attendances', function (Blueprint $table) {
            // status カラムを文字列型に変更
            $table->string('status')->change();
        });
    }

    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            // もしマイグレーションをロールバックする場合は整数型に戻す
            $table->integer('status')->change();
        });
    }
};
