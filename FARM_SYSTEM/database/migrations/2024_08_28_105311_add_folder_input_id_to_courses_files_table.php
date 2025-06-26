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
            $table->unsignedBigInteger('folder_input_id')->nullable()->after('folder_name_id');
            $table->foreign('folder_input_id')->references('folder_input_id')->on('folder_inputs')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('courses_files', function (Blueprint $table) {
            $table->dropForeign(['folder_input_id']);
            $table->dropColumn('folder_input_id');
        });
    }
};
