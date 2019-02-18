<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfileseTable extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::create('profilese', function (Blueprint $table) {
      $table->increments('id');
      $table->unsignedInteger('user_id');
      $table->string('text');
      $table->string('header_img_path');
      $table->string('avatar_img_path');
      $table->timestamps();

      $table->foreign('user_id')->references('id')->on('users');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::dropIfExists('profilese');
  }
}
