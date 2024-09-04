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
        Schema::create('folder_inputs', function (Blueprint $table) {
            $table->id('folder_input_id');
            $table->unsignedBigInteger('folder_name_id');
            $table->string('input_label');
            $table->enum('input_type', ['text', 'file']);
            $table->text('input_value')->nullable();
            $table->timestamps();

            $table->foreign('folder_name_id')->references('folder_name_id')->on('folder_name')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('folder_inputs');
    }
};
