<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateRoleEnumInUserLoginTable extends Migration
{
    public function up()
    {
        Schema::table('user_login', function (Blueprint $table) {
            $table->enum('role', ['admin', 'faculty', 'director', 'faculty-coordinator'])->change();
        });
    }

    public function down()
    {
        Schema::table('user_login', function (Blueprint $table) {
            $table->enum('role', ['admin', 'faculty', 'director'])->change();
        });
    }
}
