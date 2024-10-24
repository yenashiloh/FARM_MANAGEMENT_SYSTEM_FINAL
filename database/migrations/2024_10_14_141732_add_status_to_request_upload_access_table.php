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
        Schema::table('request_upload_access', function (Blueprint $table) {
            $table->enum('status', ['unread', 'read'])->default('unread')->after('reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('request_upload_access', function (Blueprint $table) {
            //
        });
    }
};
