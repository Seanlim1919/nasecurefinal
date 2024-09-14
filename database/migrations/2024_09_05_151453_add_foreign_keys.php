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
        Schema::table('schedules', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('class_sessions', function (Blueprint $table) {
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->foreign('program_id')->references('id')->on('programs')->onDelete('set null');
        });

        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
        });

        Schema::table('sections', function (Blueprint $table) {
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::table('borrowed_kits', function (Blueprint $table) {
            $table->foreign('kit_id')->references('id')->on('kits')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('borrowed_kits', function (Blueprint $table) {
            $table->dropForeign(['kit_id']);
        });

        Schema::table('kits', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['section_id']);
            $table->dropForeign(['created_by']);
        });

        Schema::table('class_sessions', function (Blueprint $table) {
            $table->dropForeign(['instructor_id']);
            $table->dropForeign(['course_id']);
        });

        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->dropForeign(['section_id']);
            $table->dropForeign(['student_id']);
        });

        Schema::table('sections', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
            $table->dropForeign(['created_by']);
        });
    }
};
