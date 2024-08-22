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
        Schema::table('courses_files', function (Blueprint $table) {
            $table->unsignedBigInteger('folder_name_id')->after('user_login_id'); 
         
            $table->foreign('folder_name_id')->references('folder_name_id')->on('folder_name')
                  ->onDelete('cascade'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('courses_files', function (Blueprint $table) {
            $table->dropForeign(['folder_name_id']);
            $table->dropColumn('folder_name_id');
        });
    }
};
