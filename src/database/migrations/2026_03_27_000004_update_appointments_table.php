<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->foreignId('service_id')->nullable()->after('client_id')->constrained()->onDelete('set null');
            $table->string('client_name')->nullable()->after('reminder_sent');
            $table->string('client_phone')->nullable()->after('client_name');
            $table->string('client_email')->nullable()->after('client_phone');
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['service_id']);
            $table->dropColumn(['service_id', 'client_name', 'client_phone', 'client_email']);
        });
    }
};
