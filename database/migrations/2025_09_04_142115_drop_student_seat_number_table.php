<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // First drop foreign key from student_results
        Schema::table('student_results', function (Blueprint $table) {
            $table->dropForeign('student_results_seatnumberid_foreign');
        });

        // Now drop the student_seatNumber table
        Schema::dropIfExists('student_seatNumber');
    }

    public function down(): void
    {
        Schema::create('student_seatNumber', function (Blueprint $table) {
            $table->bigIncrements('seatNumberId');
            $table->unsignedBigInteger('studentId');
            $table->unsignedBigInteger('semesterId');
            $table->unsignedBigInteger('examTypeId');
            $table->string('seatNumber')->unique();
            $table->timestamp('createdAt')->useCurrent();
            $table->timestamp('updatedAt')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('studentId')->references('studentId')->on('students')->onDelete('restrict');
            $table->foreign('semesterId')->references('semesterId')->on('semesters')->onDelete('restrict');
            $table->foreign('examTypeId')->references('examTypeId')->on('exam_types')->onDelete('restrict');
        });

        // Restore relation in student_results if needed
        Schema::table('student_results', function (Blueprint $table) {
            $table->foreign('seatNumberId')->references('seatNumberId')->on('student_seatNumber')->onDelete('restrict');
        });
    }
};

