<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->index('status');
            $table->index('visit_date');
        });

        Schema::table('admissions', function (Blueprint $table) {
            $table->index('status');
            $table->index('admitted_at');
            $table->index('bed_id');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->index('status');
            $table->index('payment_method');
        });

        Schema::table('prescriptions', function (Blueprint $table) {
            $table->index('status');
            $table->index('prescribed_at');
        });

        Schema::table('beds', function (Blueprint $table) {
            $table->index('status');
        });

        Schema::table('medications', function (Blueprint $table) {
            $table->index('is_active');
            $table->index('expiry_date');
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->index('type');
            $table->index('created_at');
        });

        Schema::table('referrals', function (Blueprint $table) {
            $table->index('status');
            $table->index('priority');
            $table->index('referral_date');
        });
    }

    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['visit_date']);
        });

        Schema::table('admissions', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['admitted_at']);
            $table->dropIndex(['bed_id']);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['payment_method']);
        });

        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['prescribed_at']);
        });

        Schema::table('beds', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('medications', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
            $table->dropIndex(['expiry_date']);
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropIndex(['type']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('referrals', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['priority']);
            $table->dropIndex(['referral_date']);
        });
    }
};
