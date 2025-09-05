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
        Schema::table('student_results', function (Blueprint $table) {
            // Change int to string (varchar)
            $table->string('total_cce_max_min')->nullable()->change();
            $table->string('total_see_max_min')->nullable()->change();
            $table->string('total_marks_max_min')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_results', function (Blueprint $table) {
            // Revert back to int
            $table->integer('total_cce_max_min')->nullable()->change();
            $table->integer('total_see_max_min')->nullable()->change();
            $table->integer('total_marks_max_min')->nullable()->change();
        });
    }
};
