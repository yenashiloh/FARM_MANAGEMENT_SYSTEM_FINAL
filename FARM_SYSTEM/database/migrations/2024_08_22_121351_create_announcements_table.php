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
        Schema::create('announcements', function (Blueprint $table) {
            $table->bigIncrements('id_announcement'); 
            $table->string('subject'); 
            $table->text('message'); 
            $table->string('type_of_recepient'); 
            $table->boolean('published'); 
            $table->unsignedBigInteger('user_login_id'); 
            $table->timestamps(); 

            $table->foreign('user_login_id')->references('user_login_id')->on('user_login')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('announcements');
    }
};
