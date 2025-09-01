<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::table('colleges', function (Blueprint $table) {
            $table->renameColumn('college_id', 'collegeId');
            $table->renameColumn('name', 'collegeName');
        });
    }

    public function down(): void
    {
        Schema::table('colleges', function (Blueprint $table) {
            $table->renameColumn('collegeId', 'college_id');
            $table->renameColumn('collegeName', 'collegeName');
        });
    }
};
