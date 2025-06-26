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
        Schema::table('user_login', function (Blueprint $table) {
            $table->enum('department', [
                'College of Engineering',
                'College of Education',
                'College of Accountant',
                'College of Business Administration',
                'College of Information Technology',
                'College of Office Administration',
                'College of Psychology',
            ])->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_login', function (Blueprint $table) {
            $table->enum('department', [
                'math',
                'IT',
                'english'
            ])->nullable()->change();
        });
    }
};
