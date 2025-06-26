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
        Schema::create('logout_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_login_id');
            $table->timestamp('logout_time');
            $table->foreign('user_login_id')->references('user_login_id')->on('user_login')->onDelete('cascade');
            $table->timestamps();
        });
    }    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logout_logs');
    }
};
