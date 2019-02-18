<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProfileseTableSeeder extends Seeder {
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    DB::table('profilese')->truncate();
    DB::statement('SET FOREIGN_KEY_CHECKS=1');

    $user_ids = range(1, 10);

    foreach ($user_ids as $key => $user_id) {
      DB::table('profilese')->insert([
        'user_id' => $user_id,
        'text' => 'profile_user_id_' . $user_id . '_text',
        'header_img_path' => 'profile_header_imgs/default.jpeg',
        'avatar_img_path' => 'profile_avatar_imgs/default.jpeg',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
      ]);
    }
  }
}
