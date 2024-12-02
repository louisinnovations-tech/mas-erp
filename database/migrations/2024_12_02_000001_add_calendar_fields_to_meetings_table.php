<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->string('google_event_id')->nullable();
            $table->string('outlook_event_id')->nullable();
            $table->json('calendar_metadata')->nullable();
        });
    }

    public function down()
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->dropColumn(['google_event_id', 'outlook_event_id', 'calendar_metadata']);
        });
    }
};