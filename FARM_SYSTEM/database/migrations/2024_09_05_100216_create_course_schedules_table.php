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
        Schema::create('course_schedules', function (Blueprint $table) {
            $table->unsignedBigInteger('course_schedule_id')->primary();
            $table->unsignedBigInteger('user_login_id'); 
            $table->string('sem_academic_year'); 
            $table->string('program'); 
            $table->string('course_code'); 
            $table->string('course_subjects');  
            $table->string('year_section'); 
            $table->string('schedule'); 

            $table->foreign('user_login_id')
                  ->references('user_login_id')
                  ->on('user_login')
                  ->onDelete('cascade');

            $table->timestamps(); 
        });
    }

    public function down()
    {
        Schema::dropIfExists('course_schedules');
    }
};
