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
        Schema::table('notifications', function (Blueprint $table) {
            $table->unsignedBigInteger('sender_user_login_id')->nullable(); 
            $table->foreign('sender_user_login_id')->references('user_login_id')->on('user_login')->onDelete('cascade');
        });
    }
    
    public function down()
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn('sender_user_login_id');
        });
    }
    
};
