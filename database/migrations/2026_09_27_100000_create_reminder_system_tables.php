<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reminder_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['before_expiry', 'on_expiry', 'after_expiry']);
            $table->unsignedSmallInteger('days')->default(0);
            $table->json('channels');
            $table->string('wa_template_name')->nullable();
            $table->string('sms_template_id')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('reminder_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('entry_id');
            $table->unsignedBigInteger('rule_id')->nullable();
            $table->string('channel', 20);
            $table->string('status', 20)->default('sent');
            $table->text('error')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['product_id', 'rule_id']);
            $table->index('entry_id');
            $table->index('created_at');
        });

        Schema::table('entries', function (Blueprint $table) {
            $table->boolean('reminders_enabled')->default(true)->after('state');
        });

        DB::table('reminder_rules')->insert([
            ['name' => '30 Days Before', 'type' => 'before_expiry', 'days' => 30, 'channels' => '["email"]', 'wa_template_name' => null, 'sms_template_id' => null, 'sort_order' => 1, 'is_active' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '15 Days Before', 'type' => 'before_expiry', 'days' => 15, 'channels' => '["sms","email"]', 'wa_template_name' => null, 'sms_template_id' => null, 'sort_order' => 2, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '7 Days Before', 'type' => 'before_expiry', 'days' => 7, 'channels' => '["sms","email","whatsapp"]', 'wa_template_name' => null, 'sms_template_id' => null, 'sort_order' => 3, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '3 Days Before', 'type' => 'before_expiry', 'days' => 3, 'channels' => '["sms","whatsapp"]', 'wa_template_name' => null, 'sms_template_id' => null, 'sort_order' => 4, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '1 Day Before', 'type' => 'before_expiry', 'days' => 1, 'channels' => '["sms","email","whatsapp"]', 'wa_template_name' => null, 'sms_template_id' => null, 'sort_order' => 5, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'On Expiry Day', 'type' => 'on_expiry', 'days' => 0, 'channels' => '["sms","email","whatsapp"]', 'wa_template_name' => null, 'sms_template_id' => null, 'sort_order' => 6, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('reminder_logs');
        Schema::dropIfExists('reminder_rules');
        Schema::table('entries', function (Blueprint $table) {
            $table->dropColumn('reminders_enabled');
        });
    }
};
