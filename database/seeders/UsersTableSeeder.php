<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;


class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        try {

            User::firstOrCreate(
                [
                    'name'      => 'admin',
                    'email'     => 'admin@gmail.com',
                    'password'  =>  Hash::make('admin123'),
                    'medium_integration_token' => '2793ade27b4c9178f96746e138bdf0409ac8721772b553a50e50fdb595174d9d3'
                ]
            );
        } catch (\Exception $e) {
        }
    }
}
