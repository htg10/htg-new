<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('telecallers', function (Blueprint $table) {
            $table->id();
            $table->string('business')->nullable();
            $table->string('name')->nullable();
            $table->string('address')->nullable();
            $table->string('mobile')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->dateTime('meeting_datetime')->nullable();
            $table->string('interest')->nullable();
            $table->string('remark')->nullable();
            $table->string('status')->default('NEW');
            $table->string('deal_status')->default('pending');
            $table->date('follow_up_date')->nullable();
            $table->string('follow_up_remark')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('telecallers');
    }
};
