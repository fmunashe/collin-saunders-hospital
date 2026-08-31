<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportPdfTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles & permissions (uses the app's UUID-aware creation path).
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    private function adminUser(): User
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        return $user->fresh();
    }

    public function test_authenticated_user_with_permission_downloads_pdf(): void
    {
        $user = $this->adminUser();

        $response = $this->actingAs($user)->get('/nova/reports/financial-reports/pdf');

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_unknown_report_returns_404(): void
    {
        $user = $this->adminUser();

        $this->actingAs($user)->get('/nova/reports/does-not-exist/pdf')->assertNotFound();
    }
}
