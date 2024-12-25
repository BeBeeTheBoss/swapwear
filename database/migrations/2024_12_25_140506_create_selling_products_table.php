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
        Schema::create('selling_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->reference('id')->on('users')->onDelete('cascade');
            $table->foreignId('sub_category_id')->constrained()->reference('id')->on('sub_categories')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('condition')->default('used');
            $table->integer('quantity')->default(1);
            $table->unsignedBigInteger('price')->default(0);
            $table->string('status')->default('selling');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('selling_products');
    }
};
