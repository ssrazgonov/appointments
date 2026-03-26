<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('currency', 3)->default('RUB');
            $table->integer('duration_days')->default(30);
            $table->integer('max_clients')->default(100);
            $table->integer('max_appointments_per_day')->default(20);
            $table->boolean('is_active')->default(true);
            $table->json('features')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['slug', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
