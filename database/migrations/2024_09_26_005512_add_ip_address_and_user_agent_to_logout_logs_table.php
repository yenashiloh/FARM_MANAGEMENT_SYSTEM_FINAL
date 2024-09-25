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
            $table->string('ip_address', 45)->after('logout_time')->nullable();
            $table->text('user_agent')->after('ip_address')->nullable();
        });
    }

    public function down()
    {
        Schema::table('logout_logs', function (Blueprint $table) {
            $table->dropColumn(['ip_address', 'user_agent']);
        });
    }
};
