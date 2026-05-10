<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $org = Organization::create([
            'name' => 'Masjid Al-Ikhlas',
            'slug' => 'masjid-al-ikhlas',
            'city' => 'Jakarta',
            'address' => 'Jl. Merdeka No. 1',
        ]);

        User::create([
            'organization_id' => $org->id,
            'name' => 'Admin Qurban',
            'email' => 'admin@qurban.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'organization_id' => $org->id,
            'name' => 'Panitia Lapangan',
            'email' => 'panitia@qurban.test',
            'password' => Hash::make('password'),
            'role' => 'panitia',
        ]);
    }
}
