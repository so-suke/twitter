<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TweetsTableSeeder extends Seeder {
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    DB::table('tweets')->truncate();
    DB::statement('SET FOREIGN_KEY_CHECKS=1');

    $user_ids = [1, 2, 3, 4, 5];

    foreach ($user_ids as $key => $user_id) {
      for ($create_cnt = 0; $create_cnt < 3; $create_cnt++) {
        DB::table('tweets')->insert([
          'user_id' => $user_id,
          'text' => 'user_id_' . $user_id . '_text_' . $create_cnt,
          'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
          'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
      }
    }

  }
}
