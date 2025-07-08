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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->string('npk');
            $table->date('tanggal');
            $table->string('subdivisi');
            $table->string('jam_pagi')->nullable();
            $table->string('jam_siang')->nullable();
            $table->string('jam_malam')->nullable();
            $table->string('status')->nullable();
            $table->string('void')->default('false');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
