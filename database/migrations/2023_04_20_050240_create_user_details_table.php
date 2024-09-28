<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->bigInteger('mobile_number')->nullable();
            $table->bigInteger('whats_app_number')->nullable();
            $table->longText('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('building_number')->nullable();
            $table->string('street_number')->nullable();
            $table->string('zone_number')->nullable();
            $table->bigInteger('land_phone')->nullable();
            $table->bigInteger('extension_number')->nullable();
            $table->string('profession')->nullable();
            $table->string('department')->nullable();
            $table->string('language')->nullable();
            $table->bigInteger('zip_code')->nullable();
            $table->string('landmark')->nullable();
            $table->string('qid_number')->nullable();
            $table->string('passport_number')->nullable();
            $table->longText('about')->nullable();
            $table->string('my_group')->default(0);
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
        Schema::dropIfExists('user_details');
    }
};
