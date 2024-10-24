<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(GenresTableSeeder::class);

        $this->call(RegionsTableSeeder::class);

        $this->call(StoresTableSeeder::class);

        $this->call(AdminTableSeeder::class);
    }
}
