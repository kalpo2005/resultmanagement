<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('student_results', function (Blueprint $table) {
            // Drop unique constraint (replace with actual index name)
            $table->dropUnique('student_results_seatnumber_unique');
        });
    }

    public function down(): void
    {
        Schema::table('student_results', function (Blueprint $table) {
            // Re-add the unique constraint in case of rollback
            $table->unique('seatnumber');
        });
    }
};
