<?php

use App\User;
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
        User::create([
            'name' => 'xtrime',
            'email' => 'alexander@i-c-a.su',
            'email_verified_at' => time(),
            'role' => 'admin',
            'password' => Hash::make(env('ROOT_PASSWORD')),
            'api_token' => Str::random(30),
        ])->save();
    }
}
