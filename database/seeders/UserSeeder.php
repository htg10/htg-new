<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\carbon;
use App\Models\User;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {

        $numberOfUsers = \DB::table('users')->count();
        $numberOfProducts = \DB::table('products')->count();

        // user
        if ($numberOfUsers == 0) {

            DB::table('users')->insert([
                'role_id' => 1,
                'name' => 'Help Together Group',
                'email' => 'info.helptogethergroup@gmail.com',
                'email_verified_at' => carbon::now(),
                'password' => bcrypt('Htg@5050'),
                'created_at' => carbon::now(),
                'updated_at' => carbon::now()
            ]);

        }
    }
}
