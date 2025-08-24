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
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade'); // student

            $table->foreignId('convocation_session_id')
                ->constrained('convocation_sessions')
                ->onDelete('cascade'); // make sure it points to the correct table

            $table->unsignedInteger('guest_count')->default(0); // always positive
            $table->timestamps();

            $table->unique(['user_id', 'convocation_session_id']); // prevent double booking
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('session_registrations');
    }
};
