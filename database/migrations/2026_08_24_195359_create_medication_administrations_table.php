<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('medication_administrations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('admission_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('medication_id')->constrained();
            $table->foreignUuid('administered_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('dosage');
            $table->string('route'); // oral, iv, im, sc, topical, etc.
            $table->dateTime('administered_at');
            $table->dateTime('scheduled_at')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('administered'); // administered, missed, refused, held
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medication_administrations');
    }
};
