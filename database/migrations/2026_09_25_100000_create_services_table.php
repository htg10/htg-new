<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        $services = [
            'Local Keyword SEO',
            'Virtual Tour',
            'Google Business Profile Management',
            'Zonal Keyword SEO',
            'Google Ads',
            'Google Ads Recharge',
            'Meta Ads Management',
            'Facebook Ads Recharge',
            'Social Media Management',
            'Website Design',
            'Custom Development',
            'Website Amc',
            'Product Photography',
            'Domain',
            'Hosting',
            'QR Code',
            'Web SEO',
            'Others',
        ];

        foreach ($services as $i => $name) {
            DB::table('services')->insert([
                'name' => $name,
                'is_active' => true,
                'sort_order' => $i,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
