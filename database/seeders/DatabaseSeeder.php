<?php

namespace Database\Seeders;

use App\Models\TodoList;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun Pemilik List (Budi)
        $budi = User::create([
            'name' => 'Budi Santoso (Pemilik)',
            'email' => 'budi@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        // 2. Akun Anggota yang sudah bergabung (Siti)
        $siti = User::create([
            'name' => 'Siti Aminah (Anggota)',
            'email' => 'siti@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        // 3. Akun Calon Anggota yang akan diundang (Joko)
        $joko = User::create([
            'name' => 'Joko Widodo (Diundang)',
            'email' => 'joko@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        // 4. Sample List dibuat oleh Budi
        $list = TodoList::create([
            'name' => 'Proyek Pengembangan Jara To-Do List',
            'owner_id' => $budi->id,
        ]);
    }
}
