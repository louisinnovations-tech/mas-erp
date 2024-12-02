<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type');
            $table->bigInteger('size');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('meeting_id')->nullable()->constrained()->onDelete('cascade');
            $table->json('permissions')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('document_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained()->onDelete('cascade');
            $table->string('path');
            $table->integer('version');
            $table->bigInteger('size');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('comment')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('document_versions');
        Schema::dropIfExists('documents');
    }
};