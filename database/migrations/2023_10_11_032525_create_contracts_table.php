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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('manager_id');
            $table->string('name');
            $table->text('note')->nullable();
            $table->string('status');
            $table->timestamp('datetime_start');
            $table->timestamp('datetime_end');
            $table->string('manager_electronic_signature');
            $table->string('customer_electronic_signature');
            $table->string('sale_eletronic_signature');
            $table->timestamps();
            $table->foreign('sale_id')->references('id')->on('users');
            $table->foreign('customer_id')->references('id')->on('users');
            $table->foreign('manager_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
