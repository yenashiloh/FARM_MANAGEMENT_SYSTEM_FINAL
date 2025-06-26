<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reminder_tracking', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_login_id');
            $table->date('sent_date');
            $table->timestamps();
            
            $table->foreign('user_login_id')->references('user_login_id')->on('user_login');
            $table->unique(['user_login_id', 'sent_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('reminder_tracking');
    }
};
