<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('prescription_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('prescription_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('medication_id')->constrained();
            $table->string('dosage');
            $table->integer('quantity');
            $table->integer('duration_days')->nullable();
            $table->text('instructions')->nullable();
            $table->boolean('dispensed')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prescription_items');
    }
};
