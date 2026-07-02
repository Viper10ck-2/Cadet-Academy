<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $adminRole = Role::create(['name' => 'admin']);
        $instructorRole = Role::create(['name' => 'instructor']);
        $cadetRole = Role::create(['name' => 'cadet']);

        // Create permissions
        Permission::create(['name' => 'manage users']);
        Permission::create(['name' => 'manage exams']);
        Permission::create(['name' => 'view results']);
        Permission::create(['name' => 'take exams']);
        Permission::create(['name' => 'do attendance']);

        // Assign permissions to roles
        $adminRole->givePermissionTo(Permission::all());
        $instructorRole->givePermissionTo(['manage exams', 'view results']);
        $cadetRole->givePermissionTo(['take exams', 'do attendance']);

        // Create admin user
        $admin = User::create([
            'name' => 'Admin Cadet Academy',
            'email' => 'admin@cadetacademy.test',
            'nip_nis' => 'ADM001',
            'phone' => '081234567890',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');

        // Create sample instructor
        $instructor = User::create([
            'name' => 'Instruktur Utama',
            'email' => 'instructor@cadetacademy.test',
            'nip_nis' => 'INS001',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $instructor->assignRole('instructor');

        // Create sample cadets
        $cadet1 = User::create([
            'name' => 'Cadet Satu',
            'email' => 'cadet1@cadetacademy.test',
            'nip_nis' => 'CDT001',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $cadet1->assignRole('cadet');

        $cadet2 = User::create([
            'name' => 'Cadet Dua',
            'email' => 'cadet2@cadetacademy.test',
            'nip_nis' => 'CDT002',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $cadet2->assignRole('cadet');
    }
}
