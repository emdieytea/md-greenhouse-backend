<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create admin 1
        User::updateOrCreate(
            [
                'name' => 'Greenhouse Admin',
                'username' => 'greenhouse-admin',
                'email' => 'greenhouse-admin@emdieytea.com',
            ],
            [
                'email_verified_at' => null, // Carbon::now()->toDateTimeString(),
                'password' => Hash::make('password'),
            ]
        );
    }
}
