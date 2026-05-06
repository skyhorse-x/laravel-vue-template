<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('luma_referrals', function (Blueprint $table) {
            $table->index('referrer_id', 'idx_referrer_id');
            $table->index('referee_id', 'idx_referee_id');
            $table->index('status', 'idx_status');
        });

        Schema::table('luma_user_extends', function (Blueprint $table) {
            $table->index('referral_code', 'idx_referral_code');
            $table->index('user_id', 'idx_user_id');
        });

        Schema::table('luma_financial_records', function (Blueprint $table) {
            $table->index('user_id', 'idx_financial_user_id');
            $table->index('type', 'idx_financial_type');
            $table->index('created_at', 'idx_financial_created_at');
        });

        Schema::table('luma_users', function (Blueprint $table) {
            $table->index('email', 'idx_users_email');
            $table->index('phone', 'idx_users_phone');
            $table->index('status', 'idx_users_status');
        });

        Schema::table('luma_admins', function (Blueprint $table) {
            $table->index('username', 'idx_admins_username');
            $table->index('email', 'idx_admins_email');
            $table->index('role_id', 'idx_admins_role_id');
        });
    }

    public function down(): void
    {
        Schema::table('luma_referrals', function (Blueprint $table) {
            $table->dropIndex('idx_referrer_id');
            $table->dropIndex('idx_referee_id');
            $table->dropIndex('idx_status');
        });

        Schema::table('luma_user_extends', function (Blueprint $table) {
            $table->dropIndex('idx_referral_code');
            $table->dropIndex('idx_user_id');
        });

        Schema::table('luma_financial_records', function (Blueprint $table) {
            $table->dropIndex('idx_financial_user_id');
            $table->dropIndex('idx_financial_type');
            $table->dropIndex('idx_financial_created_at');
        });

        Schema::table('luma_users', function (Blueprint $table) {
            $table->dropIndex('idx_users_email');
            $table->dropIndex('idx_users_phone');
            $table->dropIndex('idx_users_status');
        });

        Schema::table('luma_admins', function (Blueprint $table) {
            $table->dropIndex('idx_admins_username');
            $table->dropIndex('idx_admins_email');
            $table->dropIndex('idx_admins_role_id');
        });
    }
};
