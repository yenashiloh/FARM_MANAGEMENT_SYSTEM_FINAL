<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropOauthTables extends Migration
{
    public function up()
    {
        Schema::dropIfExists('oauth_access_tokens');
        Schema::dropIfExists('oauth_auth_codes');
        Schema::dropIfExists('oauth_clients');
        Schema::dropIfExists('oauth_personal_access_clients');
        Schema::dropIfExists('oauth_refresh_tokens');
        Schema::dropIfExists('personal_access_tokens');
    }

    public function down()
    {
        Schema::create('oauth_access_tokens', function (Blueprint $table) {
            $table->id();
        });

        Schema::create('oauth_auth_codes', function (Blueprint $table) {
            $table->id();
        });

        Schema::create('oauth_clients', function (Blueprint $table) {
            $table->id();
        });

        Schema::create('oauth_personal_access_clients', function (Blueprint $table) {
            $table->id();
        });

        Schema::create('oauth_refresh_tokens', function (Blueprint $table) {
            $table->id();
        });

        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
        });
    }
}
