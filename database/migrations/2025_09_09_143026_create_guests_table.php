<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->timestamps();
            $table->unique(['user_id', 'name']); // Prevent duplicate guests per student
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guests');
    }
};
