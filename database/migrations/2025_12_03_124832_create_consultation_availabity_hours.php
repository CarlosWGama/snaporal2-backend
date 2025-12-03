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
        Schema::create('consultation_availabity_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consultation_availabity_id')->references('id')->on('consultation_availabities')->onDelete('cascade');
            $table->string('day');
            $table->string('hour');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultation_availabity_hours');
    }
};
