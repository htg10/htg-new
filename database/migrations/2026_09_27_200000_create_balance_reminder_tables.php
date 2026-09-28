<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('balance_reminder_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('entry_id');
            $table->string('channel', 20);
            $table->string('status', 20)->default('sent');
            $table->text('error')->nullable();
            $table->unsignedBigInteger('sent_by')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['product_id', 'created_at']);
            $table->index('entry_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('balance_reminder_logs');
    }
};
