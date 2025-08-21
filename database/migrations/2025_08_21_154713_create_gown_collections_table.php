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
        Schema::create('gown_collections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('size', ['XS', 'S', 'M', 'L', 'XL']); // match with gown_stock
            $table->dateTime('collection_date')->nullable();
            $table->dateTime('return_date')->nullable();
            $table->enum('status', ['reserved', 'collected', 'returned', 'late'])->default('reserved');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gown_collections');
    }
};
