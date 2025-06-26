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
        Schema::create('request_upload_access', function (Blueprint $table) {
            $table->id('request_upload_id'); 
            $table->foreignId('user_login_id') 
                  ->constrained('user_login', 'user_login_id') 
                  ->onDelete('cascade'); 
            $table->string('reason');
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
        Schema::dropIfExists('request_upload_access');
    }
};
