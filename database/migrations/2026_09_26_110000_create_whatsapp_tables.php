<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_settings', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number_id');
            $table->string('business_account_id')->nullable();
            $table->text('access_token');
            $table->string('api_version', 10)->default('v21.0');
            $table->string('display_phone')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('whatsapp_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('template_name');
            $table->string('language', 10)->default('en');
            $table->string('category', 50)->default('UTILITY');
            $table->json('components')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('whatsapp_logs', function (Blueprint $table) {
            $table->id();
            $table->string('to_phone', 20);
            $table->string('to_name')->nullable();
            $table->string('template_name')->nullable();
            $table->string('message_type', 30)->default('template');
            $table->text('content')->nullable();
            $table->string('wa_message_id')->nullable();
            $table->string('status', 30)->default('sent');
            $table->text('error')->nullable();
            $table->string('context_type', 50)->nullable();
            $table->unsignedBigInteger('context_id')->nullable();
            $table->unsignedBigInteger('sent_by')->nullable();
            $table->timestamps();

            $table->index(['context_type', 'context_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_logs');
        Schema::dropIfExists('whatsapp_templates');
        Schema::dropIfExists('whatsapp_settings');
    }
};
