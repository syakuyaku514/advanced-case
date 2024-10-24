<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;

class AdminTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Admin::create([
          'id' => 1,
          'name' => '山田太郎',
          'email' => 'test@taro.com',
          'password' => bcrypt('passwordtaro'),
          'role' => 'admin',
        ]);

        Admin::create([
          'id' => 2,
          'name' => '山田花子',
          'email' => 'test@hanako.com',
          'password' => bcrypt('passwordhanako'),
          'role' => 'admin',
        ]);
    }
}
