<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;


class AdminTableSeeder extends Seeder
{
    public function run()
    {
        $param = [
            'name' => 'ムラタリク',
            'email' => 'clu363721@gmail.com',
            'password' => password_hash(11111111, PASSWORD_DEFAULT),
        ];

        $admin = new Admin;

        $admin->fill($param)->save();
    }
}
