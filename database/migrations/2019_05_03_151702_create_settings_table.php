<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('module');
            $table->string('config_key');
            $table->string('config_val');
            $table->string('data_type');
            $table->string('resource_url')->nullable();
            $table->string('label');
            $table->text('description')->nullable();
            $table->boolean('allow_null')->default(1);
            $table->string('roles')->default('1');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
