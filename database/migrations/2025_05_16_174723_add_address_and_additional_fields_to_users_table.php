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
            // Tambahkan kolom address, birth_date, dan gender
            $table->string('address')->nullable()->after('phone_number');
            $table->date('birth_date')->nullable()->after('address');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('birth_date');
            
            // Tambahkan kolom province_id dan city_id
            $table->unsignedBigInteger('province_id')->nullable()->after('gender');
            $table->unsignedBigInteger('city_id')->nullable()->after('province_id');
            
            // Tambahkan foreign key constraints
            $table->foreign('province_id')->references('id')->on('provinces');
            $table->foreign('city_id')->references('id')->on('cities');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Hapus foreign key constraints
            $table->dropForeign(['province_id']);
            $table->dropForeign(['city_id']);
            
            // Hapus semua kolom
            $table->dropColumn(['address', 'birth_date', 'gender', 'province_id', 'city_id']);
        });
    }
};
