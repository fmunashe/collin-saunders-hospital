<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('wards', function (Blueprint $table) {
            // null = any gender; 'male' / 'female' = restricted to that gender.
            $table->string('gender_restriction')->nullable()->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('wards', function (Blueprint $table) {
            $table->dropColumn('gender_restriction');
        });
    }
};
