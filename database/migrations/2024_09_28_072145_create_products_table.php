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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_name');
            $table->foreignId('entry_id')->constrained()->onDelete('cascade');
            $table->decimal('total_amount', 8, 2)->nullable();
            $table->string('paid_amount')->nullable();
            $table->decimal('balance_amount', 8, 2)->nullable();
            $table->string('validity')->nullable();
            $table->date('expiry_date')->nullable();
            $table->boolean('expiry_mail_sent')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
