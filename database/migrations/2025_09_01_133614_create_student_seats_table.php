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
        Schema::create('student_seatNumber', function (Blueprint $table) {
            $table->bigIncrements('seatNumberId'); // primary key
            $table->unsignedBigInteger('studentId'); // foreign key to students
            $table->unsignedBigInteger('semesterId'); // foreign key to semesters
            $table->unsignedBigInteger('examTypeId'); // foreign key to exam_types
            $table->string('seatNumber')->unique(); // unique seat number
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('studentId')->references('studentId')->on('students')->onDelete('restrict');
            $table->foreign('semesterId')->references('semesterId')->on('semesters')->onDelete('restrict');
            $table->foreign('examTypeId')->references('examTypeId')->on('exam_types')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_seats');
    }
};
