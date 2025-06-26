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
            $table->string('Fcode', 50)->nullable()->after('user_login_id');
            $table->string('surname')->nullable()->after('Fcode');
            $table->string('first_name')->nullable()->after('surname');
            $table->string('middle_name')->nullable()->after('first_name');
            $table->string('name_extension', 10)->nullable()->after('middle_name');
            $table->enum('employment_type', ['fulltime', 'parttime', 'temporary'])->nullable()->after('name_extension');
            $table->enum('department', ['math', 'IT', 'english'])->nullable()->after('employment_type');
        });
    }

    public function down()
    {
        Schema::table('user_login', function (Blueprint $table) {
            $table->dropColumn(['Fcode', 'surname', 'first_name', 'middle_name', 'name_extension', 'employment_type', 'department']);
        });
    }
};
