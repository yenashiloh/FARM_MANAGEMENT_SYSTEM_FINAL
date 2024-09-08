<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('courses_files', function (Blueprint $table) {
            // Add the column without the foreign key constraint first
            $table->unsignedBigInteger('course_schedule_id')->nullable()->after('folder_name_id');
        });


        Schema::table('courses_files', function (Blueprint $table) {
            $table->foreign('course_schedule_id')->references('course_schedule_id')->on('course_schedules')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('courses_files', function (Blueprint $table) {
            $table->dropForeign(['course_schedule_id']);
            $table->dropColumn('course_schedule_id');
        });
    }

    
};
