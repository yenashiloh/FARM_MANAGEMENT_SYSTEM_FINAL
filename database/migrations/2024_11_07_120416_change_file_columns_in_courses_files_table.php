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
        Schema::table('courses_files', function (Blueprint $table) {
            // Change the size of 'files' column to varchar(1000)
            $table->string('files', 1000)->change();
            // Change the size of 'original_file_name' column to varchar(1000)
            $table->string('original_file_name', 1000)->change();
        });
    }

    public function down()
    {
        Schema::table('courses_files', function (Blueprint $table) {
            // Revert back to original sizes
            $table->string('files', 255)->change();
            $table->string('original_file_name', 255)->change();
        });
    }
};
