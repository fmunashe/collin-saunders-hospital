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
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@hms.local',
        ]);

        $noRoleUser = User::factory()->create([
            'name' => 'New User',
            'email' => 'user@hms.local',
        ]);

        $supportUser = User::factory()->create([
            'name' => 'Support User',
            'email' => 'support@hms.local',
        ]);

        $this->call([
            DepartmentSeeder::class,
            MedicalAidSchemeSeeder::class,
            RolesAndPermissionsSeeder::class,
            TestDataSeeder::class,
        ]);

        $admin->assignRole('admin');

        // Support user gets only dashboard and user access
        $supportUser->assignRole('support_staff');
        $supportUser->givePermissionTo(['user-access', 'user-show']);
    }
}
