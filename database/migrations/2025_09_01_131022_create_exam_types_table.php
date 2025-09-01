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
        Schema::create('exam_types', function (Blueprint $table) {
            $table->bigIncrements('examTypeId'); // primary key
            $table->string('examName'); // e.g., Nov-Dec-Jan
            $table->string('alias')->unique();
            $table->string('academicYear'); // e.g., 2025/2026
            $table->text('description')->nullable(); // optional notes about exam type
            $table->boolean('status')->default(1); // active/inactive
            $table->timestamp('createdAt')->useCurrent();
            $table->timestamp('updatedAt')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_types');
    }
};
