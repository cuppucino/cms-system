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
        Schema::create('convocation_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Morning / Afternoon
            $table->date('date');
            $table->string('location');
            $table->integer('quota');
            $table->integer('registered')->default(0); // Track the number of registered students
            $table->integer('guest_quota')->default(0);
            $table->integer('guest_registered')->default(0); // Track the number of registered guests
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('convocation_sessions');
    }
};
