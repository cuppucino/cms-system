<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('gown_collection_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 8, 2);
            $table->string('status')->default('pending'); // pending, paid, failed
            $table->string('reference')->unique(); // make reference unique instead of nullable
            $table->timestamps();

            // 🔹 extra indexes for performance
            $table->index('status');
            $table->index(['user_id', 'gown_collection_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
