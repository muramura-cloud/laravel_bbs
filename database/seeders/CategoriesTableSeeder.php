<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategoriesTableSeeder extends Seeder
{
    public function run()
    {
        $names = [
            100 => 'マクロ経済学',
            200 => 'ミクロ経済学',
            300 => '統計学',
            400 => '経済数学',
            500 => '経営学',
            600 => '会計学',
            700 => '経済史',
            800 => '簿記',
            900 => '金融',
            999 => 'その他'
        ];

        foreach ($names as $id => $name) {
            $category = new Category;
            $category->id = $id;
            $category->name = $name;
            $category->save();
        }
    }
}
