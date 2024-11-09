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
            $table->string('school_year')->nullable()->after('semester'); 
        });
    }
    
    public function down()
    {
        Schema::table('courses_files', function (Blueprint $table) {
            $table->dropColumn('school_year');
        });
    }
    
};
