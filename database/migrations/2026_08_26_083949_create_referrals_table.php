<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('referrals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('patient_id')->constrained();
            $table->foreignUuid('referring_doctor_id')->constrained('doctors');
            $table->foreignUuid('visit_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('admission_id')->nullable()->constrained()->nullOnDelete();
            $table->string('referred_to_hospital');
            $table->string('referred_to_doctor')->nullable();
            $table->string('referred_to_department')->nullable();
            $table->string('priority')->default('routine'); // routine, urgent, emergency
            $table->text('reason');
            $table->text('clinical_summary')->nullable();
            $table->text('diagnosis')->nullable();
            $table->date('referral_date');
            $table->string('status')->default('pending'); // pending, accepted, completed, cancelled
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
