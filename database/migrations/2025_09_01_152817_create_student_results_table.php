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
        Schema::create('student_results', function (Blueprint $table) {
            $table->bigIncrements('reultId');
            $table->unsignedBigInteger('studentId');
            $table->unsignedBigInteger('semesterId');
            $table->unsignedBigInteger('examTypeId');
            $table->unsignedBigInteger('seatNumberId')->unique(); // UNIQUE constraint
            $table->string('seatNumber')->unique();

            // Totals
            $table->integer('total_cce_max_min')->default(0);
            $table->integer('total_cce_obt')->default(0);
            $table->integer('total_see_max_min')->default(0);
            $table->integer('total_see_obt')->default(0);
            $table->integer('total_marks_max_min')->default(0);
            $table->integer('total_marks_obt')->default(0);
            $table->integer('total_credit_points')->default(0);
            $table->integer('total_credit_points_obtain')->default(0);

            // Grades
            $table->decimal('sgpa', 4, 2)->nullable();
            $table->decimal('cgpa', 4, 2)->nullable();
            $table->string('result')->default('PENDING');

             $table->timestamp('createdAt')->useCurrent();
            $table->timestamp('updatedAt')->useCurrent()->useCurrentOnUpdate();

            // Foreign keys
            $table->foreign('studentId')->references('studentId')->on('students')->onDelete('cascade');
            $table->foreign('seatNumberId')->references('seatNumberId')->on('student_seatnumber')->onDelete('cascade');
            $table->foreign('semesterId')->references('semesterId')->on('semesters')->onDelete('cascade');
            $table->foreign('examTypeId')->references('examTypeId')->on('exam_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_results');
    }
};
