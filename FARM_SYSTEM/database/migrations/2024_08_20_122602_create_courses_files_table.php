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
        Schema::create('courses_files', function (Blueprint $table) {
            $table->id('courses_files_id'); 
            $table->unsignedBigInteger('user_login_id'); 
            $table->string('files');
            $table->timestamps(); 
            $table->foreign('user_login_id')->references('user_login_id')->on('user_login')
                  ->onDelete('cascade'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('courses_files');
    }
};
