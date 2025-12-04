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
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professional_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('specialist_id')->references('id')->on('users')->onDelete('cascade');
            $table->date('date');
            $table->string('hour');
            $table->string('stream_url')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'closed'])->default('pending');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};
