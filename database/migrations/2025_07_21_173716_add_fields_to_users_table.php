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
        Schema::table('users', function (Blueprint $table) {
            $table->string('student_id')->nullable();          // only for students
            $table->foreignId('course_id')->nullable()->constrained(); // FK to courses
            $table->enum('gown_size', ['XS', 'S', 'M', 'L', 'XL'])->nullable();
            $table->foreignId('convocation_session_id')->nullable()->constrained();
            $table->enum('status', ['pending', 'registered', 'confirmed'])->default('pending');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
