<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
    {
        Schema::table('student_results', function (Blueprint $table) {
           

            // Add foreign key constraint
            $table->foreign('collegeId')
                ->references('collegeId')->on('colleges')
                ->onUpdate('cascade')
                ->onDelete('restrict'); 
        });
    }

    public function down(): void
    {
        Schema::table('student_results', function (Blueprint $table) {
            $table->dropForeign(['collegeId']);
            $table->dropColumn('collegeId');
        });
    }

};
