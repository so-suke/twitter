<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RetweetsTableSeeder extends Seeder {
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    DB::table('retweets')->truncate();
    DB::statement('SET FOREIGN_KEY_CHECKS=1');

    $retweets = [
      [
        'user_id' => 1,
        'tweet_ids' => [10, 13],
      ],
      [
        'user_id' => 2,
        'tweet_ids' => [1, 9],
      ],
      [
        'user_id' => 3,
        'tweet_ids' => [2, 14],
      ],
      [
        'user_id' => 4,
        'tweet_ids' => [3, 7],
      ],
    ];

    $now = Carbon::now();

    foreach ($retweets as $key => $retweet) {
      $user_id = $retweet['user_id'];
      $tweet_ids = $retweet['tweet_ids'];
      foreach ($tweet_ids as $key => $tweet_id) {
        DB::table('retweets')->insert([
          'user_id' => $user_id,
          'tweet_id' => $tweet_id,
          'created_at' => $now->format('Y-m-d H:i:s'),
          'updated_at' => $now->format('Y-m-d H:i:s'),
        ]);
        $now->addMinute();
      }
    }

  }
}
