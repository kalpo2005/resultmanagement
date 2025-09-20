<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
    {
        Schema::table('student_subject_results', function (Blueprint $table) {
            // Drop old foreign key
            $table->dropForeign(['resultId']);

            // Recreate foreign key with cascade updates
            $table->foreign('resultId')
                  ->references('resultId')->on('student_results')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('student_subject_results', function (Blueprint $table) {
            $table->dropForeign(['resultId']);

            // Optional: recreate without cascade if needed
            $table->foreign('resultId')
                  ->references('resultId')->on('student_results');
        });
    }

};
