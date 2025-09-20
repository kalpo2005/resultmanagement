<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('student_results', function (Blueprint $table) {
            $table->string('total_cce_obt', 20)->nullable()->default(null)->change();
            $table->string('total_see_obt', 20)->nullable()->default(null)->change();
            $table->string('total_marks_obt', 20)->nullable()->default(null)->change();
        });

        Schema::table('student_subject_results', function (Blueprint $table) {
            $table->string('cce_obtained', 20)->nullable()->default(null)->change();
            $table->string('see_obtained', 20)->nullable()->default(null)->change();
            $table->string('total_obtained', 20)->nullable()->default(null)->change();
        });

        Schema::table('students', function (Blueprint $table) {
            $table->string('studentClass')->nullable()->after('semesterId');
        });
    }

    public function down()
    {
        Schema::table('student_results', function (Blueprint $table) {
            $table->integer('total_cce_obt')->change();
            $table->integer('total_see_obt')->change();
            $table->integer('total_marks_obt')->change();
            $table->enum('examsource', ['UNIVERSITY', 'INTERNAL'])->nullable()->default(null);
        });
        Schema::table('student_subject_results', function (Blueprint $table) {
            $table->integer('cce_obtained')->change();
            $table->integer('see_obtained')->change();
            $table->integer('total_obtained')->change();
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('studentClass');
        });
    }
};
