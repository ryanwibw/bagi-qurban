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

        $admin = User::create([
            'name' => 'Admin Qurban',
            'email' => 'admin@qurban.test',
            'password' => Hash::make('password'),
        ]);

        $admin->organizations()->attach($org->id, ['role' => 'admin']);
        $org->update(['owner_id' => $admin->id]);

        $panitia = User::create([
            'name' => 'Panitia Lapangan',
            'email' => 'panitia@qurban.test',
            'password' => Hash::make('password'),
        ]);

        $panitia->organizations()->attach($org->id, ['role' => 'panitia']);
    }
}
