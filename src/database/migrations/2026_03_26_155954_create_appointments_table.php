<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->datetime('start_time');
            $table->datetime('end_time');
            $table->string('status')->default('scheduled');
            $table->decimal('price', 10, 2)->default(0);
            $table->boolean('is_paid')->default(false);
            $table->text('notes')->nullable();
            $table->boolean('reminder_sent')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['user_id', 'start_time']);
            $table->index(['user_id', 'status']);
            $table->index(['client_id', 'start_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
