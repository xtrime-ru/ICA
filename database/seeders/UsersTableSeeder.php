<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{

    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        $user = User::query()->firstOrNew(
            ['email' => 'alexander@i-c-a.su'],
            [
                'name' => 'xtrime',
                'email_verified_at' => time(),
                'role' => 'admin',
                'password' => Hash::make(env('ROOT_PASSWORD')),
            ]
        );

        if ($user->doesntExist()) {
            $user->save();
        }

        $user->createToken('app');

    }
}
