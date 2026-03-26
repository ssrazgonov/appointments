<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('email_appointment_reminder')->default(true);
            $table->boolean('sms_appointment_reminder')->default(false);
            $table->integer('reminder_minutes_before')->default(60);
            $table->boolean('email_new_client')->default(true);
            $table->boolean('email_payment_received')->default(true);
            $table->boolean('email_subscription_expiring')->default(true);
            $table->integer('subscription_expire_days_before')->default(3);
            $table->timestamps();
            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_settings');
    }
};
