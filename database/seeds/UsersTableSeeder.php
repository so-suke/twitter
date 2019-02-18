<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder {
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    DB::table('users')->truncate();
    DB::statement('SET FOREIGN_KEY_CHECKS=1');

    for ($create_cnt = 0; $create_cnt < 10; $create_cnt++) {
      $hoge = DB::table('users')->insert([
        'name' => 'name_' . $create_cnt,
        'screen_name' => 'screen_name_' . $create_cnt,
        'email' => 'a' . $create_cnt . '@gmail.com',
        'password' => bcrypt('p' . $create_cnt),
      ]);
    }
  }
}
