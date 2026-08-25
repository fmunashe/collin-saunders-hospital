<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
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
            TestDataSeeder::class,
        ]);

        $user->assignRole('admin');
    }
}
