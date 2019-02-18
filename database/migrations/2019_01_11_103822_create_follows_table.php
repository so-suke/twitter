<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFollowsTable extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::create('follows', function (Blueprint $table) {
      $table->increments('id');
      $table->unsignedInteger('from_user_id');
      $table->unsignedInteger('to_user_id');
      $table->timestamps();

      $table->foreign('from_user_id')->references('id')->on('users');
      $table->foreign('to_user_id')->references('id')->on('users');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::dropIfExists('follows');
  }
}
