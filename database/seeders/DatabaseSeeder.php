<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // $this->call(PostTableSeeder::class);
        $this->call(ReportCategoriesTableSeeder::class);
    }
}
