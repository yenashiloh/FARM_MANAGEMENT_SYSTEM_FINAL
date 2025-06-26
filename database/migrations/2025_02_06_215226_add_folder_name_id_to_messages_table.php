<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->unsignedBigInteger('folder_name_id')->after('courses_files_id');
            $table->foreign('folder_name_id')->references('folder_name_id')->on('folder_name')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['folder_name_id']);
            $table->dropColumn('folder_name_id');
        });
    }
};
