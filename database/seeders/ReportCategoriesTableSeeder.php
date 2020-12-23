<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ReportCategory;

class ReportCategoriesTableSeeder extends Seeder
{
    public function run()
    {
        // 違反内容
        $names = [
            100 => '暴力的なコンテンツ',
            200 => '差別的なコンテンツ',
            300 => '性的なコンテンツ',
            400 => '有害なコンテンツ',
            500 => 'スパム的なコンテンツ',
            999 => 'その他'
        ];

        foreach ($names as $id => $name) {
            $category = new ReportCategory;
            $category->id = $id;
            $category->name = $name;
            $category->save();
        }
    }
}
