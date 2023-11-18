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
        Schema::create('deposit_amounts', function (Blueprint $table) {
            $table->id();
            $table->integer('amount');
            $table->integer('price')->nullable();
            $table->integer('total_price')->nullable();
            $table->float('percent')->nullable();
            $table->integer('number_of_payments')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedBigInteger('product_id')->nullable()->default(NULL);
            $table->integer('percent_amount')->nullable(); // Số tiền phần trăm
            $table->integer('remaining_amount')->nullable(); // Số tiền còn lại
            $table->json('payment_details')->nullable(); // Thêm cột JSON cho payment_details
            $table->timestamps();
            $table->foreign('product_id')->references('id')->on('products'); // Liên kết với bảng products nếu cần
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deposit_amounts');
    }
};
