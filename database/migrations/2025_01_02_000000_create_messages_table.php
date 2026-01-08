<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('session_id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->text('content');
            $table->boolean('is_admin')->default(false);
            $table->timestamps();

            $table->foreign('user_id')->references('id_utilisateur')->on('utilisateur')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('messages');
    }
};