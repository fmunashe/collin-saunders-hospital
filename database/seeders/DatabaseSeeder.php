<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@hms.local',
        ]);

        $this->call([
            DepartmentSeeder::class,
            MedicalAidSchemeSeeder::class,
            RolesAndPermissionsSeeder::class,
        ]);

        $user->assignRole('admin');
    }
}
