<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('group')->default('general');   // billing, pharmacy, ...
            $table->string('key')->unique();               // dot key, e.g. hms.billing.consultation_fee
            $table->string('label');                       // human friendly name
            $table->text('description')->nullable();
            $table->string('type')->default('string');     // string, integer, decimal, boolean
            $table->text('value')->nullable();
            $table->timestamps();

            $table->index('group');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
