<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::table('users')->insert([
            'name' => "admin",
            'email' => 'admin@admin.com',
            'password' => bcrypt('123456'),
            'remember_token' => str_random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
         $this->call(UserTableSeeder::class);
    }
}
