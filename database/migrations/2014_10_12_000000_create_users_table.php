<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name_user')->nullable();
            $table->string('last_name_user')->nullable();
            $table->string('telephone_user')->nullable();
            $table->string('email_user')->nullable();
            $table->string('password_user')->nullable();
            $table->integer('state_user')->nullable()->default(0);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('user_edite_id')->nullable();
            $table->integer('delete_user')->nullable()->default(0);
            $table->unsignedBigInteger('user_delete_id')->nullable();

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('user_edite_id')->references('id')->on('users');
            $table->foreign('user_delete_id')->references('id')->on('users');
            Schema::enableForeignKeyConstraints();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
