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
        Schema::create('students', function (Blueprint $table) {
            $table->bigIncrements('studentId'); // primary key
            $table->unsignedBigInteger('enrollmentNumber')->unique();
            $table->string('firstName');
            $table->string('middleName')->nullable();
            $table->string('lastName');
            $table->string('fullName'); // concatenated field
            $table->unsignedBigInteger('collegeId'); // foreign key to colleges
            $table->unsignedBigInteger('semesterId'); // foreign key to semesters
            $table->string('profileImage')->nullable(); // image path
            $table->date('dob')->nullable();
            $table->string('city')->nullable();
            $table->string('contactNumber')->nullable();
            $table->boolean('status')->default(1);
            $table->timestamp('createdAt')->useCurrent();
            $table->timestamp('updatedAt')->useCurrent()->useCurrentOnUpdate();

            // Foreign keys
            $table->foreign('collegeId')->references('collegeId')->on('colleges')->onDelete('cascade');
            $table->foreign('semesterId')->references('semesterId')->on('semesters')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
