<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('gown_sessions', function (Blueprint $table) {
            $table->id();
            $table->date('date');                              // e.g. 2025-04-10
            $table->time('start_time')->nullable();            // e.g. 09:00
            $table->time('end_time')->nullable();              // e.g. 12:00
            $table->string('location');
            $table->unsignedInteger('quota')->default(0);      // max students in this slot
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gown_sessions');
    }
};
