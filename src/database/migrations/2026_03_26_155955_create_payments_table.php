<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_plan_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_subscription_id')->nullable()->constrained()->onDelete('set null');
            $table->string('transaction_id')->nullable();
            $table->string('payment_system');
            $table->string('status')->default('pending');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('RUB');
            $table->string('payment_url')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'status']);
            $table->index(['transaction_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
