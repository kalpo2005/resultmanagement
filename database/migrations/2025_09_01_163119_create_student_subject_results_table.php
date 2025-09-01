<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_subject_results', function (Blueprint $table) {
            $table->bigIncrements('subjectId');
            $table->unsignedBigInteger('resultId'); // foreign key column

            $table->string('subject_code');
            $table->string('subject_type')->nullable();
            $table->string('subject_name');
            $table->integer('credit')->default(0);

            $table->string('cce_max_min')->nullable();
            $table->integer('cce_obtained')->default(0);

            $table->string('see_max_min')->nullable();
            $table->integer('see_obtained')->default(0);

            $table->string('total_max_min')->nullable();
            $table->integer('total_obtained')->default(0);

            $table->decimal('marks_percentage', 5, 2)->nullable();
            $table->string('letter_grade')->nullable();
            $table->decimal('grade_point', 4, 2)->nullable();
            $table->decimal('credit_point', 5, 2)->nullable();

            $table->timestamp('createdAt')->useCurrent();
            $table->timestamp('updatedAt')->useCurrent()->useCurrentOnUpdate();

            // ✅ Foreign key definition
            $table->foreign('resultId')
                  ->references('resultId')
                  ->on('student_results')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_subject_results');
    }
};
