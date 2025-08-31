<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('session_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('convocation_session_id')->constrained('convocation_sessions')->onDelete('cascade');
            $table->unsignedInteger('guest_count')->default(0);

            // New fields
            $table->boolean('attendance_confirmed')->default(false);
            $table->string('gown_size')->nullable(); // XS, S, M, L, XL
            $table->date('collection_date')->nullable();

            $table->timestamps();
            $table->unique(['user_id', 'convocation_session_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('session_registrations');
    }
};
