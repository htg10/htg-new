<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->id();
            $table->string('phone', 20)->index();
            $table->string('contact_name')->nullable();
            $table->enum('direction', ['in', 'out'])->default('out');
            $table->string('message_type', 30)->default('text');
            $table->text('content')->nullable();
            $table->text('media_url')->nullable();
            $table->string('media_mime', 100)->nullable();
            $table->string('wa_message_id')->nullable()->index();
            $table->string('status', 30)->default('sent');
            $table->string('context_type', 50)->nullable();
            $table->unsignedBigInteger('context_id')->nullable();
            $table->unsignedBigInteger('sent_by')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->index(['phone', 'created_at']);
            $table->index('direction');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_messages');
    }
};
