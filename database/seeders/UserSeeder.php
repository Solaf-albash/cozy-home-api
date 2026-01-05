<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'first_name'            => 'Alaa',
            'last_name'             => 'ALasrawi',
            'phonenumber'           => '0937268506',
            'password'              => Hash::make('lolo2003lolo'),
            'role'                  => 'admin',
            'status'                => 'approved',
            'birth_date'            => '2003-01-01',
            'profile_image_path'    => 'images/admin-image.png',
            'id_image_path'         => 'seed/ids/admin.png',
        ]);


        User::create([
            'first_name'            => 'Owner',
            'last_name'             => 'Test',
            'phonenumber'           => '0922222222',
            'password'              => Hash::make('password'),
            'role'                  => 'owner',
            'status'                => 'approved',
            'birth_date'            => '1995-01-01',
            'profile_image_path'    => 'seed/avatars/owner.png',
            'id_image_path'         => 'seed/ids/owner.png',
        ]);
        User::create([
            'first_name'            => 'Renter',
            'last_name'             => 'Test',
            'phonenumber'           => '0933333333',
            'password'              => Hash::make('password'),
            'role'                  => 'renter',
            'status'                => 'approved',
            'birth_date'            => '1998-01-01',
            'profile_image_path'    => 'seed/avatars/renter.png',
            'id_image_path'         => 'seed/ids/renter.png',
        ]);
        User::create([
            'first_name'            => 'Renter2',
            'last_name'             => 'Test',
            'phonenumber'           => '0933333339',
            'password'              => Hash::make('password'),
            'role'                  => 'renter',
            'status'                => 'approved',
            'birth_date'            => '1998-01-01',
            'profile_image_path'    => 'seed/avatars/renter.png',
            'id_image_path'         => 'seed/ids/renter.png',
        ]);
    }
}
