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
        Schema::create('payment_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('deposit_amount_id');
            $table->integer('payment_number');
            $table->decimal('payment_amount', 10, 2);
            $table->decimal('remaining_amount', 10, 2);
            $table->integer('status')->default(0); 
            $table->timestamps();
            $table->foreign('deposit_amount_id')->references('id')->on('deposit_amounts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_details');
    }
};
