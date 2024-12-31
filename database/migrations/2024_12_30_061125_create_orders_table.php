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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code');
            $table->foreignId('user_id')->constrained()->reference('id')->on('users')->onDelete('cascade');
            $table->foreignId('selling_product_id')->constrained()->reference('id')->on('selling_products')->onDelete('cascade');
            $table->foreignId('seller_id')->constrained()->reference('id')->on('users')->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->unsignedBigInteger('total_price')->default(0);
            $table->unsignedBigInteger('payment_id')->nullable();
            $table->foreign('payment_id')->references('id')->on('payments')->onDelete('set null');
            $table->string('payment_screenshot')->nullable();
            $table->string('status')->default('order-pending'); //On-Hold,Order-Accepted,Order-Rejected,Payment-Pending,Payment-Accepted,Payment-Rejected,,Delivered,Received
            $table->longText('note')->nullable();
            $table->longText('reject_note')->nullable();
            $table->string('payment_return_screenshot')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
