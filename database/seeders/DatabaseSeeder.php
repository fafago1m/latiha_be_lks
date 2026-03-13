<?php

namespace Database\Seeders;

use App\Models\UmkmProfile;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $userUmkm = User::create([
            'id' => Str::uuid(),
            'name' => 'fafa UMKM',
            'email' => 'fafa@gmail.com',
            'password' => Hash::make('fafa2222'),
            'role' => 'umkm'
        ]);

        UmkmProfile::create([
            'id' => Str::uuid(),
            'user_id' => $userUmkm->id,
            'nama_usaha' => 'Warung',
            'alamat' => 'bantul',
            'omzet_bulanan' => 100000 
        ]);

        
        User::create([
            'id' => Str::uuid(),
            'name' => 'approver',
            'email' => 'approver@gmail.com',
            'password' => Hash::make('admin123'),
            'role' => 'approver'
        ]);
    }
}
