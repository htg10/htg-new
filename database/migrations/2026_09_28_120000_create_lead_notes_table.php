<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('telecaller_id');
            $table->unsignedBigInteger('user_id');
            $table->enum('type', ['note', 'call', 'meeting', 'email', 'whatsapp', 'status_change'])->default('note');
            $table->text('content');
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->foreign('telecaller_id')->references('id')->on('telecallers')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['telecaller_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_notes');
    }
};
