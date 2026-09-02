<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Billing
            ['group' => 'billing', 'key' => 'hms.billing.consultation_fee', 'label' => 'Consultation Fee', 'type' => 'decimal', 'value' => 350.00, 'description' => 'Standard outpatient consultation fee (USD).'],
            ['group' => 'billing', 'key' => 'hms.billing.admission_fee', 'label' => 'Admission Fee', 'type' => 'decimal', 'value' => 500.00, 'description' => 'One-off fee applied on admission (USD).'],
            ['group' => 'billing', 'key' => 'hms.billing.bed_day_rate.general', 'label' => 'Bed Day Rate — General Ward', 'type' => 'decimal', 'value' => 800.00, 'description' => 'Daily charge per night in a general ward (USD).'],
            ['group' => 'billing', 'key' => 'hms.billing.bed_day_rate.icu', 'label' => 'Bed Day Rate — ICU', 'type' => 'decimal', 'value' => 3500.00, 'description' => 'Daily charge per night in ICU (USD).'],
            ['group' => 'billing', 'key' => 'hms.billing.bed_day_rate.maternity', 'label' => 'Bed Day Rate — Maternity', 'type' => 'decimal', 'value' => 1500.00, 'description' => 'Daily charge per night in maternity (USD).'],
            ['group' => 'billing', 'key' => 'hms.billing.bed_day_rate.paediatric', 'label' => 'Bed Day Rate — Paediatric', 'type' => 'decimal', 'value' => 1200.00, 'description' => 'Daily charge per night in paediatric (USD).'],

            // Pharmacy
            ['group' => 'pharmacy', 'key' => 'hms.pharmacy.expiry_warning_days', 'label' => 'Expiry Warning Days', 'type' => 'integer', 'value' => 90, 'description' => 'Days before expiry to start flagging medication.'],
        ];

        foreach ($settings as $s) {
            Setting::updateOrCreate(
                ['key' => $s['key']],
                [
                    'group' => $s['group'],
                    'label' => $s['label'],
                    'type' => $s['type'],
                    'description' => $s['description'],
                    // Only set value on first creation; don't overwrite admin edits on re-seed.
                    'value' => Setting::where('key', $s['key'])->exists()
                        ? Setting::where('key', $s['key'])->value('value')
                        : $s['value'],
                ]
            );
        }
    }
}
