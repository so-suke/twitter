<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FollowsTableSeeder extends Seeder {
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    DB::table('follows')->truncate();
    DB::statement('SET FOREIGN_KEY_CHECKS=1');

    $follows = [
      [
        'from_user_id' => 1,
        'to_user_ids' => [2, 3, 4, 5],
      ],
      [
        'from_user_id' => 2,
        'to_user_ids' => [1, 3, 5, 7, 9],
      ],
      [
        'from_user_id' => 3,
        'to_user_ids' => [4, 5],
      ],
      [
        'from_user_id' => 4,
        'to_user_ids' => [1, 2],
      ],
      [
        'from_user_id' => 5,
        'to_user_ids' => [1, 2, 3],
      ],
      [
        'from_user_id' => 6,
        'to_user_ids' => [1, 5],
      ],
      [
        'from_user_id' => 7,
        'to_user_ids' => [1, 4],
      ],
      [
        'from_user_id' => 8,
        'to_user_ids' => [2],
      ],
    ];

    $now = Carbon::now();

    foreach ($follows as $key => $follow) {
      $from_user_id = $follow['from_user_id'];
      $to_user_ids = $follow['to_user_ids'];
      foreach ($to_user_ids as $key => $to_user_id) {
        DB::table('follows')->insert([
          'from_user_id' => $from_user_id,
          'to_user_id' => $to_user_id,
          'created_at' => $now->format('Y-m-d H:i:s'),
          'updated_at' => $now->format('Y-m-d H:i:s'),
        ]);
        $now->addMinute();
      }
    }

  }
}
