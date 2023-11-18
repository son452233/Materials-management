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
        Schema::create('deposit_amount_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('deposit_amount_id');
            $table->integer('amount');
            $table->integer('status');
            $table->string('note');

            // Khóa ngoại liên kết với bảng deposit_amounts
            $table->foreign('deposit_amount_id')->references('id')->on('deposit_amounts');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deposit_amount_logs');
    }
};
