<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gown_collections', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // NEW: bind each collection to an admin-defined gown session
            $table->foreignId('gown_session_id')
                  ->nullable()
                  ->constrained('gown_sessions')
                  ->onDelete('cascade');

            $table->enum('size', ['XS', 'S', 'M', 'L', 'XL']); // match gown_stocks
            $table->dateTime('collection_date')->nullable();   // optional: keep as display/audit
            $table->dateTime('return_date')->nullable();

            $table->enum('status', ['reserved', 'collected', 'returned', 'late'])->default('reserved');

            $table->timestamps();

            // (optional) helpful composite index for admin screens
            $table->index(['gown_session_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gown_collections');
    }
};
