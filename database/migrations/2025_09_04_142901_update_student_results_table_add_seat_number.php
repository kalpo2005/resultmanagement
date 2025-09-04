<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_results', function (Blueprint $table) {
            // Safely drop FK if it exists
            DB::statement('ALTER TABLE student_results DROP FOREIGN KEY IF EXISTS student_results_seatnumberid_foreign');

            // Drop column if exists
            if (Schema::hasColumn('student_results', 'seatNumberId')) {
                $table->dropColumn('seatNumberId');
            }
        });

        Schema::table('student_results', function (Blueprint $table) {
            // Add seatNumber after resultId
            if (!Schema::hasColumn('student_results', 'seatNumber')) {
                $table->string('seatNumber')->unique()->after('resultId');
            }
        });
    }

    public function down(): void
    {
        Schema::table('student_results', function (Blueprint $table) {
            if (Schema::hasColumn('student_results', 'seatNumber')) {
                $table->dropColumn('seatNumber');
            }
        });

        Schema::table('student_results', function (Blueprint $table) {
            if (!Schema::hasColumn('student_results', 'seatNumberId')) {
                $table->unsignedBigInteger('seatNumberId')->unique()->after('examTypeId');
                $table->foreign('seatNumberId')->references('seatNumberId')->on('student_seatnumber')->onDelete('cascade');
            }
        });
    }
};
