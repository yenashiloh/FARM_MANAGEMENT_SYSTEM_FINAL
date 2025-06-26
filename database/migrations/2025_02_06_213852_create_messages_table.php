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
        Schema::create('messages', function (Blueprint $table) {
            $table->id('messages_id');
            $table->unsignedBigInteger('user_login_id');
            $table->unsignedBigInteger('courses_files_id');
            $table->text('message_body');
            $table->timestamps();

            $table->foreign('user_login_id')->references('user_login_id')->on('user_login')->onDelete('cascade');
            $table->foreign('courses_files_id')->references('courses_files_id')->on('courses_files')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
