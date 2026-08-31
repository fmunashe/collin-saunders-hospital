<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('admission_notes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('admission_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type')->default('general'); // doctor, nurse, observation, procedure, general
            $table->dateTime('noted_at');
            $table->text('note');
            $table->timestamps();

            $table->index(['admission_id', 'noted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admission_notes');
    }
};
