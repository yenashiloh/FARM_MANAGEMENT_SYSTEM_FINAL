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
        Schema::create('folder_name', function (Blueprint $table) {
            $table->id('folder_name_id');
            $table->string('folder_name');
            $table->string('main_folder_name'); 
            $table->unsignedBigInteger('user_login_id');
            $table->foreign('user_login_id')->references('user_login_id')->on('user_login')->onDelete('cascade'); 
            $table->timestamps(); 
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('folder_name');
    }
};
