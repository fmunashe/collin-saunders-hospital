<?php

namespace Tests\Feature;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\SettingsSeeder::class);
    }

    public function test_settings_are_seeded_from_config_defaults(): void
    {
        $this->assertDatabaseHas('settings', ['key' => 'hms.billing.consultation_fee', 'value' => '350']);
        $this->assertDatabaseHas('settings', ['key' => 'hms.pharmacy.expiry_warning_days', 'value' => '90']);
        $this->assertSame(7, Setting::count());
    }

    public function test_values_cast_to_their_declared_type(): void
    {
        $keyed = Setting::allKeyed();

        $this->assertIsFloat($keyed['hms.billing.consultation_fee']);
        $this->assertIsInt($keyed['hms.pharmacy.expiry_warning_days']);
    }

    public function test_updating_a_setting_overrides_the_file_config(): void
    {
        Setting::where('key', 'hms.billing.consultation_fee')->first()->update(['value' => 999.99]);

        // Re-run the provider overlay (mirrors what happens on the next request boot).
        foreach (Setting::allKeyed() as $key => $value) {
            config([$key => $value]);
        }

        $this->assertEquals(999.99, config('hms.billing.consultation_fee'));
    }

    public function test_saving_a_setting_busts_the_cache(): void
    {
        Setting::allKeyed(); // warm the cache

        Setting::where('key', 'hms.pharmacy.expiry_warning_days')->first()->update(['value' => 30]);

        $this->assertSame(30, Setting::allKeyed()['hms.pharmacy.expiry_warning_days']);
    }
}
