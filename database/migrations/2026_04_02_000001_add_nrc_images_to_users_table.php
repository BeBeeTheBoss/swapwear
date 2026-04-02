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
        Schema::table('users', function (Blueprint $table) {
            $table->string('nrc_front_image')->nullable()->after('image');
            $table->string('nrc_back_image')->nullable()->after('nrc_front_image');
            $table->boolean('is_approved')->default(false)->after('nrc_back_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nrc_front_image', 'nrc_back_image', 'is_approved']);
        });
    }
};
