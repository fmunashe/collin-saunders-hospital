<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Billing Defaults
    |--------------------------------------------------------------------------
    |
    | Default charges used when auto-generating invoices. Amounts are in the
    | configured currency (see config/invoices.php).
    |
    */

    'billing' => [
        // Standard outpatient consultation fee
        'consultation_fee' => env('HMS_CONSULTATION_FEE', 350.00),

        // Daily bed/ward charge applied per night of admission
        'bed_day_rate' => [
            'general' => env('HMS_RATE_GENERAL', 800.00),
            'icu' => env('HMS_RATE_ICU', 3500.00),
            'maternity' => env('HMS_RATE_MATERNITY', 1500.00),
            'paediatric' => env('HMS_RATE_PAEDIATRIC', 1200.00),
        ],

        // Default admission fee (one-off, on admission)
        'admission_fee' => env('HMS_ADMISSION_FEE', 500.00),
    ],

    /*
    |--------------------------------------------------------------------------
    | Pharmacy
    |--------------------------------------------------------------------------
    */

    'pharmacy' => [
        // Days before expiry to start warning about medication
        'expiry_warning_days' => env('HMS_EXPIRY_WARNING_DAYS', 90),
    ],

];
