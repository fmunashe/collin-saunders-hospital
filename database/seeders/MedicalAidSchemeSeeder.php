<?php

namespace Database\Seeders;

use App\Models\MedicalAidScheme;
use Illuminate\Database\Seeder;

class MedicalAidSchemeSeeder extends Seeder
{
    public function run(): void
    {
        $schemes = [
            ['name' => 'Discovery Health', 'code' => 'DISC'],
            ['name' => 'Bonitas', 'code' => 'BON'],
            ['name' => 'GEMS', 'code' => 'GEMS'],
            ['name' => 'Momentum Health', 'code' => 'MOM'],
            ['name' => 'Medihelp', 'code' => 'MHLP'],
            ['name' => 'Bestmed', 'code' => 'BEST'],
            ['name' => 'Fedhealth', 'code' => 'FED'],
            ['name' => 'Sizwe Medical Fund', 'code' => 'SIZ'],
            ['name' => 'Polmed', 'code' => 'POL'],
            ['name' => 'PSMAS', 'code' => 'PSMAS'],
            ['name' => 'CIMAS', 'code' => 'CIMAS'],
        ];

        foreach ($schemes as $scheme) {
            MedicalAidScheme::firstOrCreate(['code' => $scheme['code']], $scheme);
        }
    }
}
