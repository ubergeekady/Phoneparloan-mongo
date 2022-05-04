<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Add this lines
        \App\Models\User::query()->truncate(); // truncate user table each time of seeders run
        \App\Models\User::create([ // create a new user
            'mobile'=>'8527822269',
            'email' => 'admin@admin.com',
            'password' => \Illuminate\Support\Facades\Hash::make('admin'),
            'name' => 'Administrator'
        ]);
    }
}
