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
        Schema::create('gown_stocks', function (Blueprint $table) {
            $table->id();
            $table->enum('size', ['XS', 'S', 'M', 'L', 'XL'])->unique();
            $table->integer('total')->default(0);      // total owned
            $table->integer('issued')->default(0);     // how many given out
            $table->integer('available')->default(0);  // system can auto-calc (total - issued)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gown_stocks');
    }
};
