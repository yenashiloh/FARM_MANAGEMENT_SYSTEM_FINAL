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
        Schema::table('logout_logs', function (Blueprint $table) {
            $table->string('logout_message')->nullable(); 
        });
    }

    public function down()
    {
        Schema::table('logout_logs', function (Blueprint $table) {
            $table->dropColumn('logout_message');
        });
    }
};
